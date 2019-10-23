<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZxTeachersTimes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zx_teachers_times', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('teacher_id')->comment('老师ID'); // 老师
            $table->integer('date_at')->comment('日期');
            $table->integer('start_at')->comment('时间段开始时间');
            $table->integer('end_at')->comment('时间段结束时间');
            $table->integer('status')->default(10)->comment('时间段状态');
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
        Schema::dropIfExists('zx_teachers_times');
    }
}
