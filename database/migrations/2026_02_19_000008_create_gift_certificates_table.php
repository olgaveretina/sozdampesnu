<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gift_certificates', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->unsignedInteger('amount_rub');
            $table->boolean('is_used')->default(false);
            $table->foreignId('buyer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('used_by_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_certificates');
    }
};
