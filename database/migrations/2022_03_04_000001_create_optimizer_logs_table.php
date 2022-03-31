<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptimizerLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('optimizer_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('request_id')->comment('唯一请求id');
            $table->string('api_uri')->comment('接口路径');
            $table->string('method', 50)->comment('请求方式');
            $table->decimal('millisecond', 12)->comment('执行时间（毫秒）');
            $table->decimal('business_millisecond', 12)->comment('业务执行时间（毫秒）');
            $table->json('execution_sql')->comment('执行sql');
            $table->unsignedInteger('sql_count')->comment('sql执行次数');
            $table->json('request_params')->comment('请求参数');
            $table->unsignedInteger('request_params_length')->comment('请求参数长度');
            $table->json('response_content')->comment('响应内容');
            $table->unsignedInteger('response_content_length')->comment('响应内容长度');
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
        Schema::dropIfExists('optimizer_logs');
    }
};
