<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFkSeanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Seance', function (Blueprint $table) {
            $table->foreign('subjectId')->references('id')->on('Subject')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('teacherId')->references('id')->on('_User')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('classeId')->references('id')->on('Classe')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('dayId')->references('id')->on('_Day')->onUpdate('cascade')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Seance', function (Blueprint $table) {
            $table->dropForeign(['subjectId']);
            $table->dropForeign(['teacherId']);
            $table->dropForeign(['classeId']);
            $table->dropForeign(['dayId']);
        });
    }
}
