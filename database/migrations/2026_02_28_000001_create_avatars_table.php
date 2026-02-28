<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avatars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('name');
            $table->string('image_path');              // e.g. avatars/abc123.png
            $table->string('thumbnail_path')->nullable();
            $table->string('gender')->nullable();      // male | female | neutral
            $table->json('tags')->nullable();           // ["Professional","Lifestyle"]
            $table->boolean('is_public')->default(false);
            $table->string('status')->default('active'); // active | processing | failed
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avatars');
    }
};
