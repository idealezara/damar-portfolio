<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profile_settings', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('title');
            $table->text('biography');
            $table->string('motivation')->nullable();
            $table->string('email');
            $table->string('github_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('profile_image')->nullable();
            $table->boolean('availability_enabled')->default(true);
            $table->string('availability_text')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_settings');
    }
};
