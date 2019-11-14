<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_account', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unique();
            $table->double('account',10,2)->default(0)->comment('账户余额');
            $table->double('account_waiting',10,2)->default(0)->comment('结算中');
            $table->double('account_withdraw',10,2)->default(0)->comment('提现中');
            $table->double('duration',10)->default(0)->comment('咨询时长(分钟)');
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
        Schema::dropIfExists('users_account');
    }
}
