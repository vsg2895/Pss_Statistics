<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyStatisticMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_statistic_meta', function (Blueprint $table) {
            $table->id();
            $table->integer('daily_statistic_id')->unique();
            $table->integer('inqready')->comment('Total time ready');
            $table->integer('inqinc')->comment('Total time for incoming calls');
            $table->integer('inqbusy')->comment('Total time as busy');
            $table->integer('inqwrap')->comment('Total time in finishing');
            $table->integer('inqpause')->comment('Total time off');
            $table->integer('inqring')->comment('Total call time');
            $table->integer('repbusy')->comment('Number of connection attempts that resulted in busy');
            $table->integer('repnorep')->comment('Number of connection attempts that resulted in no response');
            $table->integer('xferout')->comment('Number of diverted calls (Blind)');
            $table->integer('confout')->comment('Number of diverted calls (Conference)');
            $table->integer('confmiss')->comment('Number of failed conferencing conferences');
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
        Schema::dropIfExists('daily_statistic_meta');
    }
}
