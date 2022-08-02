<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_statistics', function (Blueprint $table) {
            $table->id();
            $table->string('servit_user_id');
            $table->string('daily_calls');
            $table->string('daily_bookings');
            $table->string('daily_login_time');
            $table->date('date');
            $table->timestamps();

            $table->unique(['servit_user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_statistics');
    }
}
