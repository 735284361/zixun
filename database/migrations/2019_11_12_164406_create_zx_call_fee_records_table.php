<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZxCallFeeRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zx_call_fee_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('event_type');
            $table->string('bind_num')->comment('隐私保护号码');
            $table->string('session_id')->comment('通话链路的唯一标识');
            $table->string('caller_num')->comment('呼入号码');
            $table->string('callee_num')->comment('被呼叫号码');
            $table->string('record_object_name')->comment('录音文件名');
            $table->string('record_bucket_name')->comment('录音文件名所在的目录名');
            $table->string('record_domain')->comment('存放录音文件的域名');
            $table->string('subscription_id')->comment('绑定ID');
            $table->text('data')->comment('详情数据');

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
        Schema::dropIfExists('zx_call_fee_records');
    }
}
