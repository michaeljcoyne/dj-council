<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Playlist;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PlaylistController extends Controller
{
    /**
     * Show the playlist creation page
     */
    public function create()
    {
        return Inertia::render('Playlist/Create', [
            'genres' => Genre::orderBy('name')->get(),
        ]);
    }

    /**
     * Show the playlist editor
     */
    public function edit($id)
    {
        $playlist = Playlist::where('user_id', Auth::id())
            ->with('songs')
            ->findOrFail($id);

        return Inertia::render('Playlist/Edit', [
            'playlist' => $playlist,
            'genres' => Genre::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a new playlist
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'sometimes|boolean',
            'songs' => 'sometimes|array',
            'songs.*.id' => 'required|exists:songs,id',
            'songs.*.position' => 'sometimes|integer|min:0',
        ]);

        $playlist = Playlist::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_public' => $validated['is_public'] ?? false,
        ]);

        // Attach songs with positions
        if (isset($validated['songs']) && count($validated['songs']) > 0) {
            $syncData = [];
            foreach ($validated['songs'] as $index => $song) {
                $syncData[$song['id']] = [
                    'position' => $song['position'] ?? $index,
                ];
            }
            $playlist->songs()->sync($syncData);
        }

        return redirect()->route('client.playlists.show', $playlist->id)
            ->with('success', 'Playlist created successfully!');
    }

    /**
     * Update an existing playlist
     */
    public function update(Request $request, $id)
    {
        $playlist = Playlist::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'sometimes|boolean',
        ]);

        $playlist->update($validated);

        return redirect()->route('client.playlists.show', $playlist->id)
            ->with('success', 'Playlist updated successfully!');
    }

    /**
     * Search for songs to add to playlist
     */
    public function searchSongs(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2',
            'genre' => 'nullable|exists:genres,id',
        ]);

        $query = Song::query();

        // Search by title or artist
        $query->where(function($q) use ($validated) {
            $q->where('title', 'like', "%{$validated['query']}%")
              ->orWhere('artist', 'like', "%{$validated['query']}%");
        });

        // Filter by genre if provided
        if (isset($validated['genre']) && $validated['genre']) {
            // This would require songs to have a relationship with genres
            // You may need to adjust based on your actual schema
        }

        $songs = $query->take(15)->get();

        return response()->json($songs);
    }

    /**
     * Update playlist songs
     */
    public function updateSongs(Request $request, $id)
    {
        $playlist = Playlist::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'songs' => 'required|array',
            'songs.*.id' => 'required|exists:songs,id',
            'songs.*.position' => 'sometimes|integer|min:0',
        ]);

        $syncData = [];
        foreach ($validated['songs'] as $index => $song) {
            $syncData[$song['id']] = [
                'position' => $song['position'] ?? $index,
            ];
        }

        $playlist->songs()->sync($syncData);

        return redirect()->route('client.playlists.show', $playlist->id)
            ->with('success', 'Playlist songs updated successfully!');
    }

    /**
     * Delete a playlist
     */
    public function destroy($id)
    {
        $playlist = Playlist::where('user_id', Auth::id())->findOrFail($id);
        $playlist->delete();

        return redirect()->route('client.playlists')
            ->with('success', 'Playlist deleted successfully!');
    }
}
