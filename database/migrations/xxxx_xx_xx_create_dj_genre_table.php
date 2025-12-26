<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dj_genre', function (Blueprint $table) {
            $table->foreignId('dj_profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('genre_id')->constrained()->onDelete('cascade');
            $table->primary(['dj_profile_id', 'genre_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('dj_genre');
    }
};
