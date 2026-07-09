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
            $table->string('barcode')->nullable()->unique();
            $table->string('sku')->unique();
            $table->string('name');
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('unit')->default('pcs');
            $table->integer('selling_price')->default(0);
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