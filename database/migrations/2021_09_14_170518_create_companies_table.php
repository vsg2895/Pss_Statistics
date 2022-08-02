<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->unique()->comment('companyno');
            $table->integer('siteno');
            $table->string('name')->comment('company');
            $table->string('url')->nullable();
            $table->string('orgno')->nullable();
            $table->string('city')->nullable();
            $table->timestamp('added_at')->comment('addts');
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
        Schema::dropIfExists('companies');
    }
}
