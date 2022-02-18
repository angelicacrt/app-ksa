<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\spgrfile;
class CreateSpgrfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spgrfiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('cabang')->nullable();
            $table->string('no_formclaim')->nullable();
            
            $table->date('time_upload1')->nullable();
            $table->string('reason1')->nullable();
            $table->string('status1')->nullable();
            $table->string('spgr')->nullable();

            $table->date('time_upload2')->nullable();
            $table->string('reason2')->nullable();
            $table->string('status2')->nullable();
            $table->string('Letter_of_Discharge')->nullable();

            $table->date('time_upload3')->nullable();
            $table->string('reason3')->nullable();
            $table->string('status3')->nullable();
            $table->string('CMC')->nullable();

            $table->date('time_upload4')->nullable();
            $table->string('reason4')->nullable();
            $table->string('status4')->nullable();
            $table->string('surat_laut')->nullable();
            
            $table->date('time_upload5')->nullable();
            $table->string('reason5')->nullable();
            $table->string('status5')->nullable();
            $table->string('spb')->nullable();

            $table->date('time_upload6')->nullable();
            $table->string('reason6')->nullable();
            $table->string('status6')->nullable();
            $table->string('load_line')->nullable();

            $table->date('time_upload7')->nullable();
            $table->string('reason7')->nullable();
            $table->string('status7')->nullable();
            $table->string('surat_keterangan_bank')->nullable();

            // $table->date('time_upload8') ->nullable();
            // $table->string('status8')->nullable();
            // $table->string('reason8')->nullable();
            // $table->string('lain_lain')->nullable();
            
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
        Schema::dropIfExists('spgrfiles');
    }
}
