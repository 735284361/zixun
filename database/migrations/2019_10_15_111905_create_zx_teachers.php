<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZxTeachers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zx_teachers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->string('name')->comment('老师姓名');
            $table->string('phone')->comment('讲师电话');
            $table->string('title')->comment('老师Title');
            $table->string('list_img_url')->comment('列表图');
            $table->string('details_img_url')->comment('列表图');
            $table->string('work_years')->comment('从业年限');
            $table->integer('original_price')->comment('原价');
            $table->integer('price')->comment('咨询价格（小时）');
            $table->string('background')->comment('背景介绍');
            $table->string('good_at_filed')->comment('擅长领域');
            $table->bigInteger('page_view')->default(0)->comment('访客量');
            $table->integer('score')->default(0)->comment('评分');
            $table->integer('number_reputation')->default(0)->comment('评分人次');
            $table->integer('duration')->default(0)->comment('咨询时长(分钟)');
            $table->integer('consultants')->default(0)->comment('咨询人数');
            $table->integer('eval_num')->default(0)->comment('评论人数');
            $table->integer('reputation')->default(100)->comment('老师信誉分');
            $table->tinyInteger('status')->default(10)->comment('状态');
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
        Schema::dropIfExists('zx_teachers');
    }
}
