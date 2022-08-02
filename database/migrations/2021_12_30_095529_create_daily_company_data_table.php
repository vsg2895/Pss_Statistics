<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateDailyCompanyDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_company_data', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('service_provider_id');
            $table->string('company_name');
            $table->integer('calls_count');
            $table->integer('bookings_count');
            $table->integer('time_above_seconds_value');
            $table->integer('p_time_above_seconds_value')->default(0);
            $table->decimal('calls_fee');
            $table->decimal('p_calls_fee')->default(0);
            $table->decimal('bookings_fee');
            $table->decimal('p_bookings_fee')->default(0);
            $table->decimal('chats_fee');
            $table->decimal('p_chats_fee')->default(0);
            $table->decimal('above_60_fee');
            $table->decimal('p_above_60_fee')->default(0);
            $table->decimal('monthly_fee');
            $table->decimal('p_monthly_fee')->default(0);
            $table->decimal('free_calls');
            $table->decimal('p_free_calls')->default(0);
            $table->decimal('time_above_seconds');
            $table->decimal('p_time_above_seconds')->default(0);
            $table->decimal('cold_transferred_calls_fee');
            $table->decimal('p_cold_transferred_calls_fee')->default(0);
            $table->decimal('warm_transferred_calls_fee');
            $table->decimal('p_warm_transferred_calls_fee')->default(0);
//            $table->date('date')->default(DB::raw('NOW()'));
//            $table->date('date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->date('date');

            $table->unique(['company_id', 'date']);
            $table->index('service_provider_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_company_data');
    }
}
