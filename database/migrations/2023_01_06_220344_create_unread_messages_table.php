<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unread_messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('group_id')->nullable()->constrained('groups', 'id')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignId('message_id')->constrained('messages', 'id')->cascadeOnDelete();
            $table->foreignId('from')->nullable()->constrained('users', 'id')->cascadeOnDelete();
            $table->boolean('is_private');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unread_messages');
    }
};
