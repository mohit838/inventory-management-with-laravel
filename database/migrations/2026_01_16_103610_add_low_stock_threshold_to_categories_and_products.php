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
        Schema::table('categories', function (Blueprint $table) {
            $table->integer('low_stock_threshold')->nullable()->after('active');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->integer('low_stock_threshold')->nullable()->after('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('low_stock_threshold');
        });
        
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('low_stock_threshold');
        });
    }
};
