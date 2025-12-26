<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlaylistResource;
use App\Models\Playlist;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlaylistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Playlist::with(['user', 'songs']);

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by visibility
        if ($request->has('is_public')) {
            $query->where('is_public', $request->is_public);
        } else {
            // By default, only show public playlists and user's own playlists
            $query->where(function ($q) {
                $q->where('is_public', true)
                  ->orWhere('user_id', Auth::id());
            });
        }

        // Sort options
        $query->orderBy('created_at', 'desc');

        $playlists = $query->paginate(10);
        return PlaylistResource::collection($playlists);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'sometimes|boolean',
            'songs' => 'sometimes|array',
            'songs.*.id' => 'sometimes|exists:songs,id',
            'songs.*.position' => 'sometimes|integer|min:0',
        ]);

        return DB::transaction(function () use ($validated, $request) {
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

            return new PlaylistResource($playlist->load('songs'));
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Playlist $playlist)
    {
        // Check if playlist is private and belongs to another user
        if (!$playlist->is_public && $playlist->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return new PlaylistResource($playlist->load(['user', 'songs']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Playlist $playlist)
    {
        // Authorization check
        if ($playlist->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'sometimes|boolean',
        ]);

        $playlist->update($validated);

        return new PlaylistResource($playlist->load('songs'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Playlist $playlist)
    {
        // Authorization check
        if ($playlist->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $playlist->delete();
        return response()->json(null, 204);
    }

    /**
     * Add a song to the playlist
     */
    public function addSong(Request $request, Playlist $playlist)
    {
        // Authorization check
        if ($playlist->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'song_id' => 'required|exists:songs,id',
            'position' => 'nullable|integer|min:0',
        ]);

        $position = $validated['position'] ?? $playlist->songs()->count();

        $playlist->songs()->attach($validated['song_id'], [
            'position' => $position
        ]);

        return new PlaylistResource($playlist->load('songs'));
    }

    /**
     * Remove a song from the playlist
     */
    public function removeSong(Request $request, Playlist $playlist, Song $song)
    {
        // Authorization check
        if ($playlist->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $playlist->songs()->detach($song->id);

        // Reorder positions
        $playlist->songs()->get()->each(function ($song, $index) use ($playlist) {
            $playlist->songs()->updateExistingPivot($song->id, ['position' => $index]);
        });

        return new PlaylistResource($playlist->load('songs'));
    }

    /**
     * Reorder songs in the playlist
     */
    public function reorderSongs(Request $request, Playlist $playlist)
    {
        // Authorization check
        if ($playlist->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'songs' => 'required|array',
            'songs.*.id' => 'required|exists:songs,id',
            'songs.*.position' => 'required|integer|min:0',
        ]);

        return DB::transaction(function () use ($validated, $playlist) {
            foreach ($validated['songs'] as $song) {
                $playlist->songs()->updateExistingPivot($song['id'], [
                    'position' => $song['position']
                ]);
            }

            return new PlaylistResource($playlist->load('songs'));
        });
    }

    /**
     * Get playlists for the currently authenticated user
     */
    public function myPlaylists()
    {
        $playlists = Playlist::where('user_id', Auth::id())
            ->with('songs')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return PlaylistResource::collection($playlists);
    }
}
