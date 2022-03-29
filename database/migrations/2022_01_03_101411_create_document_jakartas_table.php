<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentJakartasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_jakartas', function (Blueprint $table) {
            $table->id();
            $table->string('cabang',10)->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            
            $table->string('nama_kapal',60)->nullable();
            $table->string('upload_type',10)->nullable();
            $table->string('approved_by',20)->nullable();
            $table->date('periode_awal')->nullable();
            $table->date('periode_akhir')->nullable();

            $table->dateTime('time_upload1')->nullable();
            $table->string('reason1', 90)->nullable();
            $table->string('status1', 9)->nullable();
            $table->string('dana1',14)->nullable();
            $table->string('pnbp_rpt', 100)->nullable();

            $table->dateTime('time_upload2')->nullable();
            $table->string('reason2', 90)->nullable();
            $table->string('status2', 9)->nullable();
            $table->string('dana2',14)->nullable();
            $table->string('pps', 100)->nullable();

            $table->dateTime('time_upload3')->nullable();
            $table->string('reason3', 90)->nullable();
            $table->string('status3', 9)->nullable();
            $table->string('dana3',14)->nullable();
            $table->string('pnbp_spesifikasi_kapal', 100)->nullable();

            $table->dateTime('time_upload4')->nullable();
            $table->string('reason4', 90)->nullable();
            $table->string('status4', 9)->nullable();
            $table->string('dana4',14)->nullable();
            $table->string('anti_fauling_permanen', 100)->nullable();

            $table->dateTime('time_upload5')->nullable();
            $table->string('reason5', 90)->nullable();
            $table->string('status5', 9)->nullable();
            $table->string('dana5',14)->nullable();
            $table->string('pnbp_pemeriksaan_anti_fauling', 100)->nullable();

            $table->dateTime('time_upload6')->nullable();
            $table->string('reason6', 90)->nullable();
            $table->string('status6', 9)->nullable();
            $table->string('dana6',14)->nullable();
            $table->string('snpp_permanen', 100)->nullable();

            $table->dateTime('time_upload7')->nullable();
            $table->string('reason7', 90)->nullable();
            $table->string('status7', 9)->nullable();
            $table->string('dana7',14)->nullable();
            $table->string('pengesahan_gambar', 100)->nullable();

            $table->dateTime('time_upload8')->nullable();
            $table->string('reason8', 90)->nullable();
            $table->string('status8', 9)->nullable();
            $table->string('dana8',14)->nullable();
            $table->string('surat_laut_permanen', 100)->nullable();

            $table->dateTime('time_upload9')->nullable();
            $table->string('reason9', 90)->nullable();
            $table->string('status9', 9)->nullable();
            $table->string('dana9',14)->nullable();
            $table->string('pnbp_surat_laut', 100)->nullable();

            $table->dateTime('time_upload10')->nullable();
            $table->string('reason10', 90)->nullable();
            $table->string('dana10',14)->nullable();
            $table->string('status10', 9)->nullable();
            $table->string('pnbp_surat_laut_(ubah_pemilik)', 100)->nullable();

            // <!-- belas puluhan -->
            $table->dateTime('time_upload11')->nullable();
            $table->string('reason11', 90)->nullable();
            $table->string('dana11',14)->nullable();
            $table->string('status11', 9)->nullable();
            $table->string('clc_bunker', 100)->nullable();

            $table->dateTime('time_upload12')->nullable();
            $table->string('reason12', 90)->nullable();
            $table->string('dana12',14)->nullable();
            $table->string('status12', 9)->nullable();
            $table->string('nota_dinas_penundaan_dok_i', 100)->nullable();

            $table->dateTime('time_upload13')->nullable();
            $table->string('reason13', 90)->nullable();
            $table->string('dana13',14)->nullable();
            $table->string('status13', 9)->nullable();
            $table->string('nota_dinas_penundaan_dok_ii', 100)->nullable();

            $table->dateTime('time_upload14')->nullable();
            $table->string('reason14', 90)->nullable();
            $table->string('dana14',14)->nullable();
            $table->string('status14', 9)->nullable();
            $table->string('nota_dinas_perubahan_kawasan' , 100)->nullable();

            $table->dateTime('time_upload15')->nullable();
            $table->string('reason15', 90)->nullable();
            $table->string('dana15',14)->nullable();
            $table->string('status15', 9)->nullable();
            $table->string('call_sign', 100)->nullable();

            $table->dateTime('time_upload16')->nullable();
            $table->string('reason16', 90)->nullable();
            $table->string('dana16',14)->nullable();
            $table->string('status16', 9)->nullable();
            $table->string('perubahan_kepemilikan_kapal', 100)->nullable();

            $table->dateTime('time_upload17')->nullable();
            $table->string('reason17', 90)->nullable();
            $table->string('dana17',14)->nullable();
            $table->string('status17', 9)->nullable();
            $table->string('nota_dinas_bendera_(baru)', 100)->nullable();

            $table->dateTime('time_upload18')->nullable();
            $table->string('reason18', 90)->nullable();
            $table->string('dana18',14)->nullable();
            $table->string('status18', 9)->nullable();
            $table->string('pup_safe_manning', 100)->nullable();

            $table->dateTime('time_upload19')->nullable();
            $table->string('reason19', 90)->nullable();
            $table->string('dana19',14)->nullable();
            $table->string('status19', 9)->nullable();
            $table->string('corporate', 100)->nullable();

            $table->dateTime('time_upload20')->nullable();
            $table->string('reason20', 90)->nullable();
            $table->string('dana20',14)->nullable();
            $table->string('status20', 9)->nullable();
            $table->string('dokumen_kapal_asing_(baru)', 100)->nullable();

            // <!-- dua puluhan -->
            $table->dateTime('time_upload21')->nullable();
            $table->string('reason21', 90)->nullable();
            $table->string('dana21',14)->nullable();
            $table->string('status21', 9)->nullable();
            $table->string('rekomendasi_radio_kapal', 100)->nullable();

            $table->dateTime('time_upload22')->nullable();
            $table->string('reason22', 90)->nullable();
            $table->string('dana22',14)->nullable();
            $table->string('status22', 9)->nullable();
            $table->string('izin_stasiun_radio_kapal', 100)->nullable();

            $table->dateTime('time_upload23')->nullable();
            $table->string('reason23', 90)->nullable();
            $table->string('dana23',14)->nullable();
            $table->string('status23', 9)->nullable();
            $table->string('mmsi', 100)->nullable();

            $table->dateTime('time_upload24')->nullable();
            $table->string('reason24', 90)->nullable();
            $table->string('dana24',14)->nullable();
            $table->string('status24', 9)->nullable();
            $table->string('pnbp_pemeriksaan_konstruksi', 100)->nullable();

            $table->dateTime('time_upload25')->nullable();
            $table->string('reason25', 90)->nullable();
            $table->string('dana25',14)->nullable();
            $table->string('status25', 9)->nullable();
            $table->string('ok_1_skb', 100)->nullable();

            $table->dateTime('time_upload26')->nullable();
            $table->string('reason26', 90)->nullable();
            $table->string('dana26',14)->nullable();
            $table->string('status26', 9)->nullable();
            $table->string('ok_1_skp', 100)->nullable();

            $table->dateTime('time_upload27')->nullable();
            $table->string('reason27', 90)->nullable();
            $table->string('dana27',14)->nullable();
            $table->string('status27', 9)->nullable();
            $table->string('ok_1_skr', 100)->nullable();

            $table->dateTime('time_upload28')->nullable();
            $table->string('reason28', 90)->nullable();
            $table->string('dana28',14)->nullable();
            $table->string('status28', 9)->nullable();
            $table->string('status_hukum_kapal', 19)->nullable();

            $table->dateTime('time_upload29')->nullable();
            $table->string('reason29', 90)->nullable();
            $table->string('dana29',14)->nullable();
            $table->string('status29', 9)->nullable();
            $table->string('autorization_garis_muat', 100)->nullable();

            $table->dateTime('time_upload30')->nullable();
            $table->string('reason30', 90)->nullable();
            $table->string('dana30',14)->nullable();
            $table->string('status30', 9)->nullable();
            $table->string('otorisasi_klas', 100)->nullable();

            // <!-- tiga puluhan -->
            $table->dateTime('time_upload31')->nullable();
            $table->string('reason31', 90)->nullable();
            $table->string('dana31',14)->nullable();
            $table->string('status31', 9)->nullable();
            $table->string('pnbp_otorisasi(all)', 100)->nullable();

            $table->dateTime('time_upload32')->nullable();
            $table->string('reason32', 90)->nullable();
            $table->string('dana32',14)->nullable();
            $table->string('status32', 9)->nullable();
            $table->string('halaman_tambah_grosse_akta', 100)->nullable();

            $table->dateTime('time_upload33')->nullable();
            $table->string('reason33', 90)->nullable();
            $table->string('dana33',14)->nullable();
            $table->string('status33', 9)->nullable();
            $table->string('pnbp_surat_ukur', 100)->nullable();

            $table->dateTime('time_upload34')->nullable();
            $table->string('reason34', 90)->nullable();
            $table->string('dana34',14)->nullable();
            $table->string('status34', 9)->nullable();
            $table->string('nota_dinas_penundaan_klas_bki_ss', 100)->nullable();

            $table->dateTime('time_upload35')->nullable();
            $table->string('reason35', 90)->nullable();
            $table->string('dana35',14)->nullable();
            $table->string('status35', 9)->nullable();
            $table->string('uwild_pengganti_doking', 100)->nullable();

            $table->dateTime('time_upload36')->nullable();
            $table->string('reason36', 90)->nullable();
            $table->string('dana36',14)->nullable();
            $table->string('status36', 9)->nullable();
            $table->string('update_nomor_call_sign', 100)->nullable();

            $table->dateTime('time_upload37')->nullable();
            $table->string('reason37', 90)->nullable();
            $table->string('dana37',14)->nullable();
            $table->string('status37', 9)->nullable();
            $table->string('clc_badan_kapal', 100)->nullable();

            $table->dateTime('time_upload38')->nullable();
            $table->string('reason38', 90)->nullable();
            $table->string('dana38',14)->nullable();
            $table->string('status38', 9)->nullable();
            $table->string('wreck_removal', 100)->nullable();

            $table->dateTime('time_upload39') ->nullable();
            $table->string('status39', 9)->nullable();
            $table->string('reason39', 90)->nullable();
            $table->string('dana39',14)->nullable();
            $table->string('biaya_percepatan_proses', 100)->nullable();

            $table->dateTime('time_upload40') ->nullable();
            $table->string('status40', 9)->nullable();
            $table->string('reason40', 90)->nullable();
            $table->string('dana40',14)->nullable();
            $table->string('BKI_Lambung', 100)->nullable();
            
            $table->dateTime('time_upload41') ->nullable();
            $table->string('status41', 9)->nullable();
            $table->string('reason41', 90)->nullable();
            $table->string('dana41',14)->nullable();
            $table->string('BKI_Mesin', 100)->nullable();

            $table->dateTime('time_upload42') ->nullable();
            $table->string('status42', 9)->nullable();
            $table->string('reason42', 90)->nullable();
            $table->string('dana42',14)->nullable();
            $table->string('BKI_Garis_Muat', 100)->nullable();

            $table->dateTime('time_upload43') ->nullable();
            $table->string('status43', 9)->nullable();
            $table->string('reason43', 90)->nullable();
            $table->string('dana43',14)->nullable();
            $table->string('Lain_Lain1', 100)->nullable();

            $table->dateTime('time_upload44') ->nullable();
            $table->string('status44', 9)->nullable();
            $table->string('reason44', 90)->nullable();
            $table->string('dana44',14)->nullable();
            $table->string('Lain_Lain2', 100)->nullable();

            $table->dateTime('time_upload45') ->nullable();
            $table->string('status45', 9)->nullable();
            $table->string('reason45', 90)->nullable();
            $table->string('dana45',14)->nullable();
            $table->string('Lain_Lain3', 100)->nullable();

            $table->dateTime('time_upload46') ->nullable();
            $table->string('status46', 9)->nullable();
            $table->string('reason46', 90)->nullable();
            $table->string('dana46',14)->nullable();
            $table->string('Lain_Lain4', 100)->nullable();

            $table->dateTime('time_upload47') ->nullable();
            $table->string('status47', 9)->nullable();
            $table->string('reason47', 90)->nullable();
            $table->string('dana47',14)->nullable();
            $table->string('Lain_Lain5', 100)->nullable();
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
        Schema::dropIfExists('document_jakartas');
    }
}
