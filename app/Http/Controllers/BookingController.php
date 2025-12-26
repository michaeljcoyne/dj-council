<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\DjProfile;
use App\Models\Playlist;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BookingController extends Controller
{
    /**
     * Display the booking form
     */
    public function create($djId = null)
    {
        $dj = null;
        if ($djId) {
            $dj = DjProfile::with(['user', 'genres'])->findOrFail($djId);
        }

        $playlists = Auth::user()->playlists()->get();
        $venues = Venue::where('user_id', Auth::id())->get();
        $featuredDjs = null;

        if (!$dj) {
            $featuredDjs = DjProfile::where('is_featured', true)
                ->with(['user', 'genres'])
                ->take(6)
                ->get();
        }

        return Inertia::render('Booking/Create', [
            'dj' => $dj,
            'playlists' => $playlists,
            'venues' => $venues,
            'featuredDjs' => $featuredDjs,
        ]);
    }

    /**
     * Store a new booking
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

        // Set initial status
        $validated['status'] = 'pending';

        $booking = Booking::create($validated);

        return redirect()->route('client.bookings.show', $booking->id)
            ->with('success', 'Booking request submitted successfully!');
    }

    /**
     * Show the booking confirmation page
     */
    public function confirm($id)
    {
        $booking = Booking::with(['user', 'djProfile.user', 'venue', 'playlist.songs'])
            ->findOrFail($id);

        // Ensure the DJ is the one confirming the booking
        if ($booking->djProfile->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return Inertia::render('Booking/Confirm', [
            'booking' => $booking,
        ]);
    }

    /**
     * Update the booking status
     */
    public function updateStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        
        // Check authorization based on status and user role
        $user = Auth::user();
        $newStatus = $request->status;
        
        if ($user->isDj() && $booking->djProfile->user_id === $user->id) {
            // DJs can confirm or decline pending bookings
            if ($booking->status === 'pending' && in_array($newStatus, ['confirmed', 'cancelled'])) {
                $booking->status = $newStatus;
                $booking->save();
                
                return redirect()->route('dj.bookings')
                    ->with('success', 'Booking ' . ($newStatus === 'confirmed' ? 'confirmed' : 'cancelled') . ' successfully!');
            }
        } elseif ($booking->user_id === $user->id) {
            // Clients can cancel their bookings
            if ($newStatus === 'cancelled') {
                $booking->status = 'cancelled';
                $booking->save();
                
                return redirect()->route('client.bookings')
                    ->with('success', 'Booking cancelled successfully!');
            }
        }
        
        abort(403, 'Unauthorized action.');
    }

    /**
     * Mark a booking as completed
     */
    public function markCompleted($id)
    {
        $booking = Booking::findOrFail($id);
        
        // Ensure the DJ or client can mark as completed
        $user = Auth::user();
        
        if (($user->isDj() && $booking->djProfile->user_id === $user->id) || 
            $booking->user_id === $user->id) {
            
            if ($booking->status === 'confirmed' && $booking->event_date < now()) {
                $booking->status = 'completed';
                $booking->save();
                
                if ($booking->user_id === $user->id) {
                    return redirect()->route('client.bookings.show', $booking->id)
                        ->with('success', 'Booking marked as completed. Please leave a review!');
                } else {
                    return redirect()->route('dj.bookings')
                        ->with('success', 'Booking marked as completed successfully!');
                }
            }
        }
        
        abort(403, 'Unauthorized action.');
    }
}
