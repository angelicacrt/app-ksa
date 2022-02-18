<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderHeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_heads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('order_id')->nullable();
            $table->string('cabang');
            $table->string('boatName')->nullable();
            $table->string('noPr')->nullable();
            $table->string('noPo')->nullable();
            $table->string('company', 15)->nullable();
            $table->string('companyFull')->nullable();
            $table->decimal('totalPriceBeforeCalculation', 13, 2)->default(0);
            $table->decimal('totalPrice', 13, 2)->default(0);

            // They wanted to include approved by purchasing 
            $table->string('approvedBy')->nullable();
            
            $table->integer('ppn')->nullable();
            $table->integer('retries')->default(0);
            $table->decimal('discount')->default(0);
            $table->string('sender')->nullable();
            $table->string('receiver')->nullable();
            $table->string('expedition')->nullable();
            $table->string('noResi')->nullable();
            $table->string('prDate')->nullable();
            $table->string('poDate')->nullable();
            $table->string('invoiceAddress')->nullable();
            $table->string('itemAddress')->nullable();
            $table->string('status')->default('Request In Progress By Logistic');
            $table->string('descriptions')->nullable();
            $table->string('reason')->nullable();
            $table->string('approved_at')->nullable();
            $table->string('noSbk')->nullable();
            $table->string('orderType')->nullable();
            $table->integer('order_tracker');
            $table->string('itemType', 10)->nullable();
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
        Schema::dropIfExists('order_heads');
    }
}
