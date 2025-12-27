<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientPlaylist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'booking_id',
        'name',
        'description',
        'type',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function songs()
    {
        return $this->hasMany(ClientPlaylistSong::class)->orderBy('position');
    }

    public function getTotalDurationAttribute()
    {
        return $this->songs->sum('duration');
    }

    public function getFormattedDurationAttribute()
    {
        $totalSeconds = $this->total_duration;
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);

        if ($hours > 0) {
            return sprintf('%dh %02dm', $hours, $minutes);
        }
        return sprintf('%d min', $minutes);
    }
}
