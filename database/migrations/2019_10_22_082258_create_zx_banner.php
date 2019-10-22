<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZxBanner extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zx_banner', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('goods_id');
            $table->tinyInteger('sort')->default('0')->comment('排序');
            $table->text('pic_url')->comment('图片地址');
            $table->tinyInteger('status')->comment('状态');
            $table->string('title')->nullable()->comment('标题');
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
        Schema::dropIfExists('zx_banner');
    }
}
