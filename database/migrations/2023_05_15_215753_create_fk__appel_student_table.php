<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFkAppelStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('AppelStudent', function (Blueprint $table) {
            $table->foreign('appelId')
                ->references('id')
                ->on('Appel')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('studentId')
                ->references('id')
                ->on('Student')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('seanceId')
                ->references('id')
                ->on('Seance')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('AppelStudent', function (Blueprint $table) {
            $table->dropForeign(['appelId']);
            $table->dropForeign(['studentId']);
            $table->dropForeign(['seanceId']);

        });
    }
}
