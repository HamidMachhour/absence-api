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
    public function up(): void
    {
        Schema::create('MisconductReport', function (Blueprint $table) {
            $table->id();

            $table->string('studentId',255);      // The student involved
            $table->string('seanceId',255); // Link to a specific session

            // Dynamic Data
            $table->date('reportDate')->nullable;                          // The date of the incident

            // Custom/Other Fields
            $table->text('otherActs')->nullable();                 
            $table->text('otherPropositions')->nullable();              

            // Administrative Status
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'archived'])->default('draft');

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
        Schema::dropIfExists('MisconductReport');
    }
};
