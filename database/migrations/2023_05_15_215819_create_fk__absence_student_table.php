<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFkAbsenceStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('AbsenceStudent', function (Blueprint $table) {
            $table->foreign('absenceId')->references('id')->on('Absence')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('studentId')->references('id')->on('Student')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('AbsenceStudent', function (Blueprint $table) {
            $table->dropForeign(['absenceId']);
            $table->dropForeign(['studentId']);
        });
    }
}
