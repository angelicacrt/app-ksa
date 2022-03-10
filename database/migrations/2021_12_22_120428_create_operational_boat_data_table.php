<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperationalBoatDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operational_boat_data', function (Blueprint $table) {
            $table->id();

            // Primary Data
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->string('tugName');
            // $table->foreign('tug_id')->references('id')->on('tugs')->onDelete('cascade')->onUpdate('cascade');

            $table->string('bargeName')->nullable();
            // $table->foreign('barge_id')->references('id')->on('barges')->onDelete('cascade')->onUpdate('cascade');

            $table->string('portOfLoading', 50);
            $table->string('portOfDischarge', 50);
            $table->string('taskType', 30);
            $table->double('cargoAmountStart')->nullable();
            $table->string('customer')->nullable();
            $table->string('status', 15)->default('On Going');

            // Secondary Data
            $table->string('from', 30)->nullable();
            $table->string('to', 30)->nullable();
            $table->string('MotherVessel', 50)->nullable();
            $table->string('condition', 50)->nullable();
            $table->string('estimatedTime', 30)->nullable();
            $table->double('cargoAmountEnd')->nullable();
            $table->double('cargoAmountEndCargo')->nullable();
            $table->longText('description')->nullable();

            // Calculation Data
            // Operational Shipment
            $table->dateTime('arrivalTime')->nullable();
            $table->datetime('departureTime')->nullable();

            $table->dateTime('startAsideL')->nullable();
            $table->dateTime('asideL')->nullable();
            $table->dateTime('commenceLoadL')->nullable();
            $table->dateTime('completedLoadingL')->nullable();
            $table->dateTime('cOffL')->nullable();
            $table->dateTime('StandByB')->nullable();

            $table->dateTime('DOH')->nullable();
            $table->dateTime('DOB')->nullable();

            $table->dateTime('departurePOD')->nullable();
            $table->dateTime('arrivalPODGeneral')->nullable();
            $table->dateTime('startAsidePOD')->nullable();
            $table->dateTime('asidePOD')->nullable();
            $table->dateTime('commenceLoadPOD')->nullable();
            $table->dateTime('completedLoadingPOD')->nullable();
            $table->dateTime('cOffPOD')->nullable();
            $table->dateTime('DOBPOD')->nullable();

            // Operational Transhipment
            $table->dateTime('faVessel')->nullable();
            $table->dateTime('arrivalPOL')->nullable();
            // => startAsideL
            // => asideL
            // => commenceLoadL
            // => completedLoadingL
            // => cOffL
            // => StandByB
            // => DOH
            // => DOB
            // => departurePOD
            // => arrivalPODGeneral
            $table->dateTime('startAsideMVTranshipment')->nullable();
            $table->dateTime('asideMVTranshipment')->nullable();
            $table->dateTime('commMVTranshipment')->nullable();
            $table->dateTime('compMVTranshipment')->nullable();
            $table->dateTime('cOffMVTranshipment')->nullable();

            $table->dateTime('departureTimeTranshipment')->nullable();

            // Samarinda Only (optional)
            $table->dateTime('departureJetty')->nullable();
            $table->dateTime('pengolonganNaik')->nullable();
            $table->dateTime('pengolonganTurun')->nullable();
            $table->dateTime('mooringArea')->nullable();

            // Non Operational
            // $table->dateTime('arrivalTime')->nullable();
            $table->dateTime('startDocking')->nullable();
            $table->dateTime('finishDocking')->nullable();
            $table->dateTime('departurePOL')->nullable();

            // Return Cargo
            $table->dateTime('arrivalPODCargo')->nullable();
            $table->dateTime('startAsideMVCargo')->nullable();
            $table->dateTime('asideMVCargo')->nullable();
            $table->dateTime('commMVCargo')->nullable();
            $table->dateTime('compMVCargo')->nullable();
            $table->dateTime('cOffMVCargo')->nullable();
            // => departureTime

            // Days Calculation
            $table->bigInteger('onSailingDays')->default(0);
            $table->bigInteger('loadingActivityDays')->default(0);
            $table->bigInteger('dischargeActivityDays')->default(0);
            $table->bigInteger('standbyDays')->default(0);
            $table->bigInteger('repairDays')->default(0);
            $table->bigInteger('dockingDays')->default(0);
            $table->bigInteger('standbyDockingDays')->default(0);
            $table->bigInteger('groundedBargeDays')->default(0);
            $table->bigInteger('waitingScheduleDays')->default(0);

            // Calculation Data
            $table->double('sailingToMV')->nullable();
            $table->double('standbyBL')->nullable();
            $table->double('sailingTimeMsk')->nullable();
            $table->double('sailingTimeKlr')->nullable();
            $table->double('sailingToJetty')->nullable();
            $table->double('maneuver')->nullable();
            $table->double('dischTime')->nullable();
            $table->double('dischRate')->nullable();
            $table->double('berthing')->nullable();
            $table->double('unberthing')->nullable();
            $table->double('ldgTime')->nullable();
            $table->double('ldgRate')->nullable();
            $table->double('prepareLdg')->nullable();
            $table->double('cycleTime')->nullable();
            $table->double('document')->nullable();
            $table->string('totalTime', 50)->nullable();
            $table->string('totalLostDays', 50)->nullable();

            // Additional Calculation Data For Return Cargo
            $table->double('sailingToMVCargo')->nullable();
            $table->double('maneuverCargo')->nullable();
            $table->double('dischTimeCargo')->nullable();
            $table->double('dischRateCargo')->nullable();
            $table->double('cycleTimeCargo')->nullable();

            // Validation Data
            $table->integer('task_tracker')->default(0);
            $table->integer('cargoChangeTracker')->default(0);
            $table->integer('cargo2ChangeTracker')->default(0);

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
        Schema::dropIfExists('operational_boat_data');
    }
}
