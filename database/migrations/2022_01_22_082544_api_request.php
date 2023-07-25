<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ApiRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_request', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id')->nullable();
            $table->integer('status_code')->nullable();
            $table->integer('http_status_code')->nullable();
            $table->boolean('is_time_out')->nullable();
            $table->timestamps();

            $table->foreign('request_id')->references('id')->on('api_request_logs')->onDelete('cascade');
            $table->index(['request_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_request');
    }
}
