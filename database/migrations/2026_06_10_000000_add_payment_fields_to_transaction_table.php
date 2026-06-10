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
        Schema::table('transaction', function (Blueprint $table) {
            $table->string('payment_method')->default('cash')->after('total');
            $table->bigInteger('discount_amount')->default(0)->after('payment_method');
            $table->bigInteger('cash_tendered')->default(0)->after('discount_amount');
            $table->bigInteger('change_amount')->default(0)->after('cash_tendered');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'discount_amount',
                'cash_tendered',
                'change_amount',
            ]);
        });
    }
};
