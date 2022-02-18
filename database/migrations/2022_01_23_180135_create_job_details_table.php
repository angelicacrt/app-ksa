<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('jasa_id');
            $table->string('cabang', 35)->nullable();
            $table->string('lokasi' , 35)->nullable();
            $table->string('tugName',30)->nullable();
            $table->string('bargeName' , 30)->nullable();
            $table->string('supplier' , 30)->nullable();
            $table->string('job_State' , 30)->default('Accepted');
            $table->decimal('HargaJob', 13, 2)->default(0);
            $table->decimal('totalHargaJob', 13, 2)->default(0);
            $table->longText('note')->nullable();
            $table->integer('quantity')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('jasa_id')->references('id')->on('job_heads')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('job_details');
    }
}
