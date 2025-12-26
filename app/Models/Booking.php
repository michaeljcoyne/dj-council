<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dj_profile_id',
        'venue_id',
        'event_type',
        'event_date',
        'duration_hours',
        'total_price',
        'special_requests',
        'status',
        'playlist_id',
        'is_recurring',
        'recurring_frequency',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'total_price' => 'decimal:2',
        'duration_hours' => 'integer',
        'is_recurring' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function djProfile()
    {
        return $this->belongsTo(DjProfile::class);
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}
