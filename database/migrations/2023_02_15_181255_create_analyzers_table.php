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
        Schema::create('analyzers', function (Blueprint $table) {
            $table->id();
            $table->string('data_name');
            $table->string('Junary_data')->nullable();
            $table->string('February_data')->nullable();
            $table->string('March_data')->nullable();
            $table->string('April_data')->nullable();
            $table->string('May_data')->nullable();
            $table->string('June_data')->nullable();
            $table->string('July_data')->nullable();
            $table->string('August_data')->nullable();
            $table->string('September_data')->nullable();
            $table->string('October_data')->nullable();
            $table->string('November_data')->nullable();
            $table->string('December_data')->nullable();
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
        Schema::dropIfExists('analyzers');
    }
};
