<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('Message', function (Blueprint $table) {

            $table->foreign('s_user_id')->references('id')->on('_User')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('d_user_id')->references('id')->on('_User')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('reply_to')->references('id')->on('Message')->onUpdate('cascade')->onDelete('set null');    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('Message', function (Blueprint $table) {
            $table->dropForeign(['s_user_id']);
            $table->dropForeign(['d_user_id']);
            $table->dropForeign(['reply_to']);
        }); 
    }
};