<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dj_profiles', function (Blueprint $table) {
            $table->string('spotify_id')->nullable()->after('website_url');
            $table->text('spotify_access_token')->nullable();
            $table->text('spotify_refresh_token')->nullable();
            $table->timestamp('spotify_token_expires_at')->nullable();
            $table->json('spotify_playlists')->nullable(); // Store selected playlist IDs
        });
    }

    public function down(): void
    {
        Schema::table('dj_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'spotify_id',
                'spotify_access_token',
                'spotify_refresh_token',
                'spotify_token_expires_at',
                'spotify_playlists',
            ]);
        });
    }
};
