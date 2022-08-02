<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->integer('call_id')->unique()->comment('key:garpnum');
            $table->string('accepted_call_id')->unique()->nullable()->comment('key:callid');;
            $table->string('agent_id')->nullable()->comment('Servit user id, key:agentid');
            $table->string('aid')->comment('Incoming a-number');
            $table->string('bid')->comment('B-number that the customer calls');
            $table->string('cid')->nullable()->comment('C-number that we connect (administrator).');
            $table->char('calid')->comment('Calendar id');
            $table->integer('site_number')->nullable()->comment('Place of answer, key:siteno');
            $table->integer('company_number')->nullable()->comment('Company ID, key:companyno');
            $table->string('xid')->nullable()->comment('Numbers that administrators have linked on to');
            $table->integer('xresult')->nullable()->comment('Results of the conference');

            $table->timestamp('started_at')->comment('key:startpts');
            $table->timestamp('connected_at')->nullable()->comment('key:connectpts');
            $table->timestamp('hang_up_at')->comment('key:hangpts');
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
        Schema::dropIfExists('calls');
    }
}
