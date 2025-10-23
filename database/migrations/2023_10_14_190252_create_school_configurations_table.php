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
        Schema::create('school_configurations', function (Blueprint $table) {
            $table->id();
            // Core Identity
            $table->string('name', 255);             // اسم المؤسسة (Arabic)
            $table->string('name_fr', 255)->nullable(); // Nom de l'établissement (French)
            $table->string('name_en', 255)->nullable(); // Nom de l'établissement (english)
            $table->string('name_z', 255)->nullable(); // Nom de l'établissement (tifinagh)

            $table->string('code', 50)->unique();       // Code UAR/Massar Code
            $table->string('city', 100);                // المدينة
            $table->string('address', 255)->nullable(); // العنوان

            // Contact & Administrative Info
            $table->string('director_name', 150)->nullable(); // اسم المدير
            $table->string('phone_number', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('logo_path')->nullable();             // Path to the school logo file

            // Academic Year (If not managed in a separate table)
            $table->date('current_academic_start')->nullable(); // Start date of the current academic year
            $table->date('current_academic_end')->nullable();   // End date of the current academic year


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
        Schema::dropIfExists('school_configurations');
    }
};
