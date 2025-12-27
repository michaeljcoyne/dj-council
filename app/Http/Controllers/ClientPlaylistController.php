<?php

namespace App\Http\Controllers;

use App\Models\ClientPlaylist;
use App\Models\ClientPlaylistSong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ClientPlaylistController extends Controller
{
    /**
     * Display client's playlists
     */
    public function index()
    {
        $playlists = auth()->user()
            ->clientPlaylists()
            ->withCount('songs')
            ->with('booking')
            ->latest()
            ->get();

        return Inertia::render('Client/Playlists/Index', [
            'playlists' => $playlists
        ]);
    }

    /**
     * Show create playlist form
     */
    public function create()
    {
        return Inertia::render('Client/Playlists/Create');
    }

    /**
     * Store new playlist
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:must_play,nice_to_have,do_not_play,general',
            'booking_id' => 'nullable|exists:bookings,id',
        ]);

        $playlist = auth()->user()->clientPlaylists()->create($validated);

        return redirect()->route('client.playlists.edit', $playlist->id)
            ->with('success', 'Playlist created! Now add some songs.');
    }

    /**
     * Show playlist with songs
     */
    public function show($id)
    {
        $playlist = ClientPlaylist::with(['songs', 'booking'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return Inertia::render('Client/Playlists/Show', [
            'playlist' => $playlist
        ]);
    }

    /**
     * Show edit playlist form
     */
    public function edit($id)
    {
        $playlist = ClientPlaylist::with('songs')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return Inertia::render('Client/Playlists/Edit', [
            'playlist' => $playlist
        ]);
    }

    /**
     * Update playlist
     */
    public function update(Request $request, $id)
    {
        $playlist = ClientPlaylist::where('user_id', auth()->id())
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:must_play,nice_to_have,do_not_play,general',
        ]);

        $playlist->update($validated);

        return back()->with('success', 'Playlist updated!');
    }

    /**
     * Delete playlist
     */
    public function destroy($id)
    {
        $playlist = ClientPlaylist::where('user_id', auth()->id())
            ->findOrFail($id);

        $playlist->delete();

        return redirect()->route('client.playlists')
            ->with('success', 'Playlist deleted!');
    }

    /**
     * Search songs via Spotify API
     */
    public function searchSongs(Request $request)
    {
        $query = $request->get('q');

        if (empty($query)) {
            return response()->json([]);
        }

        try {
            // Get Spotify access token (client credentials flow)
            $token = $this->getSpotifyClientToken();

            // Search Spotify
            $response = Http::withToken($token)
                ->get('https://api.spotify.com/v1/search', [
                    'q' => $query,
                    'type' => 'track',
                    'limit' => 20
                ]);

            if (!$response->successful()) {
                throw new \Exception('Spotify search failed');
            }

            $tracks = $response->json()['tracks']['items'];

            // DEBUG: Log first track to see what Spotify returns
            if (!empty($tracks)) {
                \Log::info('Spotify API first track:', [
                    'name' => $tracks[0]['name'],
                    'preview_url' => $tracks[0]['preview_url'],
                    'has_preview' => !empty($tracks[0]['preview_url'])
                ]);
            }

            // Format results
            $results = collect($tracks)->map(function ($track) {
                return [
                    'title' => $track['name'],
                    'artist' => collect($track['artists'])->pluck('name')->join(', '),
                    'album' => $track['album']['name'],
                    'duration' => floor($track['duration_ms'] / 1000),
                    'spotify_id' => $track['id'],
                    'spotify_uri' => $track['uri'],
                    'image' => $track['album']['images'][0]['url'] ?? null,
                    'preview_url' => $track['preview_url'],
                ];
            });

            return response()->json($results);

        } catch (\Exception $e) {
            Log::error('Spotify search error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to search songs'
            ], 500);
        }
    }

    /**
     * Add song to playlist
     */
    public function addSong(Request $request, $id)
    {
        $playlist = ClientPlaylist::where('user_id', auth()->id())
            ->findOrFail($id);

        // DEBUG: Log what we received
        \Log::info('Received song data:', $request->all());

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'required|string|max:255',
            'album' => 'nullable|string|max:255',
            'duration' => 'nullable|integer',
            'spotify_id' => 'nullable|string',
            'spotify_uri' => 'nullable|string',
            'image' => 'nullable|string',
            'preview_url' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // DEBUG: Log what passed validation
        \Log::info('Validated song data:', $validated);

        // Get next position
        $nextPosition = $playlist->songs()->max('position') + 1;

        // WORKAROUND: Use raw SQL because Eloquent has SQLite bug with these columns
        $songId = \DB::table('client_playlist_songs')->insertGetId([
            'client_playlist_id' => $playlist->id,
            'title' => $validated['title'],
            'artist' => $validated['artist'],
            'album' => $validated['album'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'spotify_id' => $validated['spotify_id'] ?? null,
            'spotify_uri' => $validated['spotify_uri'] ?? null,
            'image' => $validated['image'] ?? null,
            'preview_url' => $validated['preview_url'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'position' => $nextPosition,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $song = ClientPlaylistSong::find($songId);

        // DEBUG: Log what was saved
        \Log::info('Saved song:', $song->toArray());

        return response()->json([
            'message' => 'Song added!',
            'song' => $song
        ]);
    }

    /**
     * Remove song from playlist
     */
    public function removeSong($playlistId, $songId)
    {
        $playlist = ClientPlaylist::where('user_id', auth()->id())
            ->findOrFail($playlistId);

        $song = $playlist->songs()->findOrFail($songId);
        $song->delete();

        // Reorder remaining songs
        $playlist->songs()->where('position', '>', $song->position)
            ->decrement('position');

        return response()->json([
            'message' => 'Song removed!'
        ]);
    }

    /**
     * Reorder songs in playlist
     */
    public function reorderSongs(Request $request, $id)
    {
        $playlist = ClientPlaylist::where('user_id', auth()->id())
            ->findOrFail($id);

        $validated = $request->validate([
            'song_ids' => 'required|array',
            'song_ids.*' => 'required|integer|exists:client_playlist_songs,id'
        ]);

        // Update positions
        foreach ($validated['song_ids'] as $position => $songId) {
            ClientPlaylistSong::where('id', $songId)
                ->where('client_playlist_id', $playlist->id)
                ->update(['position' => $position]);
        }

        return response()->json([
            'message' => 'Playlist reordered!'
        ]);
    }

    /**
     * Get Spotify client credentials token
     */
    private function getSpotifyClientToken()
    {
        $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'client_credentials',
            'client_id' => config('services.spotify.client_id'),
            'client_secret' => config('services.spotify.client_secret'),
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get Spotify token');
        }

        return $response->json()['access_token'];
    }
}
