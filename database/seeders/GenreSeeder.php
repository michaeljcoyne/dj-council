<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = [
            'House',
            'Techno',
            'EDM',
            'Hip-Hop',
            'R&B',
            'Disco',
            'Pop',
            'Rock',
            'Latin',
            '80s',
            '90s',
            'Funk',
            'Jazz',
            'Soul',
            'Reggae',
            'Afrobeats',
            'Dancehall',
            'Lounge',
            'Ambient',
            'Trance',
            'Drum & Bass',
            'Dubstep',
            'Country',
            'Top 40',
            'Wedding',
            'Corporate',
        ];

        foreach ($genres as $genre) {
            Genre::create(['name' => $genre]);
        }
    }
}
