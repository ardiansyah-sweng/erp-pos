<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('discounts', function (Blueprint $table) {
      $table->id();
      $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
      $table->string('name');
      $table->enum('type', ['percentage', 'fixed']);
      $table->integer('value');
      $table->date('start_date');
      $table->date('end_date');
      $table->boolean('is_active')->default(true);
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('discounts');
  }
};
