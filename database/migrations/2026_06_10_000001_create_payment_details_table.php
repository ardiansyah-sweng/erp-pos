<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transaction')->cascadeOnDelete();
            $table->string('payment_method');
            $table->bigInteger('amount');
            $table->string('reference_number')->nullable();
            $table->string('payment_status')->default('success');
            $table->bigInteger('discount_amount')->default(0);
            $table->bigInteger('cash_tendered')->default(0);
            $table->bigInteger('change_amount')->default(0);
            $table->timestamps();

            $table->index(['transaction_id', 'payment_method']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_details');
    }
};
