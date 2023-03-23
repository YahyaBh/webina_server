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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_token');
            $table->string('website_name');
            $table->string('website_token');
            $table->string('user_id');
            $table->integer('amount');
            $table->enum('paid' , ['yes' , 'no'])->default('no');
            $table->enum('method' , ['westernunion' ,  'moneygram']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
