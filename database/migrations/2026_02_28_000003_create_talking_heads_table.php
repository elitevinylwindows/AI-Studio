<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('talking_heads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('avatar_id')->index();
            $table->string('title')->nullable();
            $table->text('script')->nullable();
            $table->string('voice_name')->nullable();
            $table->string('audio_path')->nullable();
            $table->string('video_path')->nullable();
            $table->string('video_url')->nullable();       // external Replicate URL
            $table->string('replicate_id')->nullable();
            $table->string('status')->default('pending');   // pending | processing | completed | failed
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('avatar_id')->references('id')->on('avatars')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('talking_heads');
    }
};
