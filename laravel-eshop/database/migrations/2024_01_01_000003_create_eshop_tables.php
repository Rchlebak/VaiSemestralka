<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migrácia pre e-shop databázové tabuľky
 * Zachováva pôvodnú štruktúru s DECIMAL ID (ako požadoval používateľ)
 */
return new class extends Migration
{
    public function up(): void
    {
        // Products Table
        Schema::create('products', function (Blueprint $table) {
            $table->decimal('product_id', 10, 2)->primary();
            $table->string('sku_model', 84)->nullable();
            $table->string('name', 200);
            $table->string('brand', 200)->nullable();
            $table->string('gender', 10)->default('unisex');
            $table->decimal('base_price', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->integer('is_active')->default(1);
        });

        // Product Variants Table
        Schema::create('product_variants', function (Blueprint $table) {
            $table->decimal('variant_id', 10, 2)->primary();
            $table->decimal('product_id', 10, 2);
            $table->string('sku', 24)->nullable();
            $table->string('color', 50)->nullable();
            $table->string('size_eu', 4)->nullable();
            $table->integer('is_active')->default(1);

            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('products')
                  ->onDelete('cascade');
        });

        // Inventory Table
        Schema::create('inventory', function (Blueprint $table) {
            $table->decimal('variant_id', 10, 2)->primary();
            $table->integer('stock_qty')->default(0);

            $table->foreign('variant_id')
                  ->references('variant_id')
                  ->on('product_variants')
                  ->onDelete('cascade');
        });

        // Product Images Table
        Schema::create('product_images', function (Blueprint $table) {
            $table->id('image_id');
            $table->decimal('product_id', 10, 2);
            $table->string('image_path', 500);
            $table->tinyInteger('is_main')->default(0);
            $table->integer('sort_order')->default(0);

            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('products')
                  ->onDelete('cascade');
        });

        // Users Table (rozšírená)
        Schema::create('eshop_users', function (Blueprint $table) {
            $table->decimal('user_id', 10, 2)->primary();
            $table->string('email', 255);
            $table->string('name', 200);
            $table->string('phone', 40)->nullable();
        });

        // Orders Table
        Schema::create('orders', function (Blueprint $table) {
            $table->decimal('order_id', 10, 2)->primary();
            $table->decimal('user_id', 10, 2)->nullable();
            $table->string('email', 255);
            $table->string('status', 20)->default('pending');
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->string('ship_name', 200)->nullable();
            $table->string('ship_street', 200)->nullable();
            $table->string('ship_city', 200)->nullable();
            $table->string('ship_zip', 20)->nullable();
            $table->string('ship_country', 20)->nullable();

            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('eshop_users')
                  ->onDelete('set null');
        });

        // Order Items Table
        Schema::create('order_items', function (Blueprint $table) {
            $table->decimal('order_item_id', 10, 2)->primary();
            $table->decimal('order_id', 10, 2);
            $table->decimal('variant_id', 10, 2)->nullable();
            $table->integer('qty')->default(1);
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->decimal('line_total', 10, 2)->nullable();

            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('orders')
                  ->onDelete('cascade');

            $table->foreign('variant_id')
                  ->references('variant_id')
                  ->on('product_variants')
                  ->onDelete('set null');
        });

        // Payments Table
        Schema::create('payments', function (Blueprint $table) {
            $table->decimal('payment_id', 10, 2)->primary();
            $table->decimal('order_id', 10, 2);
            $table->string('provider', 40)->nullable();
            $table->string('status', 20)->default('pending');
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('reference', 100)->nullable();

            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('orders')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('eshop_users');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('inventory');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
    }
};

