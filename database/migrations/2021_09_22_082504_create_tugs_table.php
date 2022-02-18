<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTugsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tugs', function (Blueprint $table) {
            $table->id();
            $table->string('tugName');
            $table->string('gt');
            $table->string('nt');
            $table->string('master');
            $table->string('flag');
            $table->string('IMONumber');
            $table->string('callSign');
            $table->boolean('tugAvailability')->default(true);
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
        Schema::dropIfExists('tugs');
    }
}
