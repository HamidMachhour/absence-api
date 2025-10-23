<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFkUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('_User', function (Blueprint $table) {
            $table->foreign('addressId')->references('id')->on('Address')->onUpdate('cascade');
            $table->foreign('imageId')->references('id')->on('UserImage')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('_User', function (Blueprint $table) {
            $table->dropForeign(['addressId']);
            $table->dropForeign(['imageId']);
        });
    }
}
