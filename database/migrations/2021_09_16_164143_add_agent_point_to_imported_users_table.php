<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAgentPointToImportedUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('imported_users', function (Blueprint $table) {
            $table->integer('agent_point')->unsigned()->nullable()->default(null)->after('liveagent_username');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('imported_users', function (Blueprint $table) {
            $table->dropColumn('agent_point');
        });
    }
}
