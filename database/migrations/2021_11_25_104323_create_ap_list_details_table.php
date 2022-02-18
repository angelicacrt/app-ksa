<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApListDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ap_list_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('aplist_id');
            $table->unsignedBigInteger('helper_cursor')->nullable();

            $table->string('supplierName');
            $table->string('noInvoice');
            $table->string('noFaktur');
            $table->string('noDo');
            $table->date('dueDate');
            $table->string('userWhoSubmitted');
            $table->unsignedBigInteger('nominalInvoice');
            $table->string('additionalInformation')->nullable();

            $table->foreign('aplist_id')->references('id')->on('ap_lists')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('ap_list_details');
    }
}
