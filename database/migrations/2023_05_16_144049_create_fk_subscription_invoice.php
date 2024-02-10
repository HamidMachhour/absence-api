<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFkSubscriptionInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('SubscriptionInvoice', function (Blueprint $table) {
            $table->foreign('subscriptionId')->references('id')->on('Subscription')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('userId')->references('id')->on('User')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('SubscriptionInvoice', function (Blueprint $table) {
            $table->dropForeign(['subscriptionId']);
            $table->dropForeign(['userId']);
        });
    }
}
