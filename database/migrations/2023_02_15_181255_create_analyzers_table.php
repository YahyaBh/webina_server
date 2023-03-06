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
            $table->string('year');
            $table->string('January');
            $table->string('February');
            $table->string('March');
            $table->string('April');
            $table->string('May');
            $table->string('June');
            $table->string('July');
            $table->string('August');
            $table->string('September');
            $table->string('October');
            $table->string('November');
            $table->string('December');
            $table->integer('number');
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
