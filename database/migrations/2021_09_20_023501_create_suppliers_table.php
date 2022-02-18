<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplierName');
            $table->string('supplierPic');
            $table->string('supplierEmail')->unique();
            $table->string('supplierAddress');
            $table->string('supplierNPWP');
            $table->string('supplierCode')->unique();
            $table->string('supplierNoRek')->nullable();
            $table->longText('supplierNote')->nullable();
            // $table->string('noTelpBks')->nullable();
            // $table->string('noTelpSmd')->nullable();
            // $table->string('noTelpBer')->nullable();
            // $table->string('noTelpBnt')->nullable();
            // $table->string('noTelpBnj')->nullable();
            // $table->string('noTelpJkt')->nullable();
            $table->integer('quality')->default(1);
            $table->integer('top')->default(1);
            $table->integer('price')->default(1);
            $table->integer('deliveryTime')->default(1);
            $table->integer('availability')->default(1);
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
        Schema::dropIfExists('suppliers');
    }
}
