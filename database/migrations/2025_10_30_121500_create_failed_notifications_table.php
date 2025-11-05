<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('failed_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('notifiable_type');
            $table->uuid('notifiable_id');
            $table->string('notification_class');
            $table->text('error_message');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('failed_notifications');
    }
};