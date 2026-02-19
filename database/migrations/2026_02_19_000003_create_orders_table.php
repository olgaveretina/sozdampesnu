<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('lyrics');
            $table->string('performer_name');
            $table->text('music_style');
            $table->unsignedTinyInteger('plan'); // 1, 2, 3
            $table->text('cover_description')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->string('status')->default('new');
            $table->foreignId('promo_code_id')->nullable()->constrained('promo_codes')->nullOnDelete();
            $table->string('gift_certificate_code')->nullable();
            $table->unsignedInteger('discount_amount')->default(0);
            $table->unsignedInteger('amount_paid')->default(0);
            $table->text('user_comment')->nullable();
            $table->unsignedBigInteger('selected_audio_id')->nullable();
            $table->unsignedBigInteger('selected_cover_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
