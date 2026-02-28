<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('avatars', function (Blueprint $table) {
            $table->string('style')->default('realistic')->after('name');           // realistic | cartoon | 3d
            $table->string('original_image_path')->nullable()->after('image_path'); // keep the original upload
        });
    }

    public function down(): void
    {
        Schema::table('avatars', function (Blueprint $table) {
            $table->dropColumn(['style', 'original_image_path']);
        });
    }
};
