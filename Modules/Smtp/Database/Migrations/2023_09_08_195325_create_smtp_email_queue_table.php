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
        Schema::dropIfExists('smtp_email_queues');
        Schema::create('smtp_email_queues', function (Blueprint $table) {
            $table->id();
            $table->string('subject', 255)->nullable();
            $table->string('from', 128)->nullable();
            $table->string('to', 128);
            $table->string('cc', 128)->nullable();
            $table->string('bcc', 128)->nullable();
            $table->text('content')->nullable();
            $table->integer('type_id')->nullable();
            $table->integer('state_id')->default(1);
            $table->integer('attempts')->nullable();
            $table->dateTime('sent_on')->nullable();
            $table->integer('model_id')->nullable();
            $table->string('model_type', 128)->nullable();
            $table->integer('smtp_account_id')->nullable();
            $table->string('message_id', 255)->nullable();
            $table->string('re_message_id', 255)->nullable();
            $table->string('created_by_id')->nullable();
            $table->timestamps();
            $table->index('from');
            $table->index('to');
            $table->index('state_id');
            $table->index('model_type');
            $table->index('model_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('smtp_email_queues');
    }
};
