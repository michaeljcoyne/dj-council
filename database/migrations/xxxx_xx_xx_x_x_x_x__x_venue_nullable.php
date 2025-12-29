<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            // Make these fields nullable for flexibility
            $table->string('state')->nullable()->change();
            $table->string('postal_code')->nullable()->change();
            $table->string('country')->nullable()->default('GB')->change();
            $table->string('phone')->nullable()->change();
            $table->text('notes')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->string('state')->nullable(false)->change();
            $table->string('postal_code')->nullable(false)->change();
            $table->string('country')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
            $table->text('notes')->nullable(false)->change();
        });
    }
};
