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

        // Try different search strategies - prioritize embeddable content
        // Avoid "official video" which is most likely to be blocked
        $searchStrategies = [
            $query . ' audio',              // Audio versions often embeddable
            $query . ' topic',              // Auto-generated topic channels (always embeddable)
            $query . ' lyric video',        // Lyric videos often embeddable
            $query . ' lyrics',             // Another variant
            $query,                         // Plain search as fallback
        ];

        foreach ($searchStrategies as $searchQuery) {
            try {
                $response = Http::get('https://www.googleapis.com/youtube/v3/search', [
                    'part' => 'snippet',
                    'q' => $searchQuery,
                    'type' => 'video',
                    'videoCategoryId' => '10', // Music category
                    'videoEmbeddable' => 'true', // CRITICAL: Only embeddable videos
                    'videoSyndicated' => 'true', // Can be played outside YouTube
                    'maxResults' => 5, // Get more results to filter through
                    'key' => $apiKey,
                ]);

                if ($response->failed()) {
                    \Log::warning('YouTube API failed', ['query' => $searchQuery]);
                    continue;
                }

                $data = $response->json();

                if (empty($data['items'])) {
                    continue;
                }

                // Try each result, skip "Official Video" titles (most likely blocked)
                foreach ($data['items'] as $item) {
                    $videoId = $item['id']['videoId'];
                    $title = $item['snippet']['title'];

                    // Skip "Official Video" - these are most restrictive
                    if (stripos($title, 'official video') !== false) {
                        \Log::info('Skipping official video', ['title' => $title]);
                        continue;
                    }

                    // VERIFY video is actually embeddable using oEmbed API
                    try {
                        $oembedResponse = Http::get('https://www.youtube.com/oembed', [
                            'url' => "https://www.youtube.com/watch?v={$videoId}",
                            'format' => 'json'
                        ]);

                        if (!$oembedResponse->successful()) {
                            \Log::info('Video not embeddable (oEmbed failed)', ['video_id' => $videoId]);
                            continue; // Try next video
                        }
                    } catch (\Exception $e) {
                        \Log::info('Video not embeddable (exception)', ['video_id' => $videoId]);
                        continue;
                    }

                    \Log::info('YouTube video found and verified embeddable', [
                        'query' => $searchQuery,
                        'video_id' => $videoId,
                        'title' => $title
                    ]);

                    return response()->json([
                        'video_id' => $videoId,
                        'embed_url' => "https://www.youtube.com/embed/{$videoId}?autoplay=1&enablejsapi=1",
                        'title' => $title
                    ]);
                }

            } catch (\Exception $e) {
                \Log::error('YouTube search attempt failed', [
                    'query' => $searchQuery,
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }

        // No embeddable video found after all attempts
        \Log::warning('No embeddable YouTube video found', ['original_query' => $query]);
        return response()->json(['video_id' => null]);
    }
}
