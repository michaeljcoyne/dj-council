<?php

namespace App\Http\Controllers;

use App\Models\DjProfile;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DjController extends Controller
{
    /**
     * Show the DJ dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $djProfile = $user->djProfile;

        // If user doesn't have a DJ profile, redirect to create one
        if (!$djProfile) {
            return Inertia::render('DJ/CreateProfile', [
                'genres' => Genre::orderBy('name')->get(),
            ]);
        }

        $upcomingBookings = $djProfile->bookings()
            ->where('event_date', '>=', now())
            ->where('status', 'confirmed')
            ->with(['user', 'venue', 'playlist'])
            ->orderBy('event_date')
            ->take(5)
            ->get();

        $pendingBookings = $djProfile->bookings()
            ->where('status', 'pending')
            ->with(['user', 'venue'])
            ->orderBy('event_date')
            ->get();

        $stats = [
            'total_bookings' => $djProfile->bookings()->count(),
            'upcoming_bookings' => $djProfile->bookings()
                ->where('event_date', '>=', now())
                ->where('status', 'confirmed')
                ->count(),
            'total_earnings' => $djProfile->bookings()
                ->where('status', 'completed')
                ->sum('total_price'),
            'average_rating' => $djProfile->averageRating(),
            'review_count' => $djProfile->reviews()->count(),
        ];

        return Inertia::render('DJ/Dashboard', [
            'profile' => $djProfile->load('genres'),
            'upcomingBookings' => $upcomingBookings,
            'pendingBookings' => $pendingBookings,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the DJ profile edit form
     */
    public function editProfile()
    {
        $user = Auth::user();
        $djProfile = $user->djProfile;

        if (!$djProfile) {
            return redirect()->route('dj.dashboard');
        }

        return Inertia::render('DJ/EditProfile', [
            'profile' => $djProfile->load('genres'),
            'genres' => Genre::orderBy('name')->get(),
        ]);
    }

    /**
     * Show the DJ bookings page
     */
    public function bookings(Request $request)
    {
        $user = Auth::user();
        $djProfile = $user->djProfile;

        if (!$djProfile) {
            return redirect()->route('dj.dashboard');
        }

        $status = $request->query('status', 'upcoming');

        $query = $djProfile->bookings()
            ->with(['user', 'venue', 'playlist']);

        switch ($status) {
            case 'upcoming':
                $query->where('event_date', '>=', now())
                    ->where('status', 'confirmed')
                    ->orderBy('event_date');
                break;
            case 'pending':
                $query->where('status', 'pending')
                    ->orderBy('event_date');
                break;
            case 'past':
                $query->where(function ($q) {
                    $q->where('event_date', '<', now())
                      ->orWhere('status', 'completed');
                })
                ->orderBy('event_date', 'desc');
                break;
            case 'cancelled':
                $query->where('status', 'cancelled')
                    ->orderBy('event_date', 'desc');
                break;
            default:
                $query->orderBy('event_date', 'desc');
                break;
        }

        $bookings = $query->paginate(10);

        return Inertia::render('DJ/Bookings', [
            'bookings' => $bookings,
            'activeStatus' => $status,
        ]);
    }

    /**
     * Show a specific booking
     */
    public function showBooking($id)
    {
        $user = Auth::user();
        $djProfile = $user->djProfile;

        if (!$djProfile) {
            return redirect()->route('dj.dashboard');
        }

        $booking = $djProfile->bookings()
            ->with(['user', 'venue', 'playlist.songs'])
            ->findOrFail($id);

        return Inertia::render('DJ/BookingDetail', [
            'booking' => $booking,
        ]);
    }

    /**
     * Show the DJ reviews page
     */
    public function reviews()
    {
        $user = Auth::user();
        $djProfile = $user->djProfile;

        if (!$djProfile) {
            return redirect()->route('dj.dashboard');
        }

        $reviews = $djProfile->reviews()
            ->with('user')
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $ratingStats = [
            'average' => $djProfile->averageRating(),
            'total' => $djProfile->reviews()->count(),
            'five_star' => $djProfile->reviews()->where('rating', 5)->count(),
            'four_star' => $djProfile->reviews()->where('rating', 4)->count(),
            'three_star' => $djProfile->reviews()->where('rating', 3)->count(),
            'two_star' => $djProfile->reviews()->where('rating', 2)->count(),
            'one_star' => $djProfile->reviews()->where('rating', 1)->count(),
        ];

        return Inertia::render('DJ/Reviews', [
            'reviews' => $reviews,
            'ratingStats' => $ratingStats,
        ]);
    }

    /**
     * Accept a booking request
     */
    public function acceptBooking($id)
    {
        $user = Auth::user();
        $djProfile = $user->djProfile;

        if (!$djProfile) {
            abort(403, 'DJ profile not found');
        }

        $booking = $djProfile->bookings()
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();

        $booking->update([
            'status' => 'confirmed',
            'responded_at' => now(),
        ]);

        // TODO: Send notification to client
        // TODO: Send confirmation email

        return redirect()->route('dj.bookings.show', $booking)
            ->with('success', 'Booking accepted successfully!');
    }

    /**
     * Decline a booking request
     */
    public function declineBooking(Request $request, $id)
    {
        $user = Auth::user();
        $djProfile = $user->djProfile;

        if (!$djProfile) {
            abort(403, 'DJ profile not found');
        }

        $booking = $djProfile->bookings()
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();

        $booking->update([
            'status' => 'declined',
            'responded_at' => now(),
            'decline_reason' => $request->input('reason', 'No reason provided'),
        ]);

        // TODO: Send notification to client
        // TODO: Send decline email with reason

        return redirect()->route('dj.bookings.index')
            ->with('success', 'Booking declined.');
    }

    /**
     * Store a new DJ profile
     */
    public function storeProfile(Request $request)
    {
        $user = Auth::user();

        // Check if user already has a DJ profile
        if ($user->djProfile) {
            return redirect()->route('dj.dashboard');
        }

        $validated = $request->validate([
            'stage_name' => 'required|string|max:255',
            'specialty' => 'required|string|max:255',
            'bio' => 'required|string|max:2000',
            'location' => 'required|string|max:255',
            'hourly_rate' => 'required|numeric|min:0',
            'minimum_booking_hours' => 'required|integer|min:1',
            'genre_ids' => 'array',
            'genre_ids.*' => 'exists:genres,id',
        ]);

        // Create DJ profile
        $djProfile = DjProfile::create([
            'user_id' => $user->id,
            'stage_name' => $validated['stage_name'],
            'specialty' => $validated['specialty'],
            'bio' => $validated['bio'],
            'location' => $validated['location'],
            'hourly_rate' => $validated['hourly_rate'],
            'minimum_booking_hours' => $validated['minimum_booking_hours'],
            'is_available' => true,
        ]);

        // Attach genres if provided
        if (!empty($validated['genre_ids'])) {
            $djProfile->genres()->attach($validated['genre_ids']);
        }

        return redirect()->route('dj.dashboard')
            ->with('success', 'DJ profile created successfully!');
    }
}
