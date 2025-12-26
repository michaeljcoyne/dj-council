<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'artist',
        'duration',
        'spotify_id',
    ];

    protected $casts = [
        'duration' => 'integer',
    ];

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class)
            ->withPivot('position');
    }

    public function getFormattedDurationAttribute()
    {
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;
        
        return sprintf('%d:%02d', $minutes, $seconds);
    }
}
