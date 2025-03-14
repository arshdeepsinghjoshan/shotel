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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 64)->nullable();
            $table->string('shipping_address', 64)->nullable();
            $table->text('product_json')->nullable();
            $table->text('address_json')->nullable();
            $table->string('shipping_method', 64)->nullable();
            $table->string('payment_method', 64)->nullable();
            $table->integer('state_id')->default(1);
            $table->integer('order_status')->default(0);
            $table->integer('product_id')->default(0);
            $table->decimal('quantity', 10, 2)->nullable(); 
            $table->decimal('unit_quantity', 10, 2)->nullable(); 
            $table->integer('warehouse_id')->nullable();
            $table->decimal('total_amount', 20, 6)->unsigned()->default(0);
            $table->decimal('unit_amount', 20, 6)->unsigned()->default(0);
            $table->decimal('total_discount', 20, 6)->unsigned()->default(0);
            $table->decimal('unit_discount', 20, 6)->unsigned()->default(0);
            $table->decimal('total_gst_amount', 20, 6)->unsigned()->default(0);
            $table->decimal('unit_gst_amount', 20, 6)->unsigned()->default(0);
            $table->integer('type_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->integer('created_by_id')->nullable();
            $table->timestamps();

            // Additional indexes for optimization
            $table->index('order_id');
            $table->index('product_id');
            $table->index('state_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('order_items');
    }
};
