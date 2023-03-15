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
        Schema::create('websites', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('website_name');
            $table->string('description')->nullable();
            $table->string('website_url')->nullable();
            $table->string('website_tags')->nullable();
            $table->string('design')->nullable();
            $table->string('token', 64)->unique()->default('0000-0000-0000-0000-0000');
            $table->string('category')->nullable();
            $table->integer('price');
            $table->integer('old_price');
            $table->integer('stars')->nullable();
            $table->string('developing_Time')->default('1 to 5 days');
            $table->enum('status' ,['availabele' , 'unavailable'])->default('available');
            $table->string('specifications')->nullable();
            $table->string('theme_document')->nullable();
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
        Schema::dropIfExists('websites');
    }
};
