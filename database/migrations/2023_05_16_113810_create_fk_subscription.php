<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFkSubscription extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Subscription', function (Blueprint $table) {
            $table->foreign('userId')->references('id')->on('User')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('planId')->references('id')->on('Plan')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Subscription', function (Blueprint $table) {
            $table->dropForeign(['userId']);
            $table->dropForeign(['planId']);
        });
    }
}
