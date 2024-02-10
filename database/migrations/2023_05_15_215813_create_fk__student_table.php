<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFkStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Student', function (Blueprint $table) {
            $table->foreign('classeId')->references('id')->on('Classe')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('userId')->references('id')->on('_User')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Student', function (Blueprint $table) {
            $table->dropForeign(['classeId']);
            $table->dropForeign(['userId']);
        });
    }
}
