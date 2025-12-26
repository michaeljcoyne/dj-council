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

       /* if (!$djProfile) {
            return Inertia::render('DJ/CreateProfile', [
                'genres' => Genre::orderBy('name')->get(),
            ]);
        }*/

        // Dummy data for development
        $djProfile = $djProfile ?? new \App\Models\DjProfile([
            'stage_name' => 'Dev DJ',
            'specialty' => 'Testing',
            'hourly_rate' => 100,
            'minimum_booking_hours' => 2,
        ]);


        $upcomingBookings = $djProfile->bookings()
            ->where('event_date', '>=', now())
            ->where('status', 'confirmed')
            ->with(['user', 'venue', 'playlist.songs'])
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
            'profile' => $djProfile,
            //'profile' => $djProfile->load('genres'),
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
}
