<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_data', function (Blueprint $table) {
            $table->char('id', 20)->primary();
            $table->bigInteger('call_id');
            $table->integer('duration');
            $table->string('a_number')->comment('Incoming number');
            $table->string('b_number')->comment('Answered number');
            $table->string('status');//['AN', 'MI', 'WT', 'CT', 'CL', 'VK']//Answered, Missed, Warm Transfer, Cold Transfer, Closed, 24 calls
            $table->tinyInteger('message')->nullable();
            $table->tinyInteger('sms')->nullable();
            $table->tinyInteger('email')->nullable();
            $table->tinyInteger('booking')->nullable();
            $table->float('price');
            $table->float('provider_price')->nullable();
            $table->boolean('free_call')->nullable();
            $table->boolean('p_free_call')->nullable();
            $table->char('agent_id')->nullable();
            $table->integer('site_id');
            $table->integer('company_id');
            $table->timestamp('date');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('billing_data');
    }
}
