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
            $table->string('title')->comment('老师Title');
            $table->string('list_img_url')->comment('列表图');
            $table->string('details_img_url')->comment('列表图');
            $table->string('work_years')->comment('从业年限');
            $table->integer('original_price')->comment('原价');
            $table->integer('price')->comment('咨询价格（小时）');
            $table->string('background')->comment('背景介绍');
            $table->string('good_at_filed')->comment('擅长领域');
            $table->integer('page_view')->comment('访客量');
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
