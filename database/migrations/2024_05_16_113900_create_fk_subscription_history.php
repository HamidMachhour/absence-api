<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFkSubscriptionHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('SubscriptionHistory', function (Blueprint $table) {
            $table->foreign('subscriptionId')->references('id')->on('Subscription')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('SubscriptionHistory', function (Blueprint $table) {
            $table->dropForeign(['subscriptionId']);
        });
    }
}
