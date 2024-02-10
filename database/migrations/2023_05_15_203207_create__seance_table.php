<?php

use Illuminate\Support\Carbon;
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
            
            $now=Carbon::now();

            $table->uuid('id')->primary();
            $table->text('matiereId');
            $table->text('profId');
            $table->text('classeId');
            $table->text('jourId');
            $table->text('salle')->nullable();
            $table->timestamp('de')->default($now)->nullable();
            $table->timestamp('a')->default($now->addHour())->nullable();;

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
        Schema::dropIfExists('Seance');
    }
}
