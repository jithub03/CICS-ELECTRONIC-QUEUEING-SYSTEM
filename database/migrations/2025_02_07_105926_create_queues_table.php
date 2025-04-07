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
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('contact_number');
            $table->string('email');
            $table->text('inquiry_type');
            $table->text('inquiry_details')->nullable();
            $table->boolean('notify_sms')->default(false);
            $table->boolean('notify_email')->default(false);
            $table->enum('status', ['pending', 'process', 'approve', 'rejected', 'archived'])->default('pending');
            $table->unsignedTinyInteger('window_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
