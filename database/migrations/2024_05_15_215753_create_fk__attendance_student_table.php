<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFkAttendanceStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('AttendanceStudent', function (Blueprint $table) {
            $table->foreign('attendanceId')
                ->references('id')
                ->on('Attendance')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('studentId')
                ->references('id')
                ->on('Student')
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
        Schema::table('AttendanceStudent', function (Blueprint $table) {
            $table->dropForeign(['attendanceId']);
            $table->dropForeign(['studentId']);
        });
    }
}
