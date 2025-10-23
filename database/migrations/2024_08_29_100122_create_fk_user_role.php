<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('UserRole', function (Blueprint $table) {
            $table->foreign('userId')->references('id')->on('_User')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('roleId')->references('id')->on('_Role')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('UserRole', function (Blueprint $table) {
            $table->dropForeign(['userId']);
            $table->dropForeign(['roleId']);
        });
    }
};
