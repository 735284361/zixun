<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZxOrdersRefuse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zx_orders_refuse', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('order_id')->unique();
            $table->integer('teacher_id')->comment('讲师ID');
            $table->string('content')->comment('拒绝的内容');
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
        Schema::dropIfExists('zx_orders_refuse');
    }
}
