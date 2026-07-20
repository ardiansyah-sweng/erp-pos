<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transaction')->cascadeOnDelete();
            $table->string('return_code')->unique();
            $table->text('reason')->nullable();
            $table->integer('total_refund')->default(0);
            $table->timestamps();
        });

        Schema::create('return_transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_transaction_id')->constrained('return_transactions')->cascadeOnDelete();
            $table->foreignId('transaction_detail_id')->constrained('transaction_detail')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity');
            $table->integer('price');
            $table->integer('amount');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_transaction_details');
        Schema::dropIfExists('return_transactions');
    }
};
