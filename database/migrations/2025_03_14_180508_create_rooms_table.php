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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();
            $table->text('note')->default(null);
            $table->integer('type_id')->default(0)->comment('0=>Single, 1=> Double, 2=> Vila, 3=> Delux, 4=> Super Delux');
            $table->integer('meal_type')->default(1)->comment('0=>None, 1=> All, 2=> Lunch, 3=> Dinner, 4=> Breakfast');
            $table->integer('ac_type')->default(0)->comment('0=>Non AC, 1=> AC');
            $table->integer('state_id')->default(1)->comment('0=>Incative, 1=> Open, 2=> Booked');
            $table->integer('capacity')->default(null);
            $table->decimal('price', 10, 2);
            $table->string('image')->nullable();
            $table->text('images')->nullable();
            $table->integer('created_by_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
