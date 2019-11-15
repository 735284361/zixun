<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZxEntryBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zx_entry_bills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_no')->comment('订单号');
            $table->integer('teacher_id')->comment('讲师ID');
            $table->double('total_fee',10,2)->comment('订单金额');
            $table->double('entry_fee',10,2)->comment('入账金额');
            $table->double('commission',10,2)->comment('佣金');
            $table->tinyInteger('status')->default(10)->comment('状态');
            $table->timestamp('entry_at')->nullable()->comment('入账时间');
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
        Schema::dropIfExists('zx_entry_bills');
    }
}
