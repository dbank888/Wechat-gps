<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('codes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0)->comment('用户ID');
            $table->enum('type', ['day', 'week', 'month', 'year'])->comment('类型');
            $table->string('code')->comment('授权码');
            $table->tinyInteger('status')->default(1)->comment('使用状态');
            $table->timestamp('used_at')->nullable()->comment('使用时间');
            $table->timestamp('expired_at')->nullable()->comment('过期时间');
            $table->integer('created_user_id')->default(0)->comment('创建者用户ID');
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
        Schema::dropIfExists('codes');
    }
}
