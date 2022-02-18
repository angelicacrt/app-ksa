<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orders_id');
            $table->unsignedBigInteger('item_id');
            // $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('supplier')->nullable();
            $table->biginteger('quantity');
            $table->biginteger('acceptedQuantity')->nullable();
            $table->decimal('itemPrice', 13, 2)->default(0);
            $table->decimal('totalItemPrice', 13, 2)->default(0);
            $table->string('department')->nullable();
            $table->string('orderItemState')->default('Accepted');
            $table->string('note')->nullable();
            $table->timestamps();

            $table->foreign('orders_id')->references('id')->on('order_heads')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade')->onUpdate('cascade');
            // $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_details');
    }
}
