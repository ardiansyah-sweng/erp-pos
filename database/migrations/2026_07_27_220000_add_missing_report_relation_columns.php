<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('transaction', 'customer_id')) {
            Schema::table('transaction', function (Blueprint $table) {
                $table->unsignedBigInteger('customer_id')->nullable()->index();
            });
        }

        if (!Schema::hasColumn('products', 'category_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('category_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('transaction', 'customer_id')) {
            Schema::table('transaction', function (Blueprint $table) {
                $table->dropIndex(['customer_id']);
                $table->dropColumn('customer_id');
            });
        }

        if (Schema::hasColumn('products', 'category_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropIndex(['category_id']);
                $table->dropColumn('category_id');
            });
        }
    }
};
