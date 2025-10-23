<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Subscription', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('userId',255);
            $table->string('planId',255);
            $table->timestamp('expire_at');
            $table->string('status'); //(e.g., active, expired, canceled)
            $table->timestamp('deleted_at')->nullable();
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
        Schema::dropIfExists('Subscription');
    }
}
