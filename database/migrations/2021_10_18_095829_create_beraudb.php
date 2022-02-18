<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\documentberau;

class CreateBeraudb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beraudb', function (Blueprint $table) {
            $table->id();
            $table->string('cabang', 15)->nullable();
            $table->unsignedBigInteger('user_id');

            $table->string('nama_kapal',100)->nullable();
            $table->date('periode_awal')->nullable();
            $table->date('periode_akhir')->nullable();
            
            $table->dateTime('time_upload1') ->nullable();
            $table->string('status1', 10)->nullable();
            $table->string('reason1',180)->nullable();
            $table->string('pnbp_sertifikat_konstruksi')->nullable();

            $table->dateTime('time_upload2') ->nullable();
            $table->string('status2', 10)->nullable();
            $table->string('reason2',180)->nullable();
            $table->string('jasa_urus_sertifikat')->nullable();

            $table->dateTime('time_upload3') ->nullable();
            $table->string('status3', 10)->nullable();
            $table->string('reason3',180)->nullable();
            $table->string('pnbp_sertifikat_perlengkapan')->nullable();

            $table->dateTime('time_upload4') ->nullable();
            $table->string('status4', 10)->nullable();
            $table->string('reason4',180)->nullable();
            $table->string('pnbp_sertifikat_radio')->nullable();

            $table->dateTime('time_upload5') ->nullable();
            $table->string('status5', 10)->nullable();
            $table->string('reason5',180)->nullable();
            $table->string('pnbp_sertifikat_ows')->nullable();

            $table->dateTime('time_upload6') ->nullable();
            $table->string('status6', 10)->nullable();
            $table->string('reason6',180)->nullable();
            $table->string('pnbp_garis_muat')->nullable();
            
            $table->dateTime('time_upload7') ->nullable();
            $table->string('status7', 10)->nullable();
            $table->string('reason7',180)->nullable();
            $table->string('pnbp_pemeriksaan_endorse_sl')->nullable();

            $table->dateTime('time_upload8') ->nullable();
            $table->string('status8', 10)->nullable();
            $table->string('reason8',180)->nullable();
            $table->string('pemeriksaan_sertifikat')->nullable();

            $table->dateTime('time_upload9') ->nullable();
            $table->string('status9', 10)->nullable();
            $table->string('reason9',180)->nullable();
            $table->string('marine_inspektor')->nullable();

            $table->dateTime('time_upload10') ->nullable();
            $table->string('status10', 10)->nullable();
            $table->string('reason10',180)->nullable();
            $table->string('biaya_clearance')->nullable() ;

            $table->dateTime('time_upload11') ->nullable();
            $table->string('status11', 10)->nullable();
            $table->string('reason11',180)->nullable();
            $table->string('pnbp_master_cable')->nullable() ; 

            $table->dateTime('time_upload12') ->nullable();
            $table->string('status12', 10)->nullable();
            $table->string('reason12',180)->nullable();
            $table->string('cover_deck_logbook')->nullable() ;

            $table->dateTime('time_upload13') ->nullable();
            $table->string('status13', 10)->nullable();
            $table->string('reason13',180)->nullable();
            $table->string('cover_engine_logbook')->nullable() ;

            $table->dateTime('time_upload14') ->nullable();
            $table->string('status14', 10)->nullable();
            $table->string('reason14',180)->nullable();
            $table->string('exibitum_dect_logbook')->nullable() ;

            $table->dateTime('time_upload15') ->nullable();
            $table->string('status15', 10)->nullable();
            $table->string('reason15',180)->nullable();
            $table->string('exibitum_engine_logbook')->nullable() ;

            $table->dateTime('time_upload16') ->nullable();
            $table->string('status16', 10)->nullable();
            $table->string('reason16',180)->nullable();
            $table->string('pnbp_deck_logbook')->nullable() ;

            $table->dateTime('time_upload17') ->nullable();
            $table->string('status17', 10)->nullable();
            $table->string('reason17',180)->nullable();
            $table->string('pnbp_engine_logbook')->nullable() ;

            $table->dateTime('time_upload18') ->nullable();
            $table->string('status18', 10)->nullable();
            $table->string('reason18',180)->nullable();
            $table->string('biaya_docking')->nullable() ;

            $table->dateTime('time_upload19') ->nullable();
            $table->string('status19', 10)->nullable();
            $table->string('reason19',180)->nullable();
            $table->string('lain-lain')->nullable();

            $table->dateTime('time_upload20') ->nullable();
            $table->string('status20', 10)->nullable();
            $table->string('reason20',180)->nullable();
            $table->string('biaya_labuh_tambat')->nullable();

            $table->dateTime('time_upload21') ->nullable();
            $table->string('status21', 10)->nullable();
            $table->string('reason21',180)->nullable();
            $table->string('biaya_rambu')->nullable();

            $table->dateTime('time_upload22') ->nullable();
            $table->string('status22', 10)->nullable();
            $table->string('reason22',180)->nullable();
            $table->string('pnbp_pemeriksaan')->nullable();

            $table->dateTime('time_upload23') ->nullable();
            $table->string('status23', 10)->nullable();
            $table->string('reason23',180)->nullable();
            $table->string('sertifikat_bebas_sanitasi&p3k')->nullable(); 

            $table->dateTime('time_upload24') ->nullable();
            $table->string('status24', 10)->nullable();
            $table->string('reason24',180)->nullable();
            $table->string('sertifikat_garis_muat')->nullable();

            $table->dateTime('time_upload25') ->nullable();
            $table->string('status25', 10)->nullable();
            $table->string('reason25',180)->nullable();
            $table->string('pnpb_sscec')->nullable();

            $table->dateTime('time_upload26') ->nullable();
            $table->string('status26', 10)->nullable();
            $table->string('reason26',180)->nullable();
            $table->string('ijin_sekali_jalan')->nullable();

            $table->dateTime('time_upload27') ->nullable();
            $table->string('status27', 10)->nullable();
            $table->string('reason27' , 170)->nullable();
            $table->string('bki_lambung', 170)->nullable();

            $table->dateTime('time_upload28') ->nullable();
            $table->string('status28', 10)->nullable();
            $table->string('reason28' , 170)->nullable();
            $table->string('bki_mesin', 170)->nullable();

            $table->dateTime('time_upload29') ->nullable();
            $table->string('status29', 10)->nullable();
            $table->string('reason29' , 170)->nullable();
            $table->string('bki_Garis_muat', 170)->nullable();
            
            $table->dateTime('time_upload30') ->nullable();
            $table->string('status30', 10)->nullable();
            $table->string('reason30', 180)->nullable();
            $table->string('Lain_Lain1', 180)->nullable();

            $table->dateTime('time_upload31') ->nullable();
            $table->string('status31', 10)->nullable();
            $table->string('reason31', 180)->nullable();
            $table->string('Lain_Lain2', 180)->nullable();

            $table->dateTime('time_upload32') ->nullable();
            $table->string('status32', 10)->nullable();
            $table->string('reason32',180, 180)->nullable();
            $table->string('Lain_Lain3', 180)->nullable();

            $table->dateTime('time_upload33') ->nullable();
            $table->string('status33', 10)->nullable();
            $table->string('reason33', 180)->nullable();
            $table->string('Lain_Lain4', 180)->nullable();

            $table->dateTime('time_upload34') ->nullable();
            $table->string('status34', 10)->nullable();
            $table->string('reason34', 180)->nullable();
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
        Schema::dropIfExists('beraudb');
    }
}
