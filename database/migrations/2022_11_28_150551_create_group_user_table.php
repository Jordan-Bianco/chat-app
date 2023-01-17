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
        Schema::create('group_user', function (Blueprint $table) {
            $table->primary(['group_id', 'user_id']);
            $table->uuid('group_id')->constrained('groups', 'id')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->enum('role', ['Leader', 'User'])->default('User');
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
        Schema::dropIfExists('group_user');
    }
};
