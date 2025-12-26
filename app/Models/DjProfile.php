<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DjProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stage_name',
        'specialty',
        'bio',
        'experience',
        'hourly_rate',
        'minimum_booking_hours',
        'profile_image',
        'instagram_url',
        'soundcloud_url',
        'website_url',
        'location',
        'is_featured',
        'is_available',
        'spotify_id',
        'spotify_access_token',
        'spotify_refresh_token',
        'spotify_token_expires_at',
        'spotify_playlists',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_available' => 'boolean',
        'hourly_rate' => 'decimal:2',
        'spotify_playlists' => 'array',
        'spotify_token_expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // In DjProfile model
    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'dj_genre');
    }

    public function djProfiles()
    {
        return $this->belongsToMany(DjProfile::class, 'dj_genre');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }
}
