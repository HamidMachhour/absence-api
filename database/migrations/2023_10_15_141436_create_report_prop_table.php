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
        Schema::create('ReportProposedAction', function (Blueprint $table) {
            $table->unsignedBigInteger('reportId');
            $table->unsignedBigInteger('proposedActionId');
            $table->primary(['reportId', 'proposedActionId'], 'report_proposition_primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ReportProposedAction');
    }
};
