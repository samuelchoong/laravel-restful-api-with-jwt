<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->text('uri')->nullable();
            $table->text('method')->nullable();
            $table->text('request_headers')->nullable();
            $table->text('request_body')->nullable();
            $table->text('response')->nullable();
            $table->text('request_ip')->nullable();
            $table->text('server_ip')->nullable();
            $table->text('device')->nullable();
            $table->timestamps(6);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_logs');
    }
}
