<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Client playlists table
        Schema::create('client_playlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['must_play', 'nice_to_have', 'do_not_play', 'general'])->default('general');
            $table->boolean('is_public')->default(false);
            $table->timestamps();
        });

        // Client playlist songs table
        Schema::create('client_playlist_songs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_playlist_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('artist');
            $table->string('album')->nullable();
            $table->integer('duration')->nullable(); // in seconds
            $table->string('spotify_id')->nullable();
            $table->string('spotify_uri')->nullable();
            $table->string('image')->nullable(); // Album artwork URL
            $table->string('preview_url')->nullable(); // 30s preview URL
            $table->integer('position')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['client_playlist_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_playlist_songs');
        Schema::dropIfExists('client_playlists');
    }
};
