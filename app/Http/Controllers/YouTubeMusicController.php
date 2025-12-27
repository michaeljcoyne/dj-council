<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class YouTubeMusicController extends Controller
{
    /**
     * Search YouTube for a song and return embeddable video ID
     */
    public function search(Request $request)
    {
        $query = $request->input('q'); // e.g. "Dua Lipa Don't Start Now"

        if (!$query) {
            return response()->json(['error' => 'Query required'], 400);
        }

        $apiKey = config('services.youtube.api_key');

        if (!$apiKey) {
            return response()->json(['error' => 'YouTube API not configured'], 500);
        }

        try {
            // Search YouTube for the song
            $response = Http::get('https://www.googleapis.com/youtube/v3/search', [
                'part' => 'snippet',
                'q' => $query . ' official audio', // Add "official audio" for better results
                'type' => 'video',
                'videoCategoryId' => '10', // Music category
                'maxResults' => 1,
                'key' => $apiKey,
            ]);

            if ($response->failed()) {
                return response()->json(['error' => 'YouTube API error'], 500);
            }

            $data = $response->json();

            if (empty($data['items'])) {
                return response()->json(['video_id' => null]);
            }

            $videoId = $data['items'][0]['id']['videoId'];

            return response()->json([
                'video_id' => $videoId,
                'embed_url' => "https://www.youtube.com/embed/{$videoId}?autoplay=1&start=0&end=30",
            ]);

        } catch (\Exception $e) {
            \Log::error('YouTube search error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Search failed'], 500);
        }
    }
}
