<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SpotifyOAuthController extends Controller
{
    /**
     * Redirect user to Spotify authorization
     */
    public function redirect()
    {
        $scopes = [
            'streaming',              // Play music
            'user-read-email',        // Get user email
            'user-read-private',      // Get user info
            'user-modify-playback-state', // Control playback
            'user-read-playback-state',   // Read playback state
        ];

        $params = http_build_query([
            'client_id' => config('services.spotify.client_id'),
            'response_type' => 'code',
            'redirect_uri' => config('services.spotify.redirect_uri'),
            'scope' => implode(' ', $scopes),
            'show_dialog' => false,
        ]);

        return redirect('https://accounts.spotify.com/authorize?' . $params);
    }

    /**
     * Handle Spotify callback
     */
    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return redirect('/client/settings')->with('error', 'Spotify authorization failed');
        }

        $code = $request->get('code');

        // Exchange code for access token
        $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => config('services.spotify.redirect_uri'),
            'client_id' => config('services.spotify.client_id'),
            'client_secret' => config('services.spotify.client_secret'),
        ]);

        if ($response->failed()) {
            return redirect('/client/settings')->with('error', 'Failed to connect Spotify');
        }

        $data = $response->json();

        // Save tokens to user
        $user = Auth::user();
        $user->update([
            'spotify_access_token' => $data['access_token'],
            'spotify_refresh_token' => $data['refresh_token'],
            'spotify_token_expires_at' => now()->addSeconds($data['expires_in']),
        ]);

        return redirect('/client/settings')->with('success', 'Spotify connected successfully!');
    }

    /**
     * Disconnect Spotify
     */
    public function disconnect()
    {
        $user = Auth::user();
        $user->update([
            'spotify_access_token' => null,
            'spotify_refresh_token' => null,
            'spotify_token_expires_at' => null,
        ]);

        return redirect('/client/settings')->with('success', 'Spotify disconnected');
    }

    /**
     * Get fresh access token (refresh if expired)
     */
    public function getAccessToken()
    {
        $user = Auth::user();

        if (!$user->spotify_refresh_token) {
            return response()->json(['error' => 'Not connected to Spotify'], 401);
        }

        // Check if token is still valid (handle both Carbon and string)
        $expiresAt = $user->spotify_token_expires_at;
        if ($expiresAt) {
            // Convert to Carbon if it's a string
            if (is_string($expiresAt)) {
                $expiresAt = \Carbon\Carbon::parse($expiresAt);
            }

            if ($expiresAt->isFuture()) {
                return response()->json(['access_token' => $user->spotify_access_token]);
            }
        }

        // Refresh token
        $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $user->spotify_refresh_token,
            'client_id' => config('services.spotify.client_id'),
            'client_secret' => config('services.spotify.client_secret'),
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to refresh token'], 500);
        }

        $data = $response->json();

        // Update user with new token
        $user->update([
            'spotify_access_token' => $data['access_token'],
            'spotify_token_expires_at' => now()->addSeconds($data['expires_in']),
        ]);

        return response()->json(['access_token' => $data['access_token']]);
    }
}
