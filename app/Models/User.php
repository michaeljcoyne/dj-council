<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'phone',
        'avatar',
        'spotify_access_token',
        'spotify_refresh_token',
        'spotify_token_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'spotify_token_expires_at' => 'datetime',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function djProfile()
    {
        return $this->hasOne(DjProfile::class);
    }

    public function venues()
    {
        return $this->hasMany(Venue::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function playlists()
    {
        return $this->hasMany(Playlist::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function isDj()
    {
        return $this->user_type === 'dj';
    }

    public function isVenue()
    {
        return $this->user_type === 'venue';
    }

    public function isAdmin()
    {
        return $this->user_type === 'admin';
    }

    public function clientPlaylists()
    {
        return $this->hasMany(ClientPlaylist::class);
    }
}
