<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFkAbsenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Absence', function (Blueprint $table) {
            $table->foreign('userId')->references('id')->on('User')->onUpdate('cascade')->onDelete('cascade');
                        $table->foreign('seanceId')->references('id')->on('Seance')->onUpdate('cascade');

                    });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('Absence', function (Blueprint $table) {
            $table->dropForeign(['userId']);
            $table->dropForeign(['seanceId']);
        });
      }
}
