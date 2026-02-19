<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->morphs('payable');
            $table->unsignedInteger('amount'); // in rubles
            $table->string('yookassa_id')->nullable()->unique();
            $table->string('status')->default('pending'); // pending, succeeded, canceled
            $table->json('yookassa_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
