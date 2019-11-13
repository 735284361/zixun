<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZxCallEventRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zx_call_event_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('event_type')->comment('事件类型');
            $table->string('session_id')->nullable();
            $table->string('caller')->nullable();
            $table->string('called')->nullable();
            $table->string('state_code')->nullable();
            $table->string('state_desc')->nullable();
            $table->string('subscription_id')->nullable();
            $table->string('event_at')->comment('事件时间戳');
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
        Schema::dropIfExists('zx_call_event_records');
    }
}
