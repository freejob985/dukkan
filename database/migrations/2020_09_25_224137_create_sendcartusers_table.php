<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSendcartusersTable extends Migration
{

    public function up()
    {
        Schema::create('sendcartusers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->integer('quantity');
            $table->integer('price');
            $table->integer('tax')->nullable();
            $table->integer('digital')->nullable();
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('sendcartusers');
    }
}
