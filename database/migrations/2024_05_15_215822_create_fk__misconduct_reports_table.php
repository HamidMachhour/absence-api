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
    public function up()
    {
        Schema::table('MisconductReport', function (Blueprint $table) {
            $table->foreign('seanceId')->references('id')->on('Seance')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('studentId')->references('id')->on('Student')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('MisconductReport', function (Blueprint $table) {
            $table->dropForeign(['seanceId']);
            $table->dropForeign(['studentId']);
        });
    }
};
