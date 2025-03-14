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
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128)->nullable();
            $table->string('slug', 128)->nullable();
            $table->integer('state_id')->default(1);
            $table->integer('type_id')->nullable(); // 1 = Package
            $table->integer('created_by_id');
            $table->timestamps();
        });
        Schema::table('product_categories', function (Blueprint $table) {
            $table->index('name');
            $table->index('created_by_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
