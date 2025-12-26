<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\VenueResource;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VenueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Venue::with('user');

        // Filter by city
        if ($request->has('city')) {
            $query->where('city', 'like', "%{$request->city}%");
        }

        // Filter by capacity
        if ($request->has('min_capacity')) {
            $query->where('capacity', '>=', $request->min_capacity);
        }

        if ($request->has('max_capacity')) {
            $query->where('capacity', '<=', $request->max_capacity);
        }

        // Filter by verification status
        if ($request->has('is_verified')) {
            $query->where('is_verified', $request->is_verified);
        }

        // Sort options
        if ($request->has('sort_by')) {
            $query->orderBy($request->sort_by, $request->sort_dir ?? 'asc');
        } else {
            $query->orderBy('name', 'asc');
        }

        $venues = $query->paginate(15);
        return VenueResource::collection($venues);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'capacity' => 'nullable|integer|min:1',
            'amenities' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'is_verified' => 'sometimes|boolean',
        ]);

        // Ensure user is of type 'venue'
        $user = User::findOrFail($validated['user_id']);
        if ($user->user_type !== 'venue') {
            $user->update(['user_type' => 'venue']);
        }

        $venue = Venue::create($validated);

        return new VenueResource($venue->load('user'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Venue $venue)
    {
        return new VenueResource($venue->load('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venue $venue)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'address' => 'sometimes|string',
            'city' => 'sometimes|string|max:100',
            'state' => 'sometimes|string|max:100',
            'postal_code' => 'sometimes|string|max:20',
            'capacity' => 'nullable|integer|min:1',
            'amenities' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'is_verified' => 'sometimes|boolean',
        ]);

        $venue->update($validated);

        return new VenueResource($venue->load('user'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venue $venue)
    {
        $venue->delete();
        return response()->json(null, 204);
    }

    /**
     * Get venues for the currently authenticated user
     */
    public function myVenues()
    {
        $user = Auth::user();
        $venues = Venue::where('user_id', $user->id)
            ->orderBy('name', 'asc')
            ->get();

        return VenueResource::collection($venues);
    }
}
