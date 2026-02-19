<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('edit_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->text('instructions');
            $table->string('status')->default('pending_payment'); // pending_payment, paid, in_progress, completed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edit_requests');
    }
};
