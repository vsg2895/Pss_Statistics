<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFixedFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fixed_fees', function (Blueprint $table) {
//            $table->id();
            $table->char('id', 20)->primary();
            $table->integer('fee_type_id');
            $table->integer('company_id')->nullable();
            $table->integer('service_provider_id')->nullable();
            $table->decimal('fee');
            $table->date('date');
            $table->timestamps();
//            $table->unique(['company_id', 'date'], 'company_date_unique');
//            $table->unique(['service_provider_id', 'date'], 'service_provider_date_unique');
//            $table->unique(['company_id', 'service_provider_id', 'date'], 'company_s_p_date_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fixed_fees');
    }
}
