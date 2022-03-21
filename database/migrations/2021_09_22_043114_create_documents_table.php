<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\documents;


class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('cabang')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            
            $table->string('nama_kapal',100)->nullable();
            $table->string('upload_type',10)->nullable();
            $table->date('periode_awal')->nullable();
            $table->date('periode_akhir')->nullable();
            
            $table->dateTime('time_upload1')->nullable();
            $table->string('reason1')->nullable();
            $table->string('status1', 25)->nullable();
            $table->string('dana1', 25)->nullable();
            $table->string('sertifikat_keselamatan')->nullable();

            $table->dateTime('time_upload2')->nullable();
            $table->string('reason2')->nullable();
            $table->string('status2' , 25)->nullable();
            $table->string('dana2' , 25)->nullable();
            $table->string('sertifikat_garis_muat')->nullable();          

            $table->dateTime('time_upload3')->nullable();
            $table->string('reason3')->nullable();
            $table->string('status3' , 25)->nullable();
            $table->string('dana3' , 25)->nullable();
            $table->string('penerbitan_sekali_jalan')->nullable();

            $table->dateTime('time_upload4')->nullable();
            $table->string('reason4')->nullable();
            $table->string('status4' , 25)->nullable();
            $table->string('dana4' , 25)->nullable();
            $table->string('sertifikat_safe_manning')->nullable();

            $table->dateTime('time_upload5')->nullable();
            $table->string('reason5')->nullable();
            $table->string('status5' , 25)->nullable();
            $table->string('dana5' , 25)->nullable();
            $table->string('endorse_surat_laut')->nullable();

            $table->dateTime('time_upload6')->nullable();
            $table->string('reason6')->nullable();
            $table->string('status6' , 25)->nullable();
            $table->string('dana6' , 25)->nullable();
            $table->string('perpanjangan_sertifikat_sscec')->nullable();

            $table->dateTime('time_upload7')->nullable();
            $table->string('reason7')->nullable();
            $table->string('status7' , 25)->nullable();
            $table->string('dana7' , 25)->nullable();
            $table->string('perpanjangan_sertifikat_p3k')->nullable();     

            $table->dateTime('time_upload8')->nullable();
            $table->string('reason8')->nullable();
            $table->string('status8' , 25)->nullable();
            $table->string('dana8' , 25)->nullable();
            $table->string('biaya_laporan_dok')->nullable();     

            $table->dateTime('time_upload9')->nullable();
            $table->string('reason9')->nullable();
            $table->string('status9' , 25)->nullable();
            $table->string('dana9' , 25)->nullable();
            $table->string('pnpb_sertifikat_keselamatan')->nullable();     

            $table->dateTime('time_upload10')->nullable();
            $table->string('reason10')->nullable();
            $table->string('status10' , 25)->nullable();
            $table->string('dana10' , 25)->nullable();
            $table->string('pnpb_sertifikat_garis_muat')->nullable();      

            $table->dateTime('time_upload11')->nullable();
            $table->string('reason11')->nullable();
            $table->string('status11' , 25)->nullable();
            $table->string('dana11' , 25)->nullable();
            $table->string('pnpb_surat_laut')->nullable();   

            $table->dateTime('time_upload12')->nullable();
            $table->string('reason12')->nullable();
            $table->string('status12' , 25)->nullable();
            $table->string('dana12' , 25)->nullable();
            $table->string('sertifikat_snpp')->nullable();        

            $table->dateTime('time_upload13')->nullable();
            $table->string('reason13')->nullable();
            $table->string('status13' , 25)->nullable();
            $table->string('dana13' , 25)->nullable();
            $table->string('sertifikat_anti_teritip')->nullable();      

            $table->dateTime('time_upload14')->nullable();
            $table->string('reason14')->nullable();
            $table->string('status14' , 25)->nullable();
            $table->string('dana14' , 25)->nullable();
            $table->string('pnbp_snpp&snat')->nullable();       

            $table->dateTime('time_upload15')->nullable();
            $table->string('reason15')->nullable();
            $table->string('status15' , 25)->nullable();
            $table->string('dana15' , 25)->nullable();
            $table->string('biaya_survey')->nullable(); 

            $table->dateTime('time_upload16')->nullable();
            $table->string('reason16')->nullable();
            $table->string('status16' , 25)->nullable();
            $table->string('dana16' , 25)->nullable();
            $table->string('pnpb_sscec')->nullable();
            
            $table->dateTime('time_upload17') ->nullable();
            $table->string('status17', 10)->nullable();
            $table->string('dana17', 10)->nullable();
            $table->string('reason17' , 170)->nullable();
            $table->string('bki_lambung', 170)->nullable();

            $table->dateTime('time_upload18') ->nullable();
            $table->string('status18', 10)->nullable();
            $table->string('dana18', 10)->nullable();
            $table->string('reason18' , 170)->nullable();
            $table->string('bki_mesin', 170)->nullable();

            $table->dateTime('time_upload19') ->nullable();
            $table->string('status19', 10)->nullable();
            $table->string('dana19', 10)->nullable();
            $table->string('reason19' , 170)->nullable();
            $table->string('bki_Garis_muat', 170)->nullable();

            $table->dateTime('time_upload20')->nullable();
            $table->string('reason20')->nullable();
            $table->string('status20' , 25)->nullable();
            $table->string('dana20' , 25)->nullable();
            $table->string('Lain_Lain1')->nullable();
            
            $table->dateTime('time_upload21')->nullable();
            $table->string('reason21')->nullable();
            $table->string('status21' , 25)->nullable();
            $table->string('dana21' , 25)->nullable();
            $table->string('Lain_Lain2')->nullable();

            $table->dateTime('time_upload22')->nullable();
            $table->string('reason22')->nullable();
            $table->string('status22' , 25)->nullable();
            $table->string('dana22' , 25)->nullable();
            $table->string('Lain_Lain3')->nullable();
            
            $table->dateTime('time_upload23')->nullable();
            $table->string('reason23')->nullable();
            $table->string('status23' , 25)->nullable();
            $table->string('dana23' , 25)->nullable();
            $table->string('Lain_Lain4')->nullable();
            
            $table->dateTime('time_upload24')->nullable();
            $table->string('reason24')->nullable();
            $table->string('status24' , 25)->nullable();
            $table->string('dana24' , 25)->nullable();
            $table->string('Lain_Lain5')->nullable();

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
        Schema::dropIfExists('Documents');
    }
}
