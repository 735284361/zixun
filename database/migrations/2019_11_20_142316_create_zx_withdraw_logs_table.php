<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZxWithdrawLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zx_withdraw_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->comment('提现申请人');
            $table->integer('withdraw_id')->comment('提现记录ID');
            $table->string('remark')->comment('备注');
            $table->tinyInteger('status')->comment('提现的状态');
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
        Schema::dropIfExists('zx_withdraw_logs');
    }
}
