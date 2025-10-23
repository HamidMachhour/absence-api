<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('_User', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('firstName');
            $table->string('lastName');
            $table->string('firstNameFr')->nullable();
            $table->string('lastNameFr')->nullable();
            $table->string('firstNameEn')->nullable();
            $table->string('lastNameEn')->nullable();
            $table->string('firstNameZ')->nullable();
            $table->string('lastNameZ')->nullable();
            $table->string('code')->nullable();
            $table->string('email')->unique();
            $table->string('tag_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamp('deleted_at')->nullable();
            $table->string('addressId',255)->nullable();
            $table->string('imageId',255)->nullable();
            $table->timestamps();
        });

        // Add the unique index after the table is created
        // This is the key part to allow nulls
        Schema::table('_User', function (Blueprint $table) {
            $table->unique('tag_id');
            $table->unique('code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('_User');
    }
}
