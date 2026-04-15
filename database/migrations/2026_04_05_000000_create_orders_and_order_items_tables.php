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
        // Check if orders table exists
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->decimal('total_price', 10, 2);
                $table->enum('status', ['pending', 'paid', 'shipped', 'delivered'])->default('pending');
                $table->boolean('is_read')->default(false);
                $table->string('phone')->nullable();
                $table->text('shipping_address')->nullable();
                $table->text('notes')->nullable();
                $table->enum('payment_method', ['cod', 'online'])->default('online');
                $table->timestamps();
            });
        } else {
            // Add missing columns to existing orders table
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'phone')) {
                    $table->string('phone')->nullable()->after('is_read');
                }
                if (!Schema::hasColumn('orders', 'shipping_address')) {
                    $table->text('shipping_address')->nullable()->after('phone');
                }
                if (!Schema::hasColumn('orders', 'notes')) {
                    $table->text('notes')->nullable()->after('shipping_address');
                }
                if (!Schema::hasColumn('orders', 'payment_method')) {
                    $table->enum('payment_method', ['cod', 'online'])->default('online')->after('notes');
                }
            });
        }

        // Check if order_items table exists
        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->integer('quantity');
                $table->decimal('price', 10, 2);
                $table->timestamps();
            });
        } else {
            // Add missing timestamps to existing order_items table
            Schema::table('order_items', function (Blueprint $table) {
                if (!Schema::hasColumn('order_items', 'created_at')) {
                    $table->timestamp('created_at')->nullable()->after('price');
                }
                if (!Schema::hasColumn('order_items', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable()->after('created_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
