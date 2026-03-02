<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_type')->default('song')->after('plan');
            $table->string('video_audio_path')->nullable()->after('cover_image_path');
            $table->text('singer_description')->nullable()->after('video_audio_path');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_type', 'video_audio_path', 'singer_description']);
        });
    }
};
