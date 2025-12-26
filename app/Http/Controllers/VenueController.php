<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\DjProfile;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class VenueController extends Controller
{
    /**
     * Show the venue dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $venues = $user->venues;

        if ($venues->isEmpty()) {
            return Inertia::render('Venue/CreateVenue');
        }

        $upcomingEvents = Booking::whereIn('venue_id', $venues->pluck('id'))
            ->where('event_date', '>=', now())
            ->where('status', 'confirmed')
            ->with(['djProfile.user', 'user', 'playlist'])
            ->orderBy('event_date')
            ->take(5)
            ->get();

        $venueStats = $venues->map(function ($venue) {
            $totalEvents = Booking::where('venue_id', $venue->id)->count();
            $upcomingEvents = Booking::where('venue_id', $venue->id)
                ->where('event_date', '>=', now())
                ->where('status', 'confirmed')
                ->count();
            
            return [
                'id' => $venue->id,
                'name' => $venue->name,
                'total_events' => $totalEvents,
                'upcoming_events' => $upcomingEvents,
            ];
        });

        return Inertia::render('Venue/Dashboard', [
            'venues' => $venues,
            'upcomingEvents' => $upcomingEvents,
            'venueStats' => $venueStats,
        ]);
    }

    /**
     * Show the venue's calendar
     */
    public function calendar(Request $request)
    {
        $user = Auth::user();
        $venues = $user->venues;
        
        if ($venues->isEmpty()) {
            return redirect()->route('venue.dashboard');
        }

        $selectedVenueId = $request->query('venue_id', $venues->first()->id);
        $selectedMonth = $request->query('month', now()->format('Y-m'));

        $startDate = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        $endDate = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->endOfMonth();

        $events = Booking::where('venue_id', $selectedVenueId)
            ->whereBetween('event_date', [$startDate, $endDate])
            ->with(['djProfile.user', 'user'])
            ->get();

        return Inertia::render('Venue/Calendar', [
            'venues' => $venues,
            'selectedVenueId' => (int)$selectedVenueId,
            'selectedMonth' => $selectedMonth,
            'events' => $events,
        ]);
    }

    /**
     * Show the venue's bookings
     */
    public function bookings(Request $request)
    {
        $user = Auth::user();
        $venues = $user->venues;
        
        if ($venues->isEmpty()) {
            return redirect()->route('venue.dashboard');
        }

        $venueIds = $venues->pluck('id')->toArray();
        $status = $request->query('status', 'upcoming');

        $query = Booking::whereIn('venue_id', $venueIds)
            ->with(['djProfile.user', 'user', 'venue', 'playlist']);

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

        return Inertia::render('Venue/Bookings', [
            'bookings' => $bookings,
            'venues' => $venues,
            'activeStatus' => $status,
        ]);
    }

    /**
     * Show the venue's profile and settings
     */
    public function manageVenues()
    {
        $user = Auth::user();
        $venues = $user->venues;

        return Inertia::render('Venue/ManageVenues', [
            'venues' => $venues,
        ]);
    }

    /**
     * Show the venue form
     */
    public function editVenue($id = null)
    {
        $user = Auth::user();
        $venue = null;

        if ($id) {
            $venue = Venue::where('user_id', $user->id)
                ->findOrFail($id);
        }

        return Inertia::render('Venue/VenueForm', [
            'venue' => $venue,
        ]);
    }

    /**
     * Show the DJ request form
     */
    public function requestDj()
    {
        $user = Auth::user();
        $venues = $user->venues;
        
        if ($venues->isEmpty()) {
            return redirect()->route('venue.dashboard');
        }

        $featuredDjs = DjProfile::where('is_featured', true)
            ->with(['user', 'genres'])
            ->take(6)
            ->get();

        return Inertia::render('Venue/RequestDj', [
            'venues' => $venues,
            'featuredDjs' => $featuredDjs,
        ]);
    }
}
