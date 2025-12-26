<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DjProfile;
use Illuminate\Http\Request;


class PlaylistWidgetController extends Controller
{
    /**
     * Get DJ's public playlists for widget
     */
    public function getPlaylists($djId)
    {
        $djProfile = DjProfile::with('user')->findOrFail($djId);

        if (!$djProfile->spotify_playlists) {
            return response()->json([]);
        }

        // spotify_playlists is already cast to array in the model
        $playlistIds = $djProfile->spotify_playlists;

        if (empty($playlistIds)) {
            return response()->json([]);
        }

        $playlists = collect($playlistIds)->map(function ($id) {
            return [
                'id' => $id,
            ];
        });

        return response()->json($playlists);
    }

    /**
     * Serve widget JavaScript
     */
    public function serveWidget($djId)
    {
        $djProfile = DjProfile::findOrFail($djId);

        $js = <<<JS
(function() {
    const widgetDiv = document.getElementById('dj-council-widget-{$djId}');
    if (!widgetDiv) return;

    // Fetch playlist data
    fetch('/api/dj/{$djId}/playlists')
        .then(res => res.json())
        .then(playlists => {
            if (playlists.length === 0) {
                widgetDiv.innerHTML = '<div style="text-align:center;padding:20px;color:#888;">No playlists available</div>';
                return;
            }

            let html = '<div style="background:#1a1a1a;border-radius:8px;padding:20px;font-family:Arial,sans-serif;">';
            html += '<h3 style="color:white;margin-bottom:20px;">My Playlists</h3>';

            playlists.forEach(playlist => {
                html += '<div style="margin-bottom:20px;">';
                html += '<iframe src="https://open.spotify.com/embed/playlist/' + playlist.id + '" ';
                html += 'width="100%" height="380" frameborder="0" allowtransparency="true" allow="encrypted-media"></iframe>';
                html += '</div>';
            });

            html += '<div style="text-align:center;margin-top:15px;">';
            html += '<a href="/" target="_blank" style="color:#888;font-size:12px;text-decoration:none;">Powered by DJ Council</a>';
            html += '</div></div>';

            widgetDiv.innerHTML = html;
        })
        .catch(err => {
            console.error('DJ Council Widget Error:', err);
            widgetDiv.innerHTML = '<div style="text-align:center;padding:20px;color:#888;">Failed to load playlists</div>';
        });
})();
JS;

        return response($js)
            ->header('Content-Type', 'application/javascript')
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
