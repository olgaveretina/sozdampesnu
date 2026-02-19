<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_upgrades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('from_plan');
            $table->unsignedTinyInteger('to_plan');
            $table->unsignedInteger('amount');
            $table->string('status')->default('pending_payment'); // pending_payment, paid
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_upgrades');
    }
};
