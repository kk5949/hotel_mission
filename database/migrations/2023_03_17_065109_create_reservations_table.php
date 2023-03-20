<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->integer("user_id");
            $table->integer("hotel_id");
            $table->integer("step")->default("10")->comment("10:진행중 20:예약완료 30:예약반려 40:예약취소");
            $table->timestamps();

            $table->index("user_id");
            $table->index("hotel_id");
            $table->index("step");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservations');
    }
}
