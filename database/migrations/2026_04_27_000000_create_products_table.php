<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->char('sku', 6)->unique();
            $table->string('barcode', 20)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('unit', 20);
            $table->bigInteger('selling_price');
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
