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
            $table->foreign('matiereId')->references('id')->on('Matiere')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('profId')->references('id')->on('_User')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('classeId')->references('id')->on('Classe')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('jourId')->references('id')->on('Jour')->onUpdate('cascade')->onDelete('cascade');

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
            $table->dropForeign(['matiereId']);
            $table->dropForeign(['profId']);
            $table->dropForeign(['classeId']);
            $table->dropForeign(['jourId']);
        });
    }
}
