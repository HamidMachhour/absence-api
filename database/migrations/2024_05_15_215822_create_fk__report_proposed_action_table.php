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
        Schema::table('ReportProposedAction', function (Blueprint $table) {
            $table->foreign('reportId')->references('id')->on('MisconductReport')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('proposedActionId')->references('id')->on('ProposedAction')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ReportProposedAction', function (Blueprint $table) {
            $table->dropForeign(['reportId']);
            $table->dropForeign(['proposedActionId']);
        });
    }
};
