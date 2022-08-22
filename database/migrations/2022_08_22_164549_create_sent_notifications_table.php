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
        Schema::create('sent_notifications', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->string('type', 60);
            $table->timestamp('created_at');
            $table->string('locale', 2);
            $table->string('channel', 50);
            $table->text('route');
            $table->json('variables');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sent_notifications');
    }
};
