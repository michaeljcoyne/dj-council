<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DjController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VenueController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PlaylistWidgetController;
use Inertia\Inertia;
use App\Http\Controllers\SpotifyController;
use App\Http\Controllers\ClientPlaylistController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

//Route::get('/api/dj/{djId}/playlists', [App\Http\Controllers\API\PlaylistWidgetController::class, 'getPlaylists']);

// Public routes
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::get('/widget/dj/{djId}', [PlaylistWidgetController::class, 'serveWidget'])
    ->name('public.dj.widget');

Route::get('/djs', function () {
    $featuredDjs = App\Models\DjProfile::where('is_featured', true)
        ->with(['user', 'genres'])
        ->take(6)
        ->get();

    return Inertia::render('Public/DjListing', [
        'featuredDjs' => $featuredDjs,
        'genres' => App\Models\Genre::orderBy('name')->get(),
    ]);
})->name('djs.public');

Route::get('/djs/{id}', function ($id) {
    $djProfile = App\Models\DjProfile::with(['user', 'genres', 'reviews' => function ($query) {
        $query->where('is_approved', true)
            ->with('user')
            ->orderBy('created_at', 'desc');
    }])->findOrFail($id);

    return Inertia::render('Public/DjProfile', [
        'dj' => $djProfile,
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('djs.show.public');

// Protected routes
Route::middleware('auth')->group(function () {
    // User profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Role-based redirects
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isDj()) {
            return redirect()->route('dj.dashboard');
        } elseif ($user->isVenue()) {
            return redirect()->route('venue.dashboard');
        } else {
            return redirect()->route('client.dashboard');
        }
    })->name('dashboard');

    // Client routes
    Route::prefix('client')->name('client.')->middleware(['auth', 'verified'])->group(function () {
        Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('dashboard');
        Route::get('/djs', [ClientController::class, 'browseDjs'])->name('djs');
        Route::get('/djs/{id}', [ClientController::class, 'showDj'])->name('djs.show');
        Route::get('/djs/{id}/book', [ClientController::class, 'bookDj'])->name('djs.book');

        Route::get('/bookings', [ClientController::class, 'bookings'])->name('bookings');
        Route::get('/bookings/{id}', [ClientController::class, 'showBooking'])->name('bookings.show');


        // Playlist CRUD routes
        Route::get('/playlists', [ClientPlaylistController::class, 'index'])->name('playlists');
        Route::get('/playlists/create', [ClientPlaylistController::class, 'create'])->name('playlists.create');
        Route::post('/playlists', [ClientPlaylistController::class, 'store'])->name('playlists.store');
        Route::get('/playlists/{id}', [ClientPlaylistController::class, 'show'])->name('playlists.show');
        Route::get('/playlists/{id}/edit', [ClientPlaylistController::class, 'edit'])->name('playlists.edit');
        Route::put('/playlists/{id}', [ClientPlaylistController::class, 'update'])->name('playlists.update');
        Route::delete('/playlists/{id}', [ClientPlaylistController::class, 'destroy'])->name('playlists.destroy');

        // Song management routes
        Route::get('/songs/search', [ClientPlaylistController::class, 'searchSongs'])->name('songs.search');
        Route::post('/playlists/{id}/songs', [ClientPlaylistController::class, 'addSong'])->name('playlists.songs.add');
        Route::delete('/playlists/{id}/songs/{songId}', [ClientPlaylistController::class, 'removeSong'])->name('playlists.songs.remove');
        Route::put('/playlists/{id}/songs/reorder', [ClientPlaylistController::class, 'reorderSongs'])->name('playlists.songs.reorder');

        //Route::get('/playlists', [ClientController::class, 'playlists'])->name('playlists');
        //Route::get('/playlists/create', [ClientController::class, 'editPlaylist'])->name('playlists.create');
        //Route::get('/playlists/{id}', [ClientController::class, 'showPlaylist'])->name('playlists.show');
        //Route::get('/playlists/{id}/edit', [ClientController::class, 'editPlaylist'])->name('playlists.edit');

        Route::get('/venues', [ClientController::class, 'venues'])->name('venues');
        Route::get('/venues/create', [ClientController::class, 'editVenue'])->name('venues.create');
        Route::get('/venues/{id}/edit', [ClientController::class, 'editVenue'])->name('venues.edit');
    });


    // Spotify OAuth routes
    Route::prefix('spotify')->name('spotify.')->group(function () {
        Route::get('/redirect', [SpotifyController::class, 'redirect'])->name('redirect');
        Route::get('/callback', [SpotifyController::class, 'callback'])->name('callback');
        Route::post('/disconnect', [SpotifyController::class, 'disconnect'])->name('disconnect');
        Route::get('/playlists', [SpotifyController::class, 'getPlaylists'])->name('playlists.get');
        Route::post('/playlists/save', [SpotifyController::class, 'savePlaylists'])->name('playlists.save');
        Route::get('/playlists/{id}', [SpotifyController::class, 'getPlaylistDetails'])->name('playlists.show');
        Route::post('/widget/generate', [SpotifyController::class, 'generateWidget'])->name('widget.generate');
    });

    // DJ routes
    Route::prefix('dj')->name('dj.')->middleware(['auth', 'verified'])->group(function () {
        Route::get('/dashboard', [DjController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile/edit', [DjController::class, 'editProfile'])->name('profile.edit');
        Route::get('/bookings', [DjController::class, 'bookings'])->name('bookings');
        Route::get('/bookings/{id}', [DjController::class, 'showBooking'])->name('bookings.show');
        Route::get('/reviews', [DjController::class, 'reviews'])->name('reviews');
        Route::get('/earnings', [DjController::class, 'earnings'])->name('earnings');


        Route::get('/playlists', function () {
            $djProfile = auth()->user()->djProfile;
            return Inertia::render('DJ/SpotifyPlaylists', [
                'djProfile' => $djProfile,
                'isConnected' => !empty($djProfile->spotify_id),
            ]);
        })->name('playlists');
    });

    // Venue routes
    Route::prefix('venue')->name('venue.')->middleware(['auth', 'verified'])->group(function () {
        Route::get('/dashboard', [VenueController::class, 'dashboard'])->name('dashboard');
        Route::get('/calendar', [VenueController::class, 'calendar'])->name('calendar');
        Route::get('/bookings', [VenueController::class, 'bookings'])->name('bookings');
        Route::get('/venues', [VenueController::class, 'manageVenues'])->name('manage');
        Route::get('/venues/create', [VenueController::class, 'editVenue'])->name('create');
        Route::get('/venues/{id}/edit', [VenueController::class, 'editVenue'])->name('edit');
        Route::get('/request-dj', [VenueController::class, 'requestDj'])->name('request-dj');
    });

    // Admin routes
    // 'can.admin'
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/djs', [AdminController::class, 'djs'])->name('djs');
        Route::get('/venues', [AdminController::class, 'venues'])->name('venues');
        Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');
        Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');
        Route::get('/genres', [AdminController::class, 'genres'])->name('genres');
        Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
    });

    // Booking routes
    Route::prefix('booking')->name('booking.')->middleware(['auth', 'verified'])->group(function () {
        Route::get('/create/{dj?}', [BookingController::class, 'create'])->name('create');
        Route::post('/store', [BookingController::class, 'store'])->name('store');
        Route::get('/{id}/confirm', [BookingController::class, 'confirm'])->name('confirm');
        Route::patch('/{id}/status', [BookingController::class, 'updateStatus'])->name('status.update');
        Route::patch('/{id}/complete', [BookingController::class, 'markCompleted'])->name('complete');
    });

    // Playlist routes
    Route::prefix('playlist')->name('playlist.')->middleware(['auth', 'verified'])->group(function () {
        Route::get('/create', [PlaylistController::class, 'create'])->name('create');
        Route::get('/{id}/edit', [PlaylistController::class, 'edit'])->name('edit');
        Route::post('/store', [PlaylistController::class, 'store'])->name('store');
        Route::patch('/{id}', [PlaylistController::class, 'update'])->name('update');
        Route::patch('/{id}/songs', [PlaylistController::class, 'updateSongs'])->name('songs.update');
        Route::delete('/{id}', [PlaylistController::class, 'destroy'])->name('destroy');
        Route::get('/search-songs', [PlaylistController::class, 'searchSongs'])->name('search-songs');
    });

});

require __DIR__ . '/auth.php';
