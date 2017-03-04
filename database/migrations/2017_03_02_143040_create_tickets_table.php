<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('concert_id');
            $table->unsignedInteger('order_id')->nullable();
            $table->dateTime('reserved_at')->nullable();
            $table->timestamps();

            $table->foreign('concert_id')->references('id')->on('concerts');
            $table->foreign('order_id')->references('id')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign('tickets_concert_id_foreign');
            $table->dropForeign('tickets_order_id_foreign');
        });

        Schema::dropIfExists('tickets');
    }
}
