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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('shopify_order_id')->index(); // Shopify's GraphQL ID
            $table->string('name'); // Shopify order name
            $table->string('email')->nullable(); // Email may be null
            $table->timestamp('created_at_shopify'); // Shopify's createdAt
            $table->decimal('total_price', 10, 2); // amount
            $table->string('currency_code', 10); // currencyCode
            $table->timestamps(); // Laravel's created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
