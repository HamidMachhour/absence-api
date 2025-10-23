<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('SubscriptionHistory', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('subscriptionId',255);
            $table->string('action'); //(e.g., renewal, upgrade, downgrade, cancellation)
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
        Schema::dropIfExists('SubscriptionHistory');
    }
}
