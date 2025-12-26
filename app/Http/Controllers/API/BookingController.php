<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\DjProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'djProfile.user', 'venue', 'playlist']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by DJ
        if ($request->has('dj_profile_id')) {
            $query->where('dj_profile_id', $request->dj_profile_id);
        }

        // Filter by venue
        if ($request->has('venue_id')) {
            $query->where('venue_id', $request->venue_id);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('event_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('event_date', '<=', $request->end_date);
        }

        // Sort options
        $query->orderBy('event_date', $request->sort_dir ?? 'desc');

        $bookings = $query->paginate(15);
        return BookingResource::collection($bookings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dj_profile_id' => 'required|exists:dj_profiles,id',
            'venue_id' => 'nullable|exists:venues,id',
            'event_type' => 'required|string|max:255',
            'event_date' => 'required|date|after:now',
            'duration_hours' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
            'playlist_id' => 'nullable|exists:playlists,id',
            'is_recurring' => 'sometimes|boolean',
            'recurring_frequency' => 'nullable|string|max:255',
        ]);

        // Set user ID from authenticated user
        $validated['user_id'] = Auth::id();

        // Calculate total price based on DJ's hourly rate and duration
        $djProfile = DjProfile::findOrFail($validated['dj_profile_id']);
        $validated['total_price'] = $djProfile->hourly_rate * $validated['duration_hours'];

        $booking = Booking::create($validated);

        return new BookingResource($booking->load(['user', 'djProfile.user', 'venue', 'playlist']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        return new BookingResource($booking->load(['user', 'djProfile.user', 'venue', 'playlist.songs', 'review']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'venue_id' => 'sometimes|nullable|exists:venues,id',
            'event_type' => 'sometimes|string|max:255',
            'event_date' => 'sometimes|date|after:now',
            'duration_hours' => 'sometimes|integer|min:1',
            'special_requests' => 'nullable|string',
            'status' => 'sometimes|in:pending,confirmed,completed,cancelled',
            'playlist_id' => 'nullable|exists:playlists,id',
            'is_recurring' => 'sometimes|boolean',
            'recurring_frequency' => 'nullable|string|max:255',
        ]);

        // Recalculate total price if duration has changed
        if (isset($validated['duration_hours']) && $booking->duration_hours != $validated['duration_hours']) {
            $validated['total_price'] = $booking->djProfile->hourly_rate * $validated['duration_hours'];
        }

        $booking->update($validated);

        return new BookingResource($booking->load(['user', 'djProfile.user', 'venue', 'playlist']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        $booking->delete();
        return response()->json(null, 204);
    }

    /**
     * Get bookings for the currently authenticated user
     */
    public function myBookings(Request $request)
    {
        $user = Auth::user();
        $query = Booking::where('user_id', $user->id)
            ->with(['djProfile.user', 'venue', 'playlist']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderBy('event_date', 'desc')->paginate(10);
        return BookingResource::collection($bookings);
    }

    /**
     * Get bookings for the currently authenticated DJ
     */
    public function myDjBookings(Request $request)
    {
        $user = Auth::user();
        if (!$user->isDj() || !$user->djProfile) {
            return response()->json(['message' => 'DJ profile not found'], 404);
        }

        $query = Booking::where('dj_profile_id', $user->djProfile->id)
            ->with(['user', 'venue', 'playlist']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderBy('event_date', 'desc')->paginate(10);
        return BookingResource::collection($bookings);
    }
}
