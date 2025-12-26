<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Booking;
use App\Models\DjProfile;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Review::with(['user', 'djProfile', 'booking']);

        // Filter by DJ profile
        if ($request->has('dj_profile_id')) {
            $query->where('dj_profile_id', $request->dj_profile_id);
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by rating
        if ($request->has('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        // Filter by approval status
        if ($request->has('is_approved')) {
            $query->where('is_approved', $request->is_approved);
        } else {
            $query->where('is_approved', true);
        }

        // Sort options
        $query->orderBy('created_at', 'desc');

        $reviews = $query->paginate(15);
        return ReviewResource::collection($reviews);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dj_profile_id' => 'required|exists:dj_profiles,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        // Set user ID from authenticated user
        $validated['user_id'] = Auth::id();

        // Verify the booking belongs to the user (if provided)
        if (isset($validated['booking_id'])) {
            $booking = Booking::findOrFail($validated['booking_id']);
            if ($booking->user_id !== Auth::id()) {
                return response()->json(['message' => 'You can only review your own bookings'], 403);
            }

            // Check if booking has already been reviewed
            $existingReview = Review::where('booking_id', $booking->id)->first();
            if ($existingReview) {
                return response()->json(['message' => 'This booking has already been reviewed'], 422);
            }

            // Ensure the DJ profile in the booking matches the one being reviewed
            if ($booking->dj_profile_id !== $validated['dj_profile_id']) {
                return response()->json(['message' => 'The DJ profile does not match the booking'], 422);
            }
        }

        $review = Review::create($validated);

        return new ReviewResource($review->load(['user', 'djProfile', 'booking']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        return new ReviewResource($review->load(['user', 'djProfile', 'booking']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        // Authorization check
        if ($review->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'is_approved' => 'sometimes|boolean',
        ]);

        // Only admins can update approval status
        if (isset($validated['is_approved']) && !Auth::user()->isAdmin()) {
            unset($validated['is_approved']);
        }

        $review->update($validated);

        return new ReviewResource($review->load(['user', 'djProfile', 'booking']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        // Authorization check
        if ($review->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $review->delete();
        return response()->json(null, 204);
    }

    /**
     * Get reviews for a specific DJ
     */
    public function forDj(DjProfile $djProfile)
    {
        $reviews = Review::where('dj_profile_id', $djProfile->id)
            ->where('is_approved', true)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return ReviewResource::collection($reviews);
    }

    /**
     * Get reviews by the authenticated user
     */
    public function myReviews()
    {
        $reviews = Review::where('user_id', Auth::id())
            ->with(['djProfile', 'booking'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return ReviewResource::collection($reviews);
    }
}
