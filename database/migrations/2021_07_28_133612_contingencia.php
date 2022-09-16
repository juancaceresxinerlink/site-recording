<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Contingencia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('contingencia', function (Blueprint $table) {
            $table->id();
            $table->string('servicio');
            $table->string('contingencia1')->nullable();
            $table->string('contingencia2')->nullable();
            $table->string('contingencia3')->nullable();
            $table->string('estado')->nullable();
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
        //
    }
}
