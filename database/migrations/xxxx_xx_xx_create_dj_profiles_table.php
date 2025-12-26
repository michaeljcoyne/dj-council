<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dj_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('stage_name');
            $table->string('specialty')->nullable();
            $table->text('bio')->nullable();
            $table->string('experience')->nullable();
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->integer('minimum_booking_hours')->default(2);
            $table->string('profile_image')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('soundcloud_url')->nullable();
            $table->string('website_url')->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dj_profiles');
    }
};
