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
        Schema::create('Friendship', function (Blueprint $table) {
            $table->id();
             
            $table->string('userId',255);  
            $table->string('friend',255);
            $table->enum('status', ['pending', 'accepted','rejected','blocked'])->default('pending');
            $table->text('deleted_at')->nullable();
            $table->timestamps();

            // Add an index for quicker lookup of conversations
            $table->index(['userId', 'friend']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Friendship');
    }
};