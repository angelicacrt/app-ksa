<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\documentrpk;

class CreateRpkdocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rpkdocuments', function (Blueprint $table) {
            $table->id();
            $table->string('cabang')->nullable();

            $table->string('nama_kapal',100)->nullable();
            $table->string('approved_by',20)->nullable();
            $table->date('periode_awal')->nullable();
            $table->date('periode_akhir')->nullable();
            
            $table->dateTime('time_upload1') ->nullable();
            $table->string('status1')->nullable();
            $table->string('reason1')->nullable();
            $table->string('surat_barang')->nullable();
            
            
            $table->dateTime('time_upload2') ->nullable();
            $table->string('status2')->nullable();
            $table->string('reason2')->nullable();
            $table->string('cargo_manifest')->nullable();
            
            $table->dateTime('time_upload3') ->nullable();
            $table->string('status3')->nullable();
            $table->string('reason3')->nullable();
            $table->string('voyage')->nullable();
            
            $table->dateTime('time_upload4') ->nullable();
            $table->string('status4')->nullable();
            $table->string('reason4')->nullable();
            $table->string('bill_lading')->nullable();
            
            $table->dateTime('time_upload5') ->nullable();
            $table->string('status5')->nullable();
            $table->string('reason5')->nullable();
            $table->string('gerak_kapal')->nullable();
            
            $table->dateTime('time_upload6') ->nullable();
            $table->string('status6')->nullable();
            $table->string('reason6')->nullable();
            $table->string('docking')->nullable();
            
            $table->dateTime('time_upload7') ->nullable();
            $table->string('status7')->nullable();
            $table->string('reason7')->nullable();
            $table->string('surat_kapal')->nullable();
            
            $table->unsignedBigInteger('user_id');
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
        Schema::dropIfExists('rpkdocuments');
    }
}
