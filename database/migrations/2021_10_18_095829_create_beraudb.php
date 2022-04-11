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
            $table->string('cabang', 10)->nullable();
            $table->unsignedBigInteger('user_id');

            
            $table->string('nama_kapal',70)->nullable();
            $table->string('upload_type',10)->nullable();
            $table->string('approved_by',20)->nullable();
            // $table->string('no_mohon',25)->nullable();
            $table->date('periode_awal')->nullable();
            $table->date('periode_akhir')->nullable();
            
            $table->dateTime('time_upload1') ->nullable();
            $table->string('status1', 30)->nullable();
            $table->string('reason1',100)->nullable();
            $table->string('dana1',15)->nullable();
            $table->string('pnbp_sertifikat_konstruksi')->nullable();
            
            $table->dateTime('time_upload2') ->nullable();
            $table->string('status2', 30)->nullable();
            $table->string('reason2',100)->nullable();
            $table->string('dana2',15)->nullable();
            $table->string('jasa_urus_sertifikat', 110)->nullable();
            
            $table->dateTime('time_upload3') ->nullable();
            $table->string('status3', 30)->nullable();
            $table->string('reason3',100)->nullable();
            $table->string('dana3',15)->nullable();
            $table->string('pnbp_sertifikat_perlengkapan', 110)->nullable();
            
            $table->dateTime('time_upload4') ->nullable();
            $table->string('status4', 30)->nullable();
            $table->string('reason4',100)->nullable();
            $table->string('dana4',15)->nullable();
            $table->string('pnbp_sertifikat_radio',110)->nullable();
            
            $table->dateTime('time_upload5') ->nullable();
            $table->string('status5', 30)->nullable();
            $table->string('reason5',100)->nullable();
            $table->string('dana5',15)->nullable();
            $table->string('pnbp_sertifikat_ows',110)->nullable();
            
            $table->dateTime('time_upload6') ->nullable();
            $table->string('status6', 30)->nullable();
            $table->string('reason6',100)->nullable();
            $table->string('dana6',15)->nullable();
            $table->string('pnbp_garis_muat',110)->nullable();
            
            $table->dateTime('time_upload7') ->nullable();
            $table->string('status7', 30)->nullable();
            $table->string('reason7',100)->nullable();
            $table->string('dana7',15)->nullable();
            $table->string('pnbp_pemeriksaan_endorse_sl',110)->nullable();
            
            $table->dateTime('time_upload8') ->nullable();
            $table->string('status8', 30)->nullable();
            $table->string('reason8',100)->nullable();
            $table->string('dana8',15)->nullable();
            $table->string('pemeriksaan_sertifikat',110)->nullable();
            
            $table->dateTime('time_upload9') ->nullable();
            $table->string('status9', 30)->nullable();
            $table->string('reason9',100)->nullable();
            $table->string('dana9',15)->nullable();
            $table->string('marine_inspektor',110)->nullable();
            
            $table->dateTime('time_upload10') ->nullable();
            $table->string('status10', 30)->nullable();
            $table->string('reason10',100)->nullable();
            $table->string('dana10',15)->nullable();
            $table->string('biaya_clearance',110)->nullable() ;
            
            $table->dateTime('time_upload11') ->nullable();
            $table->string('status11', 30)->nullable();
            $table->string('reason11',100)->nullable();
            $table->string('dana11',15)->nullable();
            $table->string('pnbp_master_cable',110)->nullable() ; 
            
            $table->dateTime('time_upload12') ->nullable();
            $table->string('status12', 30)->nullable();
            $table->string('reason12',100)->nullable();
            $table->string('dana12',15)->nullable();
            $table->string('cover_deck_logbook',110)->nullable() ;
            
            $table->dateTime('time_upload13') ->nullable();
            $table->string('status13', 30)->nullable();
            $table->string('reason13',100)->nullable();
            $table->string('dana13',15)->nullable();
            $table->string('cover_engine_logbook',110)->nullable() ;
            
            $table->dateTime('time_upload14') ->nullable();
            $table->string('status14', 30)->nullable();
            $table->string('reason14',100)->nullable();
            $table->string('dana14',15)->nullable();
            $table->string('exibitum_dect_logbook',110)->nullable() ;
            
            $table->dateTime('time_upload15') ->nullable();
            $table->string('status15', 30)->nullable();
            $table->string('reason15',100)->nullable();
            $table->string('dana15',15)->nullable();
            $table->string('exibitum_engine_logbook',110)->nullable() ;
            
            $table->dateTime('time_upload16') ->nullable();
            $table->string('status16', 30)->nullable();
            $table->string('reason16',100)->nullable();
            $table->string('dana16',15)->nullable();
            $table->string('pnbp_deck_logbook')->nullable() ;
            
            $table->dateTime('time_upload17') ->nullable();
            $table->string('status17', 30)->nullable();
            $table->string('reason17',100)->nullable();
            $table->string('dana17',15)->nullable();
            $table->string('pnbp_engine_logbook',110)->nullable() ;
            
            $table->dateTime('time_upload18') ->nullable();
            $table->string('status18', 30)->nullable();
            $table->string('reason18',100)->nullable();
            $table->string('dana18',15)->nullable();
            $table->string('biaya_docking',110)->nullable() ;
            
            $table->dateTime('time_upload19') ->nullable();
            $table->string('status19', 30)->nullable();
            $table->string('reason19',100)->nullable();
            $table->string('dana19',15)->nullable();
            $table->string('lain-lain',110)->nullable();
            
            $table->dateTime('time_upload20') ->nullable();
            $table->string('status20', 30)->nullable();
            $table->string('reason20',100)->nullable();
            $table->string('dana20',15)->nullable();
            $table->string('biaya_labuh_tambat',110)->nullable();
            
            $table->dateTime('time_upload21') ->nullable();
            $table->string('status21', 30)->nullable();
            $table->string('reason21',100)->nullable();
            $table->string('dana21',15)->nullable();
            $table->string('biaya_rambu',110)->nullable();
            
            $table->dateTime('time_upload22') ->nullable();
            $table->string('status22', 30)->nullable();
            $table->string('reason22',100)->nullable();
            $table->string('dana22',15)->nullable();
            $table->string('pnbp_pemeriksaan',110)->nullable();
            
            $table->dateTime('time_upload23') ->nullable();
            $table->string('status23', 30)->nullable();
            $table->string('reason23',100)->nullable();
            $table->string('dana23',15)->nullable();
            $table->string('sertifikat_bebas_sanitasi&p3k',110)->nullable(); 
            
            $table->dateTime('time_upload24') ->nullable();
            $table->string('status24', 30)->nullable();
            $table->string('reason24',100)->nullable();
            $table->string('dana24',15)->nullable();
            $table->string('sertifikat_garis_muat',100)->nullable();
            
            $table->dateTime('time_upload25') ->nullable();
            $table->string('status25', 30)->nullable();
            $table->string('reason25',100)->nullable();
            $table->string('dana25',15)->nullable();
            $table->string('pnpb_sscec', 110)->nullable();
            
            $table->dateTime('time_upload26') ->nullable();
            $table->string('status26', 30)->nullable();
            $table->string('reason26',100)->nullable();
            $table->string('dana26',15)->nullable();
            $table->string('ijin_sekali_jalan',110)->nullable();
            
            $table->dateTime('time_upload27') ->nullable();
            $table->string('status27', 30)->nullable();
            $table->string('reason27' , 150)->nullable();
            $table->string('dana27',15)->nullable();
            $table->string('bki_lambung', 100)->nullable();
            
            $table->dateTime('time_upload28') ->nullable();
            $table->string('status28', 30)->nullable();
            $table->string('reason28' , 150)->nullable();
            $table->string('dana28',15)->nullable();
            $table->string('bki_mesin', 100)->nullable();
            
            $table->dateTime('time_upload29') ->nullable();
            $table->string('status29', 30)->nullable();
            $table->string('reason29' , 150)->nullable();
            $table->string('dana29',15)->nullable();
            $table->string('bki_Garis_muat', 100)->nullable();
            
            $table->dateTime('time_upload30') ->nullable();
            $table->string('status30', 30)->nullable();
            $table->string('reason30', 100)->nullable();
            $table->string('dana30',15)->nullable();
            $table->string('Lain_Lain1', 100)->nullable();
            
            $table->dateTime('time_upload31') ->nullable();
            $table->string('status31', 30)->nullable();
            $table->string('reason31', 100)->nullable();
            $table->string('dana31',15)->nullable();
            $table->string('Lain_Lain2', 100)->nullable();
            
            $table->dateTime('time_upload32') ->nullable();
            $table->string('status32', 30)->nullable();
            $table->string('reason32', 100)->nullable();
            $table->string('dana32',15)->nullable();
            $table->string('Lain_Lain3', 100)->nullable();
            
            $table->dateTime('time_upload33') ->nullable();
            $table->string('status33', 30)->nullable();
            $table->string('reason33', 100)->nullable();
            $table->string('dana33',15)->nullable();
            $table->string('Lain_Lain4', 100)->nullable();
            
            $table->dateTime('time_upload34') ->nullable();
            $table->string('status34', 30)->nullable();
            $table->string('reason34', 100)->nullable();
            $table->string('dana34',15)->nullable();
            $table->string('Lain_Lain5', 100)->nullable();
            
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
