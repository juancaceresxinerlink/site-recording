<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        Schema::create('recordings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('agent_account')->nullable();
            $table->string('extension')->nullable();
            $table->string('dni')->nullable();
            $table->string('queue')->nullable();
            $table->string('id_interaction')->nullable();
            $table->string('call_type')->nullable();
            $table->string('ani')->nullable();
            $table->string('dnis')->nullable();
            $table->time('duration')->nullable();
            $table->longText('audio_file')->nullable();
            $table->timestamps();
        });
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*
        Schema::dropIfExists('recordings');
        */
    }
}
