<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\documentsamarinda;
class CreateSamarindadb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('samarindadb', function (Blueprint $table) {
            $table->id();
            $table->string('cabang', 10)->nullable();
            $table->unsignedBigInteger('user_id');

            $table->string('nama_kapal',70)->nullable();
            $table->date('periode_awal')->nullable();
            $table->date('periode_akhir')->nullable();
           
            $table->dateTime('time_upload1')->nullable();
            $table->string('status1', 10)->nullable();
            $table->string('reason1', 160)->nullable();
            $table->string('sertifikat_keselamatan(perpanjangan)', 160)->nullable();

            $table->dateTime('time_upload2')->nullable();
            $table->string('status2', 10)->nullable();
            $table->string('reason2', 160)->nullable();
            $table->string('perubahan_ok_13_ke_ok_1', 160)->nullable();

            $table->dateTime('time_upload3') ->nullable();
            $table->string('status3',  10)->nullable();
            $table->string('reason3', 160)->nullable();
            $table->string('keselamatan_(tahunan)', 160)->nullable();

            $table->dateTime('time_upload4') ->nullable();
            $table->string('status4', 10)->nullable();
            $table->string('reason4', 160)->nullable();
            $table->string('keselamatan_(dok)', 160)->nullable();

            $table->dateTime('time_upload5') ->nullable();
            $table->string('status5', 10)->nullable();
            $table->string('reason5', 160)->nullable();
            $table->string('keselamatan_(pengaturan_dok)', 160)->nullable();

            $table->dateTime('time_upload6') ->nullable();
            $table->string('status6', 10)->nullable();
            $table->string('reason6', 160)->nullable();
            $table->string('keselamatan_(penundaan_dok)', 160)->nullable();

            $table->dateTime('time_upload7') ->nullable();
            $table->string('status7', 10)->nullable();
            $table->string('reason7', 160)->nullable();
            $table->string('sertifikat_garis_muat', 160)->nullable();

            $table->dateTime('time_upload8') ->nullable();
            $table->string('status8', 10)->nullable();
            $table->string('reason8', 160)->nullable();
            $table->string('laporan_pemeriksaan_garis_muat', 160)->nullable();

            $table->dateTime('time_upload9') ->nullable();
            $table->string('status9', 10)->nullable();
            $table->string('reason9', 160)->nullable();
            $table->string('sertifikat_anti_fauling', 160)->nullable();

            $table->dateTime('time_upload10') ->nullable();
            $table->string('status10', 10)->nullable();
            $table->string('reason10', 160)->nullable();
            $table->string('surat_laut_permanen', 160)->nullable();

            $table->dateTime('time_upload11') ->nullable();
            $table->string('status11', 10)->nullable();
            $table->string('reason11', 160)->nullable();
            $table->string('surat_laut_endorse', 160)->nullable();

            $table->dateTime('time_upload12') ->nullable();
            $table->string('status12', 10)->nullable();
            $table->string('reason12', 160)->nullable();
            $table->string('call_sign', 160)->nullable();

            $table->dateTime('time_upload13') ->nullable();
            $table->string('status13', 10)->nullable();
            $table->string('reason13', 160)->nullable();
            $table->string('perubahan_sertifikat_keselamatan', 160)->nullable();

            $table->dateTime('time_upload14') ->nullable();
            $table->string('status14', 10)->nullable();
            $table->string('reason14', 160)->nullable();
            $table->string('perubahan_kawasan_tanpa_notadin', 160)->nullable();

            $table->dateTime('time_upload15') ->nullable();
            $table->string('status15', 10)->nullable();
            $table->string('reason15', 160)->nullable();
            $table->string('snpp_permanen', 160)->nullable();

            $table->dateTime('time_upload16') ->nullable();
            $table->string('status16', 10)->nullable();
            $table->string('reason16', 160)->nullable();
            $table->string('snpp_endorse', 160)->nullable();

            $table->dateTime('time_upload17') ->nullable();
            $table->string('status17', 10)->nullable();
            $table->string('reason17', 160)->nullable();
            $table->string('laporan_pemeriksaan_snpp', 160)->nullable();;

            $table->dateTime('time_upload18') ->nullable();
            $table->string('status18', 10)->nullable();
            $table->string('reason18', 160)->nullable();
            $table->string('laporan_pemeriksaan_keselamatan', 160)->nullable();

            $table->dateTime('time_upload19') ->nullable();
            $table->string('status19', 10)->nullable();
            $table->string('reason19', 160)->nullable();
            $table->string('buku_kesehatan', 160)->nullable();

            $table->dateTime('time_upload20') ->nullable();
            $table->string('status20', 10)->nullable();
            $table->string('reason20' , 160)->nullable();
            $table->string('sertifikat_sanitasi_water&p3k', 160)->nullable();

            $table->dateTime('time_upload21') ->nullable();
            $table->string('status21', 10)->nullable();
            $table->string('reason21' , 160)->nullable();
            $table->string('pengaturan_non_ke_klas_bki', 160)->nullable();

            $table->dateTime('time_upload22') ->nullable();
            $table->string('status22', 10)->nullable();
            $table->string('reason22' , 160)->nullable();
            $table->string('pengaturan_klas_bki_(dok_ss)', 160)->nullable();

            $table->dateTime('time_upload23') ->nullable();
            $table->string('status23', 10)->nullable();
            $table->string('reason23' , 160)->nullable();
            $table->string('surveyor_endorse_tahunan_bki', 160)->nullable();

            $table->dateTime('time_upload24') ->nullable();
            $table->string('status24', 10)->nullable();
            $table->string('reason24' , 160)->nullable();
            $table->string('pr_supplier_bki', 160)->nullable();

            $table->dateTime('time_upload25') ->nullable();
            $table->string('status25', 10)->nullable();
            $table->string('reason25' , 160)->nullable();
            $table->string('balik_nama_grosse', 160)->nullable();

            $table->dateTime('time_upload26') ->nullable();
            $table->string('status26', 10)->nullable();
            $table->string('reason26' , 160)->nullable();
            $table->string('kapal_baru_body_(set_dokumen)', 160)->nullable();

            $table->dateTime('time_upload27') ->nullable();
            $table->string('status27', 10)->nullable();
            $table->string('reason27' , 160)->nullable();
            $table->string('halaman_tambahan_grosse', 160)->nullable();

            $table->dateTime('time_upload28') ->nullable();
            $table->string('status28', 10)->nullable();
            $table->string('reason28' , 160)->nullable();
            $table->string('pnbp&pup', 160)->nullable();

            $table->dateTime('time_upload29') ->nullable();
            $table->string('status29', 10)->nullable();
            $table->string('reason29' , 160)->nullable();
            $table->string('laporan_pemeriksaan_anti_teriti', 160)->nullable();

            $table->dateTime('time_upload30') ->nullable();
            $table->string('status30', 10)->nullable();
            $table->string('reason30', 160)->nullable();
            $table->string('surveyor_pengedokan', 160)->nullable();

            $table->dateTime('time_upload31') ->nullable();
            $table->string('status31', 10)->nullable();
            $table->string('reason31', 160)->nullable();
            $table->string('surveyor_penerimaan_klas_bki', 160)->nullable();

            $table->dateTime('time_upload32') ->nullable();
            $table->string('status32', 10)->nullable();
            $table->string('reason32', 160)->nullable();
            $table->string('nota_tagihan_jasa_perkapalan', 160)->nullable();

            $table->dateTime('time_upload33') ->nullable();
            $table->string('status33', 10)->nullable();
            $table->string('reason33', 160)->nullable();
            $table->string('gambar_kapal_baru_(bki)', 160)->nullable();

            $table->dateTime('time_upload34') ->nullable();
            $table->string('status34', 10)->nullable();
            $table->string('reason34', 160)->nullable();
            $table->string('dana_jaminan_(clc)', 160)->nullable();

            $table->dateTime('time_upload35') ->nullable();
            $table->string('status35', 10)->nullable();
            $table->string('reason35', 160)->nullable();
            $table->string('surat_ukur_dalam_negeri', 160)->nullable();

            $table->dateTime('time_upload36') ->nullable();
            $table->string('status36', 10)->nullable();
            $table->string('reason36', 160)->nullable();
            $table->string('penerbitan_sertifikat_kapal_baru', 160)->nullable();

            $table->dateTime('time_upload37') ->nullable();
            $table->string('status37', 10)->nullable();
            $table->string('reason37', 160)->nullable();
            $table->string('buku_stabilitas', 160)->nullable();

            $table->dateTime('time_upload38') ->nullable();
            $table->string('status38', 10)->nullable();
            $table->string('reason38', 160)->nullable();
            $table->string('grosse_akta', 160)->nullable();
            
            
            $table->dateTime('time_upload39') ->nullable();
            $table->string('status39', 10)->nullable();
            $table->string('reason39', 160)->nullable();
            $table->string('penerbitan_nota_dinas_pertama', 160)->nullable();
            
            $table->dateTime('time_upload40') ->nullable();
            $table->string('status40', 10)->nullable();
            $table->string('reason40', 160)->nullable();
            $table->string('penerbitan_nota_dinas_kedua', 160)->nullable();
            
            $table->dateTime('time_upload41') ->nullable();
            $table->string('status41', 10)->nullable();
            $table->string('reason41', 160)->nullable();
            $table->string('BKI_Lambung', 160)->nullable();
            
            $table->dateTime('time_upload42') ->nullable();
            $table->string('status42', 10)->nullable();
            $table->string('reason42', 160)->nullable();
            $table->string('BKI_Mesin', 160)->nullable();

            $table->dateTime('time_upload43') ->nullable();
            $table->string('status43', 10)->nullable();
            $table->string('reason43', 160)->nullable();
            $table->string('BKI_Garis_Muat', 160)->nullable();
            
            $table->dateTime('time_upload44') ->nullable();
            $table->string('status44', 10)->nullable();
            $table->string('reason44', 160)->nullable();
            $table->string('Lain_Lain1', 160)->nullable();

            $table->dateTime('time_upload45') ->nullable();
            $table->string('status45', 10)->nullable();
            $table->string('reason45', 160)->nullable();
            $table->string('Lain_Lain2', 160)->nullable();
            
            $table->dateTime('time_upload46') ->nullable();
            $table->string('status46', 10)->nullable();
            $table->string('reason46', 160)->nullable();
            $table->string('Lain_Lain3', 160)->nullable();

            $table->dateTime('time_upload47') ->nullable();
            $table->string('status47', 10)->nullable();
            $table->string('reason47', 160)->nullable();
            $table->string('Lain_Lain4', 160)->nullable();

            $table->dateTime('time_upload48') ->nullable();
            $table->string('status48', 10)->nullable();
            $table->string('reason48', 160)->nullable();
            $table->string('Lain_Lain5', 160)->nullable();

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
        Schema::dropIfExists('samarindadb');
    }
}
