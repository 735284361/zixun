<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersChildTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_sub', function (Blueprint $table) {
            $table->bigInteger('uid')->comment('与users表的uid关联');
            $table->string('session_key')->nullable()->comment('小程序的session_key');
            $table->string('open_id')->unique()->comment('小程序的open_id');
            $table->tinyInteger('since_from')->comment('注册来源');
            $table->rememberToken();
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
        Schema::dropIfExists('users_sub');
    }
}
