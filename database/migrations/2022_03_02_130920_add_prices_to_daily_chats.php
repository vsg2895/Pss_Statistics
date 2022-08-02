<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPricesToDailyChats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_chats', function (Blueprint $table) {
            $table->float('price')->after('department_id');
            $table->float('provider_price')->after('price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_chats', function (Blueprint $table) {
            $table->dropColumn(['price', 'provider_price']);
        });
    }
}
