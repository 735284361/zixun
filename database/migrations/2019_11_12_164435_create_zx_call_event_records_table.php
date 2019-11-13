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
            $table->integer('event_type')->comment('事件类型');
            $table->string('session_id');
            $table->string('caller');
            $table->string('called');
            $table->string('state_code');
            $table->string('state_desc');
            $table->string('subscription_id');
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
