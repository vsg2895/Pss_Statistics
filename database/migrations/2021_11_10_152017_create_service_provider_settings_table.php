<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceProviderSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_provider_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('service_provider_id');
            $table->string('name');
            $table->string('slug');
            $table->string('value');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['service_provider_id', 'slug'], 'unique_slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_provider_settings');
    }
}
