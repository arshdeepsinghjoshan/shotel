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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 64)->nullable();
            $table->string('address_id', 64)->nullable();
            $table->text('address_json')->nullable();
            $table->string('shipping_method', 64)->nullable();
            $table->string('payment_method', 64)->nullable();
            $table->integer('state_id')->default(1); // For order status
            $table->decimal('total_amount', 20, 6)->unsigned()->default(0);
            $table->decimal('delivery_charge', 20, 6)->unsigned()->default(0);
            $table->decimal('total_gst_amount', 20, 6)->unsigned()->default(0);
            $table->integer('order_payment_status')->default(0)->comment('0 for pending, 1 for confirmed, 2 for rejected');
            $table->decimal('total_discount', 20, 6)->unsigned()->nullable();
            $table->integer('gift_card_id')->default(0);
            $table->integer('type_id')->nullable(); // 1 = Joining, 2 = Topup, 3 = Repurchase
            $table->integer('table_id')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->nullable(); // Assumes `users` table exists
            $table->string('merchant_transaction_id', 64)->nullable();
            $table->string('merchant_user_id', 64)->nullable();
            $table->integer('created_by_id')->nullable();
            $table->integer('warehouse_id')->default(1);
            $table->timestamps();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->index('order_number');
            $table->index('created_by_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('orders');
    }
};
