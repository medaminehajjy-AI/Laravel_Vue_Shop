<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if foreign keys already exist using information_schema
        $orderFkExists = DB::select("
            SELECT * FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'order_items' 
            AND COLUMN_NAME = 'order_id' 
            AND REFERENCED_TABLE_NAME = 'orders'
        ");

        if (empty($orderFkExists)) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->unsignedBigInteger('order_id')->change();
                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            });
        }

        // Check if foreign key for product_id already exists
        $productFkExists = DB::select("
            SELECT * FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'order_items' 
            AND COLUMN_NAME = 'product_id' 
            AND REFERENCED_TABLE_NAME = 'products'
        ");

        if (empty($productFkExists)) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->unsignedBigInteger('product_id')->change();
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            try {
                $table->dropForeign(['order_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist
            }
            try {
                $table->dropForeign(['product_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist
            }
        });
    }
};
