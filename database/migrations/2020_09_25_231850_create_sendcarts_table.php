<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSendcartsTable extends Migration
{

    public function up()
    {
        Schema::create('sendcarts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sender_id');
            $table->integer('total');
            $table->string('qr');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sendcarts');
    }
}
