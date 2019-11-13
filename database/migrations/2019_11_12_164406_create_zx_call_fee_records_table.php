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
            $table->string('direction')->comment('通话方向');
            $table->string('bind_num')->comment('隐私保护号码')->nullable();
            $table->string('session_id')->comment('通话链路的唯一标识')->nullable();
            $table->string('subscription_id')->comment('绑定ID');
            $table->string('caller_num')->comment('呼入号码')->nullable();
            $table->string('callee_num')->comment('被呼叫号码')->nullable();
            $table->timestamp('call_in_time')->comment('呼叫时间')->nullable();
            $table->timestamp('fwd_alerting_time')->comment('呼入时间')->nullable();
            $table->timestamp('fwd_answer_time')->comment('接通时间')->nullable();
            $table->timestamp('call_end_time')->comment('挂断时间')->nullable();
            $table->integer('call_time_len')->comment('通话时长')->nullable();
            $table->string('record_object_name')->comment('录音文件名')->nullable();
            $table->string('record_bucket_name')->comment('录音文件名所在的目录名')->nullable();
            $table->string('record_domain')->comment('存放录音文件的域名')->nullable();
            $table->text('content')->comment('详情数据')->nullable();

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
