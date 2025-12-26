<?php

use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\DjProfileController;
use App\Http\Controllers\API\GenreController;
use App\Http\Controllers\API\PlaylistController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\SongController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\VenueController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public API routes
Route::get('/genres', [GenreController::class, 'index']);
Route::get('/djs', [DjProfileController::class, 'index']);
Route::get('/djs/{djProfile}', [DjProfileController::class, 'show']);
Route::get('/djs/{djProfile}/reviews', [ReviewController::class, 'forDj']);

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    // User related
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // DJ Profiles
    Route::apiResource('dj-profiles', DjProfileController::class);
    Route::get('/my-dj-profile', [DjProfileController::class, 'myProfile']);

    // Venues
    Route::apiResource('venues', VenueController::class);
    Route::get('/my-venues', [VenueController::class, 'myVenues']);

    // Bookings
    Route::apiResource('bookings', BookingController::class);
    Route::get('/my-bookings', [BookingController::class, 'myBookings']);
    Route::get('/my-dj-bookings', [BookingController::class, 'myDjBookings']);

    // Playlists
    Route::apiResource('playlists', PlaylistController::class);
    Route::get('/my-playlists', [PlaylistController::class, 'myPlaylists']);
    Route::post('/playlists/{playlist}/songs', [PlaylistController::class, 'addSong']);
    Route::delete('/playlists/{playlist}/songs/{song}', [PlaylistController::class, 'removeSong']);
    Route::patch('/playlists/{playlist}/songs/reorder', [PlaylistController::class, 'reorderSongs']);

    // Songs
    Route::apiResource('songs', SongController::class);
    Route::get('/songs/search', [SongController::class, 'search']);

    // Reviews
    Route::apiResource('reviews', ReviewController::class);
    Route::get('/my-reviews', [ReviewController::class, 'myReviews']);

    // Admin only routes
    Route::middleware('can.admin')->group(function () {
        Route::apiResource('users', UserController::class);
    });
});
