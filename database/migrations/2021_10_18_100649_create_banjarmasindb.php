<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\documentbanjarmasin;
class CreateBanjarmasindb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banjarmasindb', function (Blueprint $table) {
            $table->id();
            $table->string('cabang', 15)->nullable();
            $table->unsignedBigInteger('user_id');

            $table->string('nama_kapal',70)->nullable();
            $table->string('upload_type',10)->nullable();
            $table->date('periode_awal')->nullable();
            $table->date('periode_akhir')->nullable();
            
            $table->dateTime('time_upload1') ->nullable();
            $table->string('dana1',15)->nullable();
            $table->string('status1', 10)->nullable();
            $table->string('reason1', 160) ->nullable();
            $table->string('perjalanan', 160)->nullable();

            $table->dateTime('time_upload2') ->nullable();
            $table->string('dana2',15)->nullable();
            $table->string('status2', 10) ->nullable();
            $table->string('reason2', 160) ->nullable();
            $table->string('sertifikat_keselamatan', 160)->nullable();

            $table->dateTime('time_upload3') ->nullable();
            $table->string('dana3',15)->nullable();
            $table->string('status3', 10)->nullable();
            $table->string('reason3', 160)->nullable();
            $table->string('sertifikat_anti_fauling', 160)->nullable();

            $table->dateTime('time_upload4') ->nullable();
            $table->string('dana4',15)->nullable();
            $table->string('status4', 10)->nullable();
            $table->string('reason4', 160)->nullable();
            $table->string('surveyor', 160)->nullable();

            $table->dateTime('time_upload5') ->nullable();
            $table->string('dana5',15)->nullable();
            $table->string('status5', 10)->nullable();
            $table->string('reason5', 160)->nullable();
            $table->string('drawing&stability', 160)->nullable();
            
            $table->dateTime('time_upload6') ->nullable();
            $table->string('dana6',15)->nullable();
            $table->string('status6', 10)->nullable();
            $table->string('reason6', 160)->nullable();
            $table->string('laporan_pengeringan', 160)->nullable();

            $table->dateTime('time_upload7') ->nullable();
            $table->string('dana7',15)->nullable();
            $table->string('status7', 10)->nullable();
            $table->string('reason7', 160)->nullable();
            $table->string('berita_acara_lambung', 160)->nullable();

            $table->dateTime('time_upload8') ->nullable();
            $table->string('dana8',15)->nullable();
            $table->string('status8', 10)->nullable();
            $table->string('reason8', 160)->nullable();
            $table->string('laporan_pemeriksaan_nautis', 160)->nullable();

            $table->dateTime('time_upload9') ->nullable();
            $table->string('dana9',15)->nullable();
            $table->string('status9', 10)->nullable();
            $table->string('reason9', 160)->nullable();
            $table->string('laporan_pemeriksaan_anti_faulin', 160)->nullable();

            $table->dateTime('time_upload10') ->nullable();
            $table->string('dana10',15)->nullable();
            $table->string('status10', 10)->nullable();
            $table->string('reason10' , 160)->nullable();
            $table->string('laporan_pemeriksaan_radio', 160)->nullable();

            $table->dateTime('time_upload11') ->nullable();
            $table->string('dana11',15)->nullable();
            $table->string('status11', 10)->nullable();
            $table->string('reason11' , 160)->nullable();
            $table->string('laporan_pemeriksaan_snpp', 160)->nullable();

            $table->dateTime('time_upload12') ->nullable();
            $table->string('dana12',15)->nullable();
            $table->string('status12', 10)->nullable();
            $table->string('reason12' , 160)->nullable();
            $table->string('bki', 160)->nullable();

            $table->dateTime('time_upload13') ->nullable();
            $table->string('dana13',15)->nullable();
            $table->string('status13', 10)->nullable();
            $table->string('reason13' , 160)->nullable();
            $table->string('snpp_permanen', 160)->nullable();

            $table->dateTime('time_upload14') ->nullable();
            $table->string('dana14',15)->nullable();
            $table->string('status14', 10)->nullable();
            $table->string('reason14' , 160)->nullable();
            $table->string('snpp_endorse', 160)->nullable();

            $table->dateTime('time_upload15') ->nullable();
            $table->string('dana15',15)->nullable();
            $table->string('status15', 10)->nullable();
            $table->string('reason15' , 160)->nullable();
            $table->string('surat_laut_endorse', 160)->nullable();

            $table->dateTime('time_upload16') ->nullable();
            $table->string('dana16',15)->nullable();
            $table->string('status16', 10)->nullable();
            $table->string('reason16' , 160)->nullable();
            $table->string('surat_laut_permanen', 160)->nullable();

            $table->dateTime('time_upload17') ->nullable();
            $table->string('dana17',15)->nullable();
            $table->string('status17', 10)->nullable();
            $table->string('reason17' , 160)->nullable();
            $table->string('compas_seren', 160)->nullable();

            $table->dateTime('time_upload18') ->nullable();
            $table->string('dana18',15)->nullable();
            $table->string('status18', 10)->nullable();
            $table->string('reason18' , 160)->nullable();
            $table->string('keselamatan_(tahunan)', 160)->nullable();

            $table->dateTime('time_upload19') ->nullable();
            $table->string('dana19',15)->nullable();
            $table->string('status19', 10)->nullable();
            $table->string('reason19' , 160)->nullable();
            $table->string('keselamatan_(pengaturan_dok)', 160)->nullable();

            $table->dateTime('time_upload20') ->nullable();
            $table->string('dana20',15)->nullable();
            $table->string('status20' , 10)->nullable();
            $table->string('reason20' , 160)->nullable();
            $table->string('keselamatan_(dok)', 160)->nullable();

            $table->dateTime('time_upload21') ->nullable();
            $table->string('dana21',15)->nullable();
            $table->string('status21' , 10)->nullable();
            $table->string('reason21' , 160)->nullable();
            $table->string('garis_muat', 160)->nullable();

            $table->dateTime('time_upload22') ->nullable();
            $table->string('dana22',15)->nullable();
            $table->string('status22' , 10)->nullable();
            $table->string('reason22' , 160)->nullable();
            $table->string('dispensasi_isr')->nullable();

            $table->dateTime('time_upload23') ->nullable();
            $table->string('dana23',15)->nullable();
            $table->string('status23' , 10)->nullable();
            $table->string('reason23' , 160)->nullable();
            $table->string('life_raft_1_2_pemadam', 160)->nullable();

            $table->dateTime('time_upload24') ->nullable();
            $table->string('dana24',15)->nullable();
            $table->string('status24' , 10)->nullable();
            $table->string('reason24' , 160)->nullable();
            $table->string('sscec', 160)->nullable();

            $table->dateTime('time_upload25') ->nullable();
            $table->string('dana25',15)->nullable();
            $table->string('status25' , 10)->nullable();
            $table->string('reason25' , 160)->nullable();
            $table->string('seatrail', 160)->nullable();

            $table->dateTime('time_upload26') ->nullable();
            $table->string('dana26',15)->nullable();
            $table->string('status26' , 10)->nullable();
            $table->string('reason26' , 160)->nullable();
            $table->string('laporan_pemeriksaan_umum', 160)->nullable();

            $table->dateTime('time_upload27') ->nullable();
            $table->string('dana27',15)->nullable();
            $table->string('status27' , 10)->nullable();
            $table->string('reason27' , 160)->nullable();
            $table->string('laporan_pemeriksaan_mesin', 160)->nullable();

            $table->dateTime('time_upload28') ->nullable();
            $table->string('dana28',15)->nullable();
            $table->string('status28' , 10)->nullable();
            $table->string('reason28' , 160)->nullable();
            $table->string('nota_dinas_perubahan_kawasan', 160)->nullable();

            $table->dateTime('time_upload29') ->nullable();
            $table->string('dana29',15)->nullable();
            $table->string('status29' , 10)->nullable();
            $table->string('reason29' , 160)->nullable();
            $table->string('PAS', 160)->nullable();

            $table->dateTime('time_upload30') ->nullable();
            $table->string('dana30',15)->nullable();
            $table->string('status30', 10)->nullable();
            $table->string('reason30', 160)->nullable();
            $table->string('invoice_bki')->nullable();

            $table->dateTime('time_upload31') ->nullable();
            $table->string('dana31',15)->nullable();
            $table->string('status31', 10)->nullable();
            $table->string('reason31', 160)->nullable();
            $table->string('safe_manning', 160)->nullable();

            $table->dateTime('time_upload32') ->nullable();
            $table->string('dana32',15)->nullable();
            $table->string('status32', 10)->nullable();
            $table->string('reason32' , 160)->nullable();
            $table->string('bki_lambung', 160)->nullable();

            $table->dateTime('time_upload33') ->nullable();
            $table->string('dana33',15)->nullable();
            $table->string('status33', 10)->nullable();
            $table->string('reason33' , 160)->nullable();
            $table->string('bki_mesin', 160)->nullable();

            $table->dateTime('time_upload34') ->nullable();
            $table->string('dana34',15)->nullable();
            $table->string('status34', 10)->nullable();
            $table->string('reason34' , 160)->nullable();
            $table->string('bki_Garis_muat', 160)->nullable();

            $table->dateTime('time_upload35') ->nullable();
            $table->string('dana35',15)->nullable();
            $table->string('status35', 10)->nullable();
            $table->string('reason35', 180)->nullable();
            $table->string('Lain_Lain1', 180)->nullable();

            $table->dateTime('time_upload36') ->nullable();
            $table->string('dana36',15)->nullable();
            $table->string('status36', 10)->nullable();
            $table->string('reason36', 180)->nullable();
            $table->string('Lain_Lain2', 180)->nullable();

            $table->dateTime('time_upload37') ->nullable();
            $table->string('dana37',15)->nullable();
            $table->string('status37', 10)->nullable();
            $table->string('reason37', 180)->nullable();
            $table->string('Lain_Lain3', 180)->nullable();

            $table->dateTime('time_upload38') ->nullable();
            $table->string('dana38',15)->nullable();
            $table->string('status38', 10)->nullable();
            $table->string('reason38', 180)->nullable();
            $table->string('Lain_Lain4', 180)->nullable();

            $table->dateTime('time_upload39') ->nullable();
            $table->string('dana39',15)->nullable();
            $table->string('status39', 10)->nullable();
            $table->string('reason39', 180)->nullable();
            $table->string('Lain_Lain5', 180)->nullable();

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
        Schema::dropIfExists('banjarmasindb');
    }
}
