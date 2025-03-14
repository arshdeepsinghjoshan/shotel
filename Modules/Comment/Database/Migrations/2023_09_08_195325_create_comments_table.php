<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('comments');
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->integer('model_id');
            $table->string('model_type', 255);
            $table->text('comment')->nullable();
            $table->integer('state_id')->default(1);
            $table->integer('type_id')->default(0);
            $table->string('created_by_id')->nullable();
            $table->timestamps();
            $table->index('model_id');
            $table->index('model_type');
            $table->index('comment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
};


