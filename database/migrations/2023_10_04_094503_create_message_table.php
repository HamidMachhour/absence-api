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
        Schema::create('Message', function (Blueprint $table) {
            $table->id();
            
            // Source User ID (Sender)
            // Assuming your User IDs are UUIDs, use 'uuid' for the foreign key. 
            // If they are BigIntegers, replace 'uuid' with 'foreignId'.
            $table->string('s_user_id',255);  
            // Destination User ID (Receiver)
            $table->string('d_user_id',255);
            $table->string('subject')->nullable();
            $table->text('message');
            //reply to message id
            $table->unsignedBigInteger('reply_to')->nullable();
            // Status: 'read' or 'unread'
            $table->enum('status', ['read', 'unread','source_archived', 'destination_archived', 'both_archived'])->default('unread');
             $table->timestamp('deleted_at')->nullable();;
            $table->timestamps();

            // Add an index for quicker lookup of conversations
            $table->index(['s_user_id', 'd_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Message');
    }
};