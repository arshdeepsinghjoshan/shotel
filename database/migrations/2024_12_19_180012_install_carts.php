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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_price', 20, 6)->unsigned()->default(0);
            $table->decimal('unit_price', 20, 6)->unsigned()->default(0);
            $table->decimal('shipping_price', 20, 6)->unsigned()->default(0);
            $table->decimal('tax_price', 20, 6)->unsigned()->default(0);
            $table->decimal('quantity', 20, 6)->default(0);
            $table->integer('state_id')->default(1); // For order status
            $table->integer('product_id')->default(0); // For order status
            $table->string('custom_product')->default(0); // For order status
            $table->decimal('delivery_charge', 20, 6)->unsigned()->default(0);
            $table->decimal('total_gst_amount', 20, 6)->unsigned()->default(0);
            $table->decimal('unit_gst_amount', 20, 6)->unsigned()->default(0);
            $table->decimal('total_discount', 20, 6)->unsigned()->nullable();
            $table->integer('type_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('created_by_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('carts');
    }
};
