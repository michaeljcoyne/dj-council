<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ClientPlaylist;
use App\Models\DjProfile;
use App\Models\Genre;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class ClientController extends Controller
{
    /**
     * Show the client dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();

        $upcomingBookings = $user->bookings()
            ->where('event_date', '>=', now())
            ->where('status', 'confirmed')
            ->with(['djProfile.user', 'venue', 'playlist'])
            ->orderBy('event_date')
            ->take(3)
            ->get()
            ->map(function($booking) {
                return [
                    'id' => $booking->id,
                    'event_type' => $booking->event_type ?? 'Event',
                    'event_date' => $booking->event_date->format('M d, Y'),
                    'start_time' => $booking->start_time ?? 'TBD',
                    'end_time' => $booking->end_time ?? 'TBD',
                    'dj_name' => $booking->djProfile->user->name ?? 'TBD',
                    'venue_name' => $booking->venue->name ?? 'TBD',
                    'status' => ucfirst($booking->status),
                ];
            });

        $recentPlaylists = $user->clientPlaylists()
            ->withCount('songs')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function($playlist) {
                // Calculate total duration
                $totalSeconds = \DB::table('client_playlist_songs')
                    ->where('client_playlist_id', $playlist->id)
                    ->sum('duration');

                $hours = floor($totalSeconds / 3600);
                $mins = floor(($totalSeconds % 3600) / 60);

                return [
                    'id' => $playlist->id,
                    'name' => $playlist->name,
                    'song_count' => $playlist->songs_count,
                    'duration' => $hours > 0 ? "{$hours}h {$mins}min" : "{$mins} min",
                ];
            });

        $featuredDjs = DjProfile::where('is_featured', true)
            ->with(['user', 'genres'])
            ->take(4)
            ->get();

        // Calculate stats for dashboard cards
        $stats = [
            'upcomingEvents' => $user->bookings()
                ->where('event_date', '>=', now())
                ->where('status', 'confirmed')
                ->count(),
            'playlists' => $user->clientPlaylists()->count(),
            'savedDJs' => 0, // TODO: Implement when favorites system exists
            'completedEvents' => $user->bookings()
                ->where('status', 'completed')
                ->count(),
        ];

        // DEBUG: Log the counts
        \Log::info('Dashboard Stats', [
            'playlists_count' => $stats['playlists'],
            'playlists_table' => $user->clientPlaylists()->getModel()->getTable(),
        ]);

        return Inertia::render('Client/Dashboard', [
            'stats' => $stats,
            'upcomingBookings' => $upcomingBookings,
            'recentPlaylists' => $recentPlaylists,
            'featuredDjs' => $featuredDjs,
        ]);
    }

    /**
     * Show a single DJ profile (public - for everyone)
     */
    public function showDj($id)
    {
        $djProfile = DjProfile::with(['user', 'genres', 'reviews' => function($query) {
            $query->where('is_approved', true)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit(10);
        }])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->findOrFail($id);

        // Always use public profile for everyone
        return Inertia::render('Public/DjProfile', [
            'dj' => $djProfile,
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
        ]);
    }

    /**
     * Show the booking form for a DJ
     */
    public function bookDj($id)
    {
        $djProfile = DjProfile::with(['user', 'genres'])->findOrFail($id);
        $userPlaylists = Auth::user()->playlists()->with('songs')->get();
        $userVenues = Venue::where('user_id', Auth::id())->get();

        return Inertia::render('Client/BookDj', [
            'dj' => $djProfile,
            'playlists' => $userPlaylists,
            'venues' => $userVenues,
        ]);
    }

    /**
     * Show the client's bookings
     */
    public function bookings(Request $request)
    {
        $status = $request->query('status', 'upcoming');

        $query = Auth::user()->bookings()
            ->with(['djProfile.user', 'venue', 'playlist']);

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

        return Inertia::render('Client/Bookings', [
            'bookings' => $bookings,
            'activeStatus' => $status,
        ]);
    }

    /**
     * Show a specific booking
     */
    public function showBooking($id)
    {
        $booking = Auth::user()->bookings()
            ->with(['djProfile.user', 'venue', 'playlist.songs', 'review'])
            ->findOrFail($id);

        return Inertia::render('Client/BookingDetail', [
            'booking' => $booking,
        ]);
    }

    /**
     * Show the client's playlists
     */
    public function playlists()
    {
        $playlists = Auth::user()->clientPlaylists()
            ->withCount('songs')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return Inertia::render('Client/Playlists', [
            'playlists' => $playlists,
        ]);
    }

    /**
     * Show a specific playlist
     */
    public function showPlaylist($id)
    {
        $playlist = Auth::user()->clientPlaylists()
            ->with('songs')
            ->findOrFail($id);

        return Inertia::render('Client/PlaylistDetail', [
            'playlist' => $playlist,
        ]);
    }

    /**
     * Show the playlist editor
     */
    public function editPlaylist($id = null)
    {
        $playlist = null;
        if ($id) {
            $playlist = Auth::user()->clientPlaylists()
                ->with('songs')
                ->findOrFail($id);
        }

        return Inertia::render('Client/PlaylistEditor', [
            'playlist' => $playlist,
            'genres' => Genre::orderBy('name')->get(),
        ]);
    }

    /**
     * Show the client's venues
     */
    public function venues()
    {
        $venues = Venue::where('user_id', Auth::id())
            ->orderBy('name')
            ->paginate(10);

        return Inertia::render('Client/Venues', [
            'venues' => $venues,
        ]);
    }

    /**
     * Show the venue form
     */
    public function editVenue($id = null)
    {
        $venue = null;
        if ($id) {
            $venue = Venue::where('user_id', Auth::id())
                ->findOrFail($id);
        }

        return Inertia::render('Client/VenueForm', [
            'venue' => $venue,
        ]);
    }

    /**
     * Show settings page
     */
    public function settings()
    {
        $user = Auth::user();

        return Inertia::render('Client/Settings', [
            'user' => $user,
            'spotifyConnected' => !empty($user->spotify_refresh_token),
        ]);
    }

    /**
     * Browse DJs (public or authenticated client)
     */
    public function browseDjs(Request $request)
    {
        $query = DjProfile::with(['user', 'genres', 'reviews'])
            ->where('is_available', true);

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('stage_name', 'like', '%'.$request->search.'%')
                    ->orWhere('specialty', 'like', '%'.$request->search.'%')
                    ->orWhere('location', 'like', '%'.$request->search.'%');
            });
        }

        // Genre filter
        if ($request->has('genre') && $request->genre) {
            $query->whereHas('genres', function($q) use ($request) {
                $q->where('genres.id', $request->genre);
            });
        }

        // Location filter
        if ($request->has('location') && $request->location) {
            $query->where('location', 'like', '%'.$request->location.'%');
        }

        // Rate filters
        if ($request->has('min_rate') && $request->min_rate) {
            $query->where('hourly_rate', '>=', $request->min_rate);
        }
        if ($request->has('max_rate') && $request->max_rate) {
            $query->where('hourly_rate', '<=', $request->max_rate);
        }

        // Sort: Featured first, then by rating
        $djs = $query->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->orderBy('is_featured', 'desc')
            ->orderByDesc('reviews_avg_rating')
            ->paginate(12);

        return Inertia::render('Public/DjListing', [
            'djs' => $djs,
            'genres' => Genre::orderBy('name')->get(),
            'filters' => $request->only(['search', 'genre', 'location', 'min_rate', 'max_rate']),
        ]);
    }
}
