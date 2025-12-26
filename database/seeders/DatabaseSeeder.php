<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
        ]);

        // Create a DJ user
        $djUser = \App\Models\User::factory()->create([
            'name' => 'DJ User',
            'email' => 'dj@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'dj',
        ]);

        // Create a client user
        $clientUser = \App\Models\User::factory()->create([
            'name' => 'Client User',
            'email' => 'client@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'client',
        ]);

        // Create a venue user
        $venueUser = \App\Models\User::factory()->create([
            'name' => 'Venue User',
            'email' => 'venue@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'venue',
        ]);
        
        // Seed genres
        $this->call([
            GenreSeeder::class,
        ]);

        // Create a DJ profile for the DJ user
        $djProfile = \App\Models\DjProfile::create([
            'user_id' => $djUser->id,
            'stage_name' => 'DJ Awesome',
            'specialty' => 'House & Disco',
            'bio' => 'Professional DJ with over 10 years of experience playing at clubs, festivals, and private events.',
            'experience' => '10+ years',
            'hourly_rate' => 100.00,
            'minimum_booking_hours' => 3,
            'location' => 'Los Angeles, CA',
            'is_featured' => true,
            'is_available' => true,
        ]);

        // Attach genres to the DJ profile
        $houseGenre = \App\Models\Genre::where('name', 'House')->first();
        $discoGenre = \App\Models\Genre::where('name', 'Disco')->first();
        
        if ($houseGenre && $discoGenre) {
            $djProfile->genres()->attach([$houseGenre->id, $discoGenre->id]);
        }

        // Create a venue for the venue user
        $venue = \App\Models\Venue::create([
            'user_id' => $venueUser->id,
            'name' => 'Skyline Lounge',
            'description' => 'Elegant rooftop venue with panoramic city views, perfect for corporate events and private parties.',
            'address' => '123 Main Street',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'postal_code' => '90001',
            'capacity' => 200,
            'amenities' => 'Full bar, DJ booth, Dance floor, Audio system, Outdoor patio',
            'is_verified' => true,
        ]);

        // Create sample songs
        $songs = [
            ['title' => 'Stayin\' Alive', 'artist' => 'Bee Gees', 'duration' => 285],
            ['title' => 'Billie Jean', 'artist' => 'Michael Jackson', 'duration' => 294],
            ['title' => 'Get Lucky', 'artist' => 'Daft Punk ft. Pharrell Williams', 'duration' => 248],
            ['title' => 'Don\'t Stop \'Til You Get Enough', 'artist' => 'Michael Jackson', 'duration' => 360],
            ['title' => 'I Wanna Dance with Somebody', 'artist' => 'Whitney Houston', 'duration' => 291],
            ['title' => 'Uptown Funk', 'artist' => 'Mark Ronson ft. Bruno Mars', 'duration' => 270],
            ['title' => 'Dancing Queen', 'artist' => 'ABBA', 'duration' => 231],
            ['title' => 'One More Time', 'artist' => 'Daft Punk', 'duration' => 320],
            ['title' => 'Rhythm Is A Dancer', 'artist' => 'Snap!', 'duration' => 228],
            ['title' => 'Don\'t Start Now', 'artist' => 'Dua Lipa', 'duration' => 183],
        ];

        foreach ($songs as $songData) {
            \App\Models\Song::create($songData);
        }

        // Create a playlist for the client user
        $playlist = \App\Models\Playlist::create([
            'user_id' => $clientUser->id,
            'name' => 'Party Classics',
            'description' => 'A collection of classic dance hits for any party.',
            'is_public' => true,
        ]);

        // Attach songs to the playlist
        $songIds = \App\Models\Song::pluck('id')->toArray();
        $playlistSongs = [];
        
        foreach ($songIds as $index => $songId) {
            $playlistSongs[$songId] = ['position' => $index];
        }
        
        $playlist->songs()->attach($playlistSongs);

        // Create a booking between the client and DJ
        $booking = \App\Models\Booking::create([
            'user_id' => $clientUser->id,
            'dj_profile_id' => $djProfile->id,
            'venue_id' => $venue->id,
            'event_type' => 'Corporate Party',
            'event_date' => now()->addDays(30), // 30 days from now
            'duration_hours' => 4,
            'total_price' => 400.00, // 4 hours at $100/hour
            'special_requests' => 'Please include some 80s classics in your set.',
            'status' => 'confirmed',
            'playlist_id' => $playlist->id,
            'is_recurring' => false,
        ]);

        // Create some reviews for the DJ
        \App\Models\Review::create([
            'user_id' => $clientUser->id,
            'dj_profile_id' => $djProfile->id,
            'booking_id' => null,
            'rating' => 5,
            'comment' => 'Amazing DJ! Kept the party going all night long.',
            'is_approved' => true,
        ]);

        // Create a few more DJs
        $otherDjs = [
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah@example.com',
                'stage_name' => 'DJ Rhythm',
                'specialty' => 'Hip-Hop & R&B',
                'genres' => ['Hip-Hop', 'R&B'],
                'hourly_rate' => 85.00,
                'location' => 'New York, NY',
            ],
            [
                'name' => 'Mike Wilson',
                'email' => 'mike@example.com',
                'stage_name' => 'DJ Electro',
                'specialty' => 'EDM & Techno',
                'genres' => ['EDM', 'Techno'],
                'hourly_rate' => 120.00,
                'location' => 'Miami, FL',
            ],
            [
                'name' => 'Emma Davis',
                'email' => 'emma@example.com',
                'stage_name' => 'DJ Vinyl Queen',
                'specialty' => 'Disco & Funk',
                'genres' => ['Disco', 'Funk'],
                'hourly_rate' => 90.00,
                'location' => 'Chicago, IL',
            ],
        ];

        foreach ($otherDjs as $djData) {
            $user = \App\Models\User::factory()->create([
                'name' => $djData['name'],
                'email' => $djData['email'],
                'password' => Hash::make('password'),
                'user_type' => 'dj',
            ]);

            $profile = \App\Models\DjProfile::create([
                'user_id' => $user->id,
                'stage_name' => $djData['stage_name'],
                'specialty' => $djData['specialty'],
                'bio' => 'Professional DJ specializing in ' . $djData['specialty'] . ' music.',
                'experience' => mt_rand(3, 15) . '+ years',
                'hourly_rate' => $djData['hourly_rate'],
                'minimum_booking_hours' => 2,
                'location' => $djData['location'],
                'is_featured' => mt_rand(0, 1) == 1,
                'is_available' => true,
            ]);

            // Attach genres
            foreach ($djData['genres'] as $genreName) {
                $genre = \App\Models\Genre::where('name', $genreName)->first();
                if ($genre) {
                    $profile->genres()->attach($genre->id);
                }
            }

            // Add some reviews
            for ($i = 0; $i < mt_rand(1, 3); $i++) {
                \App\Models\Review::create([
                    'user_id' => $clientUser->id,
                    'dj_profile_id' => $profile->id,
                    'booking_id' => null,
                    'rating' => mt_rand(3, 5),
                    'comment' => 'Great DJ performance at our event!',
                    'is_approved' => true,
                ]);
            }
        }
    }
}
