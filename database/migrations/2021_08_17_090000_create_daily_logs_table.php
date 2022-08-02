<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_logs', function (Blueprint $table) {
            $table->id();
            $table->string('agent_id');
            $table->char('time_range');
            $table->integer('login_minutes');
            $table->timestamp('logon_time')->nullable();
            $table->timestamp('logoff_time')->nullable();
            $table->date('date');
            $table->timestamp('created_at');

            $table->index('agent_id');
            $table->index('time_range');
            $table->unique(['agent_id', 'time_range', 'date', 'logon_time', 'logoff_time'], 'user_log_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_logs');
    }
}
