<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('SubscriptionInvoice', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('userId',255);
            $table->string('subscriptionId',255);
            $table->float('amount');
            $table->string('status');
            $table->timestamp('due_date');
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
        Schema::dropIfExists('SubscriptionInvoice');
    }
}
