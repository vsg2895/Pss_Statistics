<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEazyChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eazy_chats', function (Blueprint $table) {
            $table->id();
            $table->char('conversation_id', 36)->unique();
            $table->integer('imported_user_id');
            $table->integer('company_id');
            $table->string('agent_email');
            $table->timestamp('date');
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
        Schema::dropIfExists('eazy_chats');
    }
}
