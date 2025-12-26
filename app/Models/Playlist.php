<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function songs()
    {
        return $this->belongsToMany(Song::class)
            ->withPivot('position')
            ->orderBy('position');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getTotalDurationAttribute()
    {
        return $this->songs->sum('duration');
    }
}
