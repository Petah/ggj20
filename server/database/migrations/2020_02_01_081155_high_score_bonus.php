<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HighScoreBonus extends Migration
{
    public function up()
    {
        Schema::table('high_scores', function (Blueprint $table) {
            $table->decimal('bonus')->nullable()->after('score');
        });
    }

    public function down()
    {
    }
}
