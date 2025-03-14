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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128)->nullable();
            $table->text('description')->nullable();
            $table->decimal('quantity_in_stock', 10, 2); 
            $table->decimal('remaining_quantity', 10, 2);            
            $table->decimal('price', 20, 6)->unsigned()->default(0);
            $table->decimal('distribution_price', 20, 6)->unsigned()->default(0);
            $table->integer('state_id')->default(1);
            $table->integer('type_id')->nullable(); // 1 = Package
            $table->string('image')->nullable();
            $table->text('images')->nullable();
            $table->integer('category_id')->nullable();
            $table->integer('tax_id')->nullable();
            $table->integer('created_by_id');
            $table->timestamp('bill_date');
            $table->timestamp('expiry_date')->nullable();
            $table->timestamps();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->index('name');
            $table->index('created_by_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('products');
    }
};
