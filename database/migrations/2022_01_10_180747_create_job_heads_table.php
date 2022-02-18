<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobHeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_heads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('Headjasa_id')->nullable();
            $table->string('Headjasa_tracker_id')->nullable();
            $table->date('jrDate')->nullable();
            $table->string('noJr')->nullable();
            $table->string('created_by')->nullable();
            $table->string('check_by')->nullable();
            $table->string('cabang')->nullable();
            $table->string('status')->nullable();
            $table->string('reason')->nullable();
            
            $table->string('company')->nullable();
            
            $table->string('JO_id')->nullable();
            $table->date('JODate')->nullable();
            $table->string('ppn')->nullable();
            $table->decimal('discount')->default(0);
            $table->decimal('totalPriceBeforeCalculation', 13, 2)->default(0);
            $table->decimal('totalPrice', 13, 2)->default(0);
            $table->string('boatName')->nullable();
            $table->string('invoiceAddress')->nullable();
            $table->string('Approved_by')->nullable();
            $table->string('manager_name')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('job_heads');
    }
}
