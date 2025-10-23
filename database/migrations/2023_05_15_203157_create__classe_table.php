<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClasseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Classe', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('nameFr')->nullable();
            $table->string('nameEn')->nullable();
            $table->string('nameZ')->nullable();
            
            $table->text('description')->nullable();
            $table->string('tags')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('userId',255); // admin user id
            $table->string('imageId',255)->nullable();
            $table->string('subscriptionId',255)->nullable(); // subscription plan id

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
        Schema::dropIfExists('Classe');
    }
}
