<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingDataFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_data_fees', function (Blueprint $table) {
            $table->id();
            $table->char('billing_id', 20)->unique();
            $table->float('calls_fee');
            $table->float('bookings_fee');
            $table->float('above_60_fee');
            $table->float('cold_transferred_calls_fee');
            $table->float('warm_transferred_calls_fee');
            $table->integer('time_above_seconds');
            $table->float('messages_fee');
            $table->float('sms_fee');
            $table->float('emails_fee');
            $table->float('p_calls_fee')->nullable();
            $table->float('p_bookings_fee')->nullable();
            $table->float('p_above_60_fee')->nullable();
            $table->float('p_cold_transferred_calls_fee')->nullable();
            $table->float('p_warm_transferred_calls_fee')->nullable();
            $table->integer('p_time_above_seconds')->nullable();
            $table->float('p_messages_fee')->nullable();
            $table->float('p_sms_fee')->nullable();
            $table->float('p_emails_fee')->nullable();
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
        Schema::dropIfExists('billing_data_fees');
    }
}
