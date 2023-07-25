<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('phone_no')->nullable();
            $table->longText('address')->nullable();
            $table->integer('experience')->nullable();
            $table->text('cv_url')->nullable();
            $table->text('image_url')->nullable();
            $table->timestamp('artist_verified_at')->nullable();
            $table->timestamp('user_verified_at')->nullable();
            $table->decimal('total_balance')->default(0);
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
        Schema::dropIfExists('users');
    }
}
