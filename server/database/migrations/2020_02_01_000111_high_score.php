<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HighScore extends Migration
{
    public function up()
    {
        Schema::create('high_scores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('level_id');
            $table->text('player_name');
            $table->decimal('score');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('high_scores');
    }
}
