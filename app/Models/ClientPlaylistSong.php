<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientPlaylistSong extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_playlist_id',
        'title',
        'artist',
        'album',
        'duration',
        'spotify_id',
        'spotify_uri',
        'position',
        'notes',
    ];

    protected $casts = [
        'duration' => 'integer',
        'position' => 'integer',
    ];

    public function playlist()
    {
        return $this->belongsTo(ClientPlaylist::class, 'client_playlist_id');
    }

    public function getFormattedDurationAttribute()
    {
        if (!$this->duration) {
            return '0:00';
        }

        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;

        return sprintf('%d:%02d', $minutes, $seconds);
    }
}
