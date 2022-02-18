<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempcartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tempcarts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            
            $table->string('name')->nullable();
            $table->string('no_FormClaim')->nullable();
            $table->date('tgl_formclaim') ->nullable();
            $table->date('tgl_insiden') ->nullable();
            $table->string('jenis_incident', 2 )->nullable();
            $table->string('incident')->nullable();
            $table->string('surveyor')->nullable();
            $table->string('TSI_TugBoat', 25)->nullable();
            $table->string('TSI_barge', 25)->nullable();
            
            $table->string('barge')->nullable();
            $table->string('tugBoat')->nullable();
            $table->string('item')->nullable();
            $table->decimal('deductible', 14, 2)->nullable();
            $table->string('amount', 25)->nullable();
            $table->longText('description')->nullable();

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
        Schema::dropIfExists('tempcarts');
    }
}
