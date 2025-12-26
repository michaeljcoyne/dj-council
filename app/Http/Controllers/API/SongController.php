<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SongResource;
use App\Models\Song;
use Illuminate\Http\Request;

class SongController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Song::query();

        // Search by title or artist
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('artist', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by artist
        if ($request->has('artist')) {
            $query->where('artist', 'like', "%{$request->artist}%");
        }

        // Sort options
        if ($request->has('sort_by')) {
            $query->orderBy($request->sort_by, $request->sort_dir ?? 'asc');
        } else {
            $query->orderBy('title', 'asc');
        }

        $songs = $query->paginate(20);
        return SongResource::collection($songs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'required|string|max:255',
            'duration' => 'nullable|integer',
            'spotify_id' => 'nullable|string|max:255',
        ]);

        $song = Song::create($validated);

        return new SongResource($song);
    }

    /**
     * Display the specified resource.
     */
    public function show(Song $song)
    {
        return new SongResource($song);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Song $song)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'artist' => 'sometimes|string|max:255',
            'duration' => 'nullable|integer',
            'spotify_id' => 'nullable|string|max:255',
        ]);

        $song->update($validated);

        return new SongResource($song);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Song $song)
    {
        $song->delete();
        return response()->json(null, 204);
    }

    /**
     * Search for songs (for use in playlist creation)
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $songs = Song::where('title', 'like', "%{$validated['query']}%")
            ->orWhere('artist', 'like', "%{$validated['query']}%")
            ->take(15)
            ->get();

        return SongResource::collection($songs);
    }
}
