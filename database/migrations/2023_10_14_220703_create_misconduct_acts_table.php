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
    public function up()
    {
        Schema::create('MisconductAction', function (Blueprint $table) {
            $table->id();
            // Multilingual Fields for the Act Description
            $table->string('name_ar', 255)->comment('وصف السلوك / Arabic Name')->unique();
            $table->string('name_fr', 255)->comment('Description de l\'acte / French Name')->nullable();
            $table->string('name_en', 255)->comment('Act Description / English Name')->nullable();
            $table->string('name_z', 255)->comment('ⵜⴰⴳⵎⴰⵢⵜ / Tifinagh Name')->nullable();
             $table->timestamp('deleted_at')->nullable();;
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
        Schema::dropIfExists('MisconductAction');
    }
};
