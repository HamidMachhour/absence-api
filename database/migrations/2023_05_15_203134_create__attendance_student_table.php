<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('AttendanceStudent', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('attendanceId',255);
            $table->string('studentId',255);
            $table->float('plage');
            $table->text('justification')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
            $table->unique(['attendanceId', 'studentId'], 'attendance_student_unique');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('AttendanceStudent');
    }
}
