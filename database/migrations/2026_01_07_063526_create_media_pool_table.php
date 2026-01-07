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
        Schema::create('media_pool', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->tinyInteger('type')->comment('1: movie, 2: tv_show');
            $table->text('description')->nullable();
            $table->integer('release_year')->nullable();
            $table->string('poster_url')->nullable();
            $table->timestamps();

            $table->index(['title', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_pool');
    }
};
