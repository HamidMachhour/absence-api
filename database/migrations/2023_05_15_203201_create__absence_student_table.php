<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsenceStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('AbsenceStudent', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('studentId',255);
            $table->string('absenceId',255);
            $table->float('plage');
            $table->text('justification')->nullable();
            $table->text('deleted_at')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('AbsenceStudent');
    }
}
