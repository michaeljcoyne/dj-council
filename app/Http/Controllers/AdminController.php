<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\DjProfile;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard
     */
    public function dashboard()
    {
        // Platform statistics
        $stats = [
            'total_users' => User::count(),
            'total_djs' => DjProfile::count(),
            'total_venues' => Venue::count(),
            'total_bookings' => Booking::count(),
            'completed_bookings' => Booking::where('status', 'completed')->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'pending_reviews' => Review::where('is_approved', false)->count(),
        ];

        // Recent activity
        $recentBookings = Booking::with(['user', 'djProfile.user', 'venue'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $newUsers = User::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $pendingReviews = Review::where('is_approved', false)
            ->with(['user', 'djProfile.user'])
            ->take(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentBookings' => $recentBookings,
            'newUsers' => $newUsers,
            'pendingReviews' => $pendingReviews,
        ]);
    }

    /**
     * Show the user management page
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Filter by user type
        if ($request->has('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        // Search by name or email
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        // Sort options
        if ($request->has('sort_by')) {
            $query->orderBy($request->sort_by, $request->sort_dir ?? 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $users = $query->paginate(15);

        return Inertia::render('Admin/Users', [
            'users' => $users,
            'filters' => $request->only(['user_type', 'search', 'sort_by', 'sort_dir']),
        ]);
    }

    /**
     * Show the DJ management page
     */
    public function djs(Request $request)
    {
        $query = DjProfile::with(['user', 'genres']);

        // Filter by featured status
        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->is_featured);
        }

        // Filter by availability
        if ($request->has('is_available')) {
            $query->where('is_available', $request->is_available);
        }

        // Search by stage name
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('stage_name', 'like', "%{$searchTerm}%")
                  ->orWhereHas('user', function($q) use ($searchTerm) {
                      $q->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Sort options
        if ($request->has('sort_by')) {
            $query->orderBy($request->sort_by, $request->sort_dir ?? 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $djs = $query->paginate(15);

        return Inertia::render('Admin/Djs', [
            'djs' => $djs,
            'filters' => $request->only(['is_featured', 'is_available', 'search', 'sort_by', 'sort_dir']),
        ]);
    }

    /**
     * Show the venue management page
     */
    public function venues(Request $request)
    {
        $query = Venue::with('user');

        // Filter by verification status
        if ($request->has('is_verified')) {
            $query->where('is_verified', $request->is_verified);
        }

        // Search by name or location
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('city', 'like', "%{$searchTerm}%")
                  ->orWhere('address', 'like', "%{$searchTerm}%");
            });
        }

        // Sort options
        if ($request->has('sort_by')) {
            $query->orderBy($request->sort_by, $request->sort_dir ?? 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $venues = $query->paginate(15);

        return Inertia::render('Admin/Venues', [
            'venues' => $venues,
            'filters' => $request->only(['is_verified', 'search', 'sort_by', 'sort_dir']),
        ]);
    }

    /**
     * Show the booking management page
     */
    public function bookings(Request $request)
    {
        $query = Booking::with(['user', 'djProfile.user', 'venue']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('event_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('event_date', '<=', $request->end_date);
        }

        // Search by client or DJ name
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('user', function($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%");
                })->orWhereHas('djProfile.user', function($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%");
                })->orWhereHas('djProfile', function($q) use ($searchTerm) {
                    $q->where('stage_name', 'like', "%{$searchTerm}%");
                });
            });
        }

        // Sort options
        if ($request->has('sort_by')) {
            $query->orderBy($request->sort_by, $request->sort_dir ?? 'desc');
        } else {
            $query->orderBy('event_date', 'desc');
        }

        $bookings = $query->paginate(15);

        return Inertia::render('Admin/Bookings', [
            'bookings' => $bookings,
            'filters' => $request->only(['status', 'start_date', 'end_date', 'search', 'sort_by', 'sort_dir']),
        ]);
    }

    /**
     * Show the review management page
     */
    public function reviews(Request $request)
    {
        $query = Review::with(['user', 'djProfile.user', 'booking']);

        // Filter by approval status
        if ($request->has('is_approved')) {
            $query->where('is_approved', $request->is_approved);
        }

        // Filter by rating
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        // Search reviews
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('comment', 'like', "%{$searchTerm}%")
                  ->orWhereHas('user', function($q) use ($searchTerm) {
                      $q->where('name', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('djProfile.user', function($q) use ($searchTerm) {
                      $q->where('name', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('djProfile', function($q) use ($searchTerm) {
                      $q->where('stage_name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Sort options
        if ($request->has('sort_by')) {
            $query->orderBy($request->sort_by, $request->sort_dir ?? 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $reviews = $query->paginate(15);

        return Inertia::render('Admin/Reviews', [
            'reviews' => $reviews,
            'filters' => $request->only(['is_approved', 'rating', 'search', 'sort_by', 'sort_dir']),
        ]);
    }

    /**
     * Show the genre management page
     */
    public function genres()
    {
        $genres = Genre::withCount('djProfiles')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Genres', [
            'genres' => $genres,
        ]);
    }

    /**
     * Show the analytics page
     */
    public function analytics()
    {
        // Monthly bookings data for the past year
        $monthlyBookings = [];
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $monthlyBookings[] = [
                'month' => $date->format('M Y'),
                'count' => Booking::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'revenue' => Booking::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('status', 'completed')
                    ->sum('total_price'),
            ];
        }
        // Reverse to show chronological order
        $monthlyBookings = array_reverse($monthlyBookings);

        // User growth data
        $userGrowth = [];
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $userGrowth[] = [
                'month' => $date->format('M Y'),
                'total' => User::whereDate('created_at', '<=', $date->endOfMonth())->count(),
                'new' => User::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }
        // Reverse to show chronological order
        $userGrowth = array_reverse($userGrowth);

        // Top DJs by bookings
        $topDjs = DjProfile::withCount('bookings')
            ->with('user')
            ->orderBy('bookings_count', 'desc')
            ->take(10)
            ->get();

        // Top venues by bookings
        $topVenues = Venue::withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->take(10)
            ->get();

        // Genre distribution
        $genreDistribution = Genre::withCount('djProfiles')
            ->orderBy('dj_profiles_count', 'desc')
            ->get();

        return Inertia::render('Admin/Analytics', [
            'monthlyBookings' => $monthlyBookings,
            'userGrowth' => $userGrowth,
            'topDjs' => $topDjs,
            'topVenues' => $topVenues,
            'genreDistribution' => $genreDistribution,
        ]);
    }
}
