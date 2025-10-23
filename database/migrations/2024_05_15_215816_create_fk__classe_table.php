<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFkClasseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Classe', function (Blueprint $table) {
            $table->foreign('userId')->references('id')->on('_User')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('imageId')->references('id')->on('ClasseImage')->onDelete('set null');
            $table->foreign('subscriptionId')->references('id')->on('Subscription')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('Classe', function (Blueprint $table) {
            $table->dropForeign(['userId']);
            $table->dropForeign(['imageId']);
            $table->dropForeign(['subscriptionId']);
        });
    }
}
