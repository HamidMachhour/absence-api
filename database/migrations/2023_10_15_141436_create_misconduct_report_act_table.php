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
        Schema::create('ReportMisconductAction', function (Blueprint $table) {
            $table->unsignedBigInteger('reportId');
            $table->unsignedBigInteger('misconductActionId');
            $table->primary(['reportId', 'misconductActionId'], 'report_act_primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ReportMisconductAction');
    }
};
