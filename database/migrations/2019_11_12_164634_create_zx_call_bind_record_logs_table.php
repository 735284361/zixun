<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZxCallBindRecordLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zx_call_bind_record_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('bind_id')->comment('绑定记录的ID');
            $table->text('content')->comment('绑定的详情');
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
        Schema::dropIfExists('zx_call_bind_record_logs');
    }
}
