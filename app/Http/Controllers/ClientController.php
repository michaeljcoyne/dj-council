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



    /**
     * Store booking request
     */
    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'dj_profile_id' => 'nullable|exists:dj_profiles,id',
            'playlist_id' => 'nullable|exists:playlists,id',
            'event_type' => 'required|string|in:wedding,birthday,corporate,club,festival,private,other',
            'event_date' => 'required|date|after:today',
            'event_time' => 'required|date_format:H:i',
            'duration_hours' => 'required|integer|min:1|max:24',
            'expected_guests' => 'required|integer|min:1',
            'venue_name' => 'required|string|max:255',
            'venue_address' => 'required|string|max:255',
            'venue_city' => 'required|string|max:100',
            'venue_postcode' => 'required|string|max:20',
            'special_requests' => 'nullable|string|max:2000',
        ]);

        // Create or find venue
        $venue = Venue::firstOrCreate([
            'name' => $validated['venue_name'],
            'address' => $validated['venue_address'],
            'city' => $validated['venue_city'],
            'postcode' => $validated['venue_postcode'],
        ]);

        // If booking specific DJ, validate minimum hours and calculate pricing
        $totalPrice = null;
        if ($validated['dj_profile_id']) {
            $dj = DjProfile::findOrFail($validated['dj_profile_id']);

            // Validate minimum booking hours
            if ($validated['duration_hours'] < $dj->minimum_booking_hours) {
                return back()->withErrors([
                    'duration_hours' => "This DJ requires a minimum of {$dj->minimum_booking_hours} hours."
                ]);
            }

            // Calculate total price (hourly rate * hours * 1.10 for 10% fee)
            $subtotal = $dj->hourly_rate * $validated['duration_hours'];
            $totalPrice = $subtotal * 1.10; // Include 10% booking fee
        }

        // Combine date and time into datetime
        $eventDateTime = \Carbon\Carbon::parse($validated['event_date'] . ' ' . $validated['event_time']);

        // Create booking
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'dj_profile_id' => $validated['dj_profile_id'],
            'venue_id' => $venue->id,
            'event_type' => $validated['event_type'],
            'event_date' => $eventDateTime,
            'duration_hours' => $validated['duration_hours'],
            'total_price' => $totalPrice,
            'special_requests' => $validated['special_requests'],
            'playlist_id' => $validated['playlist_id'],
            'status' => 'pending',
        ]);

        // TODO: Send notification to DJ (or all DJs if generic request)
        // TODO: Send confirmation email to client

        if ($validated['dj_profile_id']) {
            return redirect()->route('client.bookings.show', $booking)
                ->with('success', 'Booking request sent successfully!');
        } else {
            return redirect()->route('client.bookings.index')
                ->with('success', 'Your request has been sent to available DJs!');
        }
    }

    /**
     * Show booking details (rename from showBooking to avoid conflict)
     */
    public function show(Booking $booking)
    {
        // Ensure user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['djProfile.user', 'venue', 'playlist']);

        return Inertia::render('Client/BookingDetails', [
            'booking' => $booking,
        ]);
    }




    /**
     * Cancel booking
     */
    public function cancelBooking(Booking $booking)
    {
        // Ensure user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        // Can only cancel pending or confirmed bookings
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->withErrors([
                'error' => 'This booking cannot be cancelled.'
            ]);
        }

        $booking->update([
            'status' => 'cancelled',
        ]);

        // TODO: Notify DJ of cancellation
        // TODO: Process refund if payment was made

        return back()->with('success', 'Booking cancelled successfully.');
    }
}
