<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name');
            $table->text('address')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('currency', 3)->default('IDR');
            $table->text('receipt_footer')->nullable();
            $table->boolean('low_stock_notification')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
