<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DjProfileResource;
use App\Models\DjProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DjProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DjProfile::with(['user', 'genres', 'reviews']);

        // Apply filters
        if ($request->has('genre')) {
            $query->whereHas('genres', function($q) use ($request) {
                $q->where('genres.id', $request->genre);
            });
        }

        if ($request->has('location')) {
            $query->where('location', 'like', '%'.$request->location.'%');
        }

        if ($request->has('min_rate')) {
            $query->where('hourly_rate', '>=', $request->min_rate);
        }

        if ($request->has('max_rate')) {
            $query->where('hourly_rate', '<=', $request->max_rate);
        }

        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->is_featured);
        }

        if ($request->has('is_available')) {
            $query->where('is_available', $request->is_available);
        }

        // Sort options
        if ($request->has('sort_by')) {
            if ($request->sort_by === 'rating') {
                $query->withAvg('reviews', 'rating')
                      ->orderBy('reviews_avg_rating', $request->sort_dir ?? 'desc');
            } else {
                $query->orderBy($request->sort_by, $request->sort_dir ?? 'asc');
            }
        } else {
            $query->orderBy('is_featured', 'desc')
                  ->orderBy('created_at', 'desc');
        }

        $djProfiles = $query->paginate(12);
        return DjProfileResource::collection($djProfiles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'stage_name' => 'required|string|max:255',
            'specialty' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'experience' => 'nullable|string|max:255',
            'hourly_rate' => 'nullable|numeric|min:0',
            'minimum_booking_hours' => 'nullable|integer|min:1',
            'profile_image' => 'nullable|string|max:255',
            'instagram_url' => 'nullable|string|max:255',
            'soundcloud_url' => 'nullable|string|max:255',
            'website_url' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'is_featured' => 'sometimes|boolean',
            'is_available' => 'sometimes|boolean',
            'genres' => 'sometimes|array',
            'genres.*' => 'exists:genres,id',
        ]);

        // Ensure user is of type 'dj'
        $user = User::findOrFail($validated['user_id']);
        if ($user->user_type !== 'dj') {
            $user->update(['user_type' => 'dj']);
        }

        return DB::transaction(function () use ($validated) {
            $djProfile = DjProfile::create($validated);

            if (isset($validated['genres'])) {
                $djProfile->genres()->attach($validated['genres']);
            }

            return new DjProfileResource($djProfile->load(['user', 'genres']));
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(DjProfile $djProfile)
    {
        return new DjProfileResource($djProfile->load(['user', 'genres', 'reviews.user']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DjProfile $djProfile)
    {
        $validated = $request->validate([
            'stage_name' => 'sometimes|string|max:255',
            'specialty' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'experience' => 'nullable|string|max:255',
            'hourly_rate' => 'nullable|numeric|min:0',
            'minimum_booking_hours' => 'nullable|integer|min:1',
            'profile_image' => 'nullable|string|max:255',
            'instagram_url' => 'nullable|string|max:255',
            'soundcloud_url' => 'nullable|string|max:255',
            'website_url' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'is_featured' => 'sometimes|boolean',
            'is_available' => 'sometimes|boolean',
            'genres' => 'sometimes|array',
            'genres.*' => 'exists:genres,id',
        ]);

        return DB::transaction(function () use ($validated, $djProfile) {
            $djProfile->update($validated);

            if (isset($validated['genres'])) {
                $djProfile->genres()->sync($validated['genres']);
            }

            return new DjProfileResource($djProfile->load(['user', 'genres']));
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DjProfile $djProfile)
    {
        $djProfile->delete();
        return response()->json(null, 204);
    }

    /**
     * Get the current DJ's profile
     */
    public function myProfile()
    {
        $user = Auth::user();
        if (!$user->isDj() || !$user->djProfile) {
            return response()->json(['message' => 'DJ profile not found'], 404);
        }

        return new DjProfileResource($user->djProfile->load(['genres', 'reviews.user']));
    }
}
