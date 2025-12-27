<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SpotifyController extends Controller
{
    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $baseUrl = 'https://api.spotify.com/v1';
    private $authUrl = 'https://accounts.spotify.com/api/token';

    public function __construct()
    {
        $this->clientId = config('services.spotify.client_id');
        $this->clientSecret = config('services.spotify.client_secret');
        $this->redirectUri = config('services.spotify.redirect_uri');
    }

    /**
     * Redirect to Spotify OAuth
     */
    public function redirect()
    {
        if (!auth()->user()->isDj()) {
            return redirect()->route('dashboard')
                ->with('error', 'Only DJs can connect Spotify accounts');
        }

        $scopes = [
            'user-read-private',
            'user-read-email',
            'playlist-read-private',
            'playlist-read-collaborative',
        ];

        $query = http_build_query([
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri,
            'scope' => implode(' ', $scopes),
            'show_dialog' => true,
        ]);

        return redirect('https://accounts.spotify.com/authorize?' . $query);
    }

    /**
     * Handle Spotify OAuth callback
     */
    public function callback(Request $request)
    {
        $code = $request->get('code');

        if (!$code) {
            return redirect()->route('dj.dashboard')
                ->with('error', 'Spotify authorization failed');
        }

        try {
            // Exchange code for access token
            $response = Http::asForm()->post($this->authUrl, [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            if (!$response->successful()) {
                throw new \Exception('Failed to get access token');
            }

            $data = $response->json();

            // Get user's Spotify profile
            $profileResponse = Http::withToken($data['access_token'])
                ->get($this->baseUrl . '/me');

            if (!$profileResponse->successful()) {
                throw new \Exception('Failed to get Spotify profile');
            }

            $profile = $profileResponse->json();

            // Update DJ profile with Spotify data
            $djProfile = auth()->user()->djProfile;

            if (!$djProfile) {
                return redirect()->back()->with('error', 'DJ profile not found');
            }


            $djProfile->update([
                'spotify_id' => $profile['id'],
                'spotify_access_token' => encrypt($data['access_token']),
                'spotify_refresh_token' => encrypt($data['refresh_token']),
                'spotify_token_expires_at' => now()->addSeconds($data['expires_in']),
            ]);

            return redirect()->route('dj.playlists')
                ->with('success', 'Spotify account connected successfully!');

        } catch (\Exception $e) {
            Log::error('Spotify OAuth error: ' . $e->getMessage());
            return redirect()->route('dj.dashboard')
                ->with('error', 'Failed to connect Spotify account. Please try again.');
        }
    }

    /**
     * Disconnect Spotify account
     */
    public function disconnect()
    {
        $djProfile = auth()->user()->djProfile;

        if (!$djProfile) {
            return redirect()->route('dashboard')
                ->with('error', 'No DJ profile found');
        }

        $djProfile->update([
            'spotify_id' => null,
            'spotify_access_token' => null,
            'spotify_refresh_token' => null,
            'spotify_token_expires_at' => null,
            'spotify_playlists' => null,
        ]);

        return redirect()->back()
            ->with('success', 'Spotify account disconnected');
    }

    /**
     * Get DJ's Spotify playlists
     */
    public function getPlaylists()
    {
        $djProfile = auth()->user()->djProfile;

        if (!$djProfile || !$djProfile->spotify_id) {
            return response()->json([
                'error' => 'Spotify not connected'
            ], 400);
        }

        try {
            $token = $this->getValidAccessToken($djProfile);

            $response = Http::withToken($token)
                ->get($this->baseUrl . '/me/playlists', [
                    'limit' => 50
                ]);

            if (!$response->successful()) {
                throw new \Exception('Failed to fetch playlists');
            }

            $playlists = $response->json()['items'];

            // Format playlists for frontend
            $formatted = collect($playlists)->map(function ($playlist) {
                return [
                    'id' => $playlist['id'],
                    'name' => $playlist['name'],
                    'description' => $playlist['description'],
                    'image' => $playlist['images'][0]['url'] ?? null,
                    'tracks_count' => $playlist['tracks']['total'],
                    'public' => $playlist['public'],
                    'url' => $playlist['external_urls']['spotify'],
                ];
            });

            return response()->json($formatted);

        } catch (\Exception $e) {
            Log::error('Error fetching Spotify playlists: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch playlists'
            ], 500);
        }
    }

    /**
     * Save selected playlists for DJ profile
     */
    public function savePlaylists(Request $request)
    {
        $validated = $request->validate([
            'playlist_ids' => 'required|array',
            'playlist_ids.*' => 'required|string',
        ]);

        $djProfile = auth()->user()->djProfile;

        if (!$djProfile || !$djProfile->spotify_id) {
            return response()->json([
                'error' => 'Spotify not connected'
            ], 400);
        }

        $djProfile->update([
            'spotify_playlists' => $validated['playlist_ids']
        ]);

        return response()->json([
            'message' => 'Playlists saved successfully'
        ]);
    }

    /**
     * Get playlist details including tracks
     */
    public function getPlaylistDetails($playlistId)
    {
        $djProfile = auth()->user()->djProfile;

        if (!$djProfile || !$djProfile->spotify_id) {
            return response()->json([
                'error' => 'Spotify not connected'
            ], 400);
        }

        try {
            $token = $this->getValidAccessToken($djProfile);

            $response = Http::withToken($token)
                ->get($this->baseUrl . '/playlists/' . $playlistId);

            if (!$response->successful()) {
                throw new \Exception('Failed to fetch playlist');
            }

            return response()->json($response->json());

        } catch (\Exception $e) {
            Log::error('Error fetching playlist details: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch playlist'
            ], 500);
        }
    }

    /**
     * Generate embed widget code
     */
    public function generateWidget()
    {
        $djProfile = auth()->user()->djProfile;

        // Add this check
        if (!$djProfile) {
            return response()->json([
                'error' => 'DJ profile not found'
            ], 404);
        }

        if (!$djProfile->spotify_playlists) {
            return response()->json([
                'error' => 'No playlists selected'
            ], 400);
        }

        $widgetUrl = route('public.dj.widget', ['djId' => $djProfile->id]);

        $embedCode = <<<HTML
<!-- DJ Council Widget -->
<div id="dj-council-widget-{$djProfile->id}"></div>
<script src="{$widgetUrl}" async></script>
HTML;

        return response()->json([
            'embed_code' => $embedCode,
            'widget_url' => $widgetUrl,
        ]);
    }

    /**
     * Get or refresh valid access token
     */
    private function getValidAccessToken($djProfile)
    {
        // Check if token is still valid
        if ($djProfile->spotify_token_expires_at && $djProfile->spotify_token_expires_at->isFuture()) {
            return decrypt($djProfile->spotify_access_token);
        }

        // Refresh token
        $response = Http::asForm()->post($this->authUrl, [
            'grant_type' => 'refresh_token',
            'refresh_token' => decrypt($djProfile->spotify_refresh_token),
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to refresh token');
        }

        $data = $response->json();

        // Update tokens
        $djProfile->update([
            'spotify_access_token' => encrypt($data['access_token']),
            'spotify_token_expires_at' => now()->addSeconds($data['expires_in']),
        ]);

        return $data['access_token'];
    }
}
