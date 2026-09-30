<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Seance', function (Blueprint $table) {
            
            $now=new DateTime();

            $table->uuid('id')->primary();
            $table->string('subjectId',255);
            $table->string('teacherId',255);
            $table->string('classeId',255);
            $table->unsignedBigInteger('dayId');
            $table->text('salle')->nullable();
            $table->time('de')->default('08:00')->nullable();
            $table->time('a')->default('09:00')->nullable();

             $table->timestamp('deleted_at')->nullable();;
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
        Schema::dropIfExists('Seance');
    }
}
