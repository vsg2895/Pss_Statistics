<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTalkTimeToDailyStatisticMeta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_statistic_meta', function (Blueprint $table) {
            $table->integer('inqtalk')->after('inqinc')->comment('Total talk time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_statistic_meta', function (Blueprint $table) {
            $table->dropColumn('inqtalk');
        });
    }
}
