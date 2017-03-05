<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserChatFkMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('messages', function ($table) {
        $table->foreign('user_id')->references('id')->on('users');
        $table->foreign('chat_id')->references('id')->on('chats');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('messages', function ($table) {
        $table->dropForeign(['user_id']);
        $table->dropForeign(['chat_id']);
      });
    }
}
