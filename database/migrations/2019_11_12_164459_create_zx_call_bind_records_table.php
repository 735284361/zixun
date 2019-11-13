<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZxCallBindRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zx_call_bind_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_no')->comment('订单号');
            $table->string('caller_num')->comment('A号码');
            $table->string('relation_num')->comment('X号码');
            $table->string('callee_num')->comment('B号码');
            $table->integer('duration')->comment('自动解绑时长');
            $table->integer('max_duration')->comment('最大通话时长');
            $table->integer('resultcode')->comment('绑定结果');
            $table->string('resultdesc')->comment('绑定结果描述');
            $table->string('subscription_id')->comment('绑定ID');
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
        Schema::dropIfExists('zx_call_bind_records');
    }
}
