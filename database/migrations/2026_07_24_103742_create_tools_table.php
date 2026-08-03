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
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline');
            $table->text('description');
            $table->string('website_url');
            $table->string('github_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('app_store_url')->nullable();
            $table->string('play_store_url')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('banner_url')->nullable();
            $table->json('gallery')->nullable();
            $table->json('categories');
            $table->string('pricing');
            $table->json('platforms');
            $table->unsignedSmallInteger('founded_year')->nullable();
            $table->unsignedSmallInteger('first_release_year')->nullable();
            $table->string('headquarters')->nullable();
            $table->string('headcount')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools');
    }
};
