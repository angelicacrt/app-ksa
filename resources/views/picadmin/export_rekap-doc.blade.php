<table>
    <thead>
        <tr>
                <th scope="col" style="text-align: center">No.PR : </th>
            @foreach($document as $doc )
                <th scope="col" style="text-align: center">{{$doc->no_PR}}</th>
            @endforeach
            @foreach($documentberau as $d )
                <th scope="col" style="text-align: center">{{$d->no_PR}}</th>
            @endforeach
            @foreach($documentbanjarmasin as $b )
                <th scope="col" style="text-align: center">{{$b->no_PR}}</th>
            @endforeach
            @foreach($documentsamarinda as $s )
                <th scope="col" style="text-align: center">{{$s->no_PR}}</th>
            @endforeach
            @foreach($documentjakarta as $jkt )
                <th scope="col" style="text-align: center">{{$jkt->no_PR}}</th>
            @endforeach
        </tr>
        <tr>
            <th scope="col" style="text-align: center">Time Uploaded</th>
            <th scope="col" style="text-align: center">No.Permohonan</th>
            <th scope="col" style="text-align: center">Cabang</th>
            <th scope="col" style="text-align: center">Nama Kapal</th>
            <th scope="col" style="text-align: center">Periode (Y-M-D)</th>
            <th scope="col" style="text-align: center">Nama File</th>
            <th scope="col" style="text-align: center">Dana diajukan</th>
            <th scope="col" style="text-align: center">Status</th>
            <th scope="col" style="text-align: center">Reason</th>
            {{-- <th scope="col">Action</th> --}}
        </tr>
    </thead>
    <tbody>
    {{-- Babelan------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ --}} 
        @foreach($document as $doc )
            @for ( $a = 1 ; $a <= 24 ; $a++)
            @php
                $BABELAN = array('sertifikat_keselamatan',
                    'sertifikat_garis_muat','penerbitan_sekali_jalan','sertifikat_safe_manning',
                    'endorse_surat_laut','perpanjangan_sertifikat_sscec','perpanjangan_sertifikat_p3k' ,
                    'biaya_laporan_dok','pnpb_sertifikat_keselamatan','pnpb_sertifikat_garis_muat',
                    'pnpb_surat_laut','sertifikat_snpp','sertifikat_anti_teritip',    
                    'pnbp_snpp&snat','biaya_survey' ,'pnpb_sscec', 'bki_lambung', 'bki_mesin', 'bki_Garis_muat',
                    'Sertifikat_Konstruksi_Kapal_Barang' , 'Sertifikat_Radio_Kapal_Barang' , 'PNBP_Safe_Maning' , 'Lain_Lain1' , 'Lain_Lain2');

                $names = array('Sertifikat Keselamatan' , 'Sertifikat Garis Muat' , 'Penerbitan 1 Kali Jalan' , 'Sertifikat Safe Manning' ,
                    'Endorse Surat Laut' , 'Perpanjangan Sertifikat SSCEC' , 'Perpanjangan Sertifikat P3K' , 'Biaya Laporan Dok' , 
                    'PNPB Sertifikat Keselamatan' , 'PNPB Sertifikat Garis Muat' , 'PNPB Surat Laut'  , 'Sertifikat SNPP' ,
                    'Sertifikat Anti Teritip' , 'PNBP SNPP & SNAT', 'Biaya Survey' , 'PNPB SSCEC', 'BKI Lambung', 'BKI Mesin', 'BKI Garis Muat',
                    'Sertifikat Konstruksi Kapal Barang' , 'Sertifikat Radio Kapal Barang' , 'PNBP Safe Maning' , 'File extra 1' , 'File extra 2');
                $time_upload ="time_upload".$a;
                $stats ="status".$a;
                $reason = "reason".$a;
                $dana = "dana".$a;
                $scan = $BABELAN[$a-1];
            @endphp
            @if(empty($doc->$stats))
            @elseif($doc->$stats == 'approved')
                <tr>
                    <td>{{ $doc->$time_upload }}</td>
                    <td>{{ $doc->no_mohon }}</td>
                    <td>{{ $doc->cabang }}</td>
                    <td>{{$doc->nama_kapal}}</td>                                        
                    <td>{{$doc->periode_awal}} To {{$doc->periode_akhir}}</td>                                   
                    <td>{{$names[$a-1]}}</td>    
                    <td>{{str_replace(',', '', $doc->$dana)}}</td>   
                    <td>{{$doc->$stats}} By {{$doc->approved_by}}</td>                                      
                    <td>{{$doc ->$reason}}</td>
                </tr>
            @endif
            @endfor
        @endforeach
    {{-- Berau----------------------------------------------------------------------------------------------------------------------------------------------------------------------------- --}}
        @foreach($documentberau as $d )
        @for ( $a = 1 ; $a <= 34 ; $a++)
        @php
            $BERAU = array('pnbp_sertifikat_konstruksi','jasa_urus_sertifikat',
                'pnbp_sertifikat_perlengkapan','pnbp_sertifikat_radio','pnbp_sertifikat_ows',
                'pnbp_garis_muat','pnbp_pemeriksaan_endorse_sl','pemeriksaan_sertifikat',
                'marine_inspektor','biaya_clearance','pnbp_master_cable', 
                'cover_deck_logbook','cover_engine_logbook','exibitum_dect_logbook',
                'exibitum_engine_logbook','pnbp_deck_logbook','pnbp_engine_logbook',
                'biaya_docking','lain-lain','biaya_labuh_tambat',
                'biaya_rambu','pnbp_pemeriksaan','sertifikat_bebas_sanitasi&p3k',
                'sertifikat_garis_muat','pnpb_sscec','ijin_sekali_jalan', 'bki_lambung', 'bki_mesin', 'bki_Garis_muat',
                'Lain_Lain1' , 'Lain_Lain2' , 'Lain_Lain3' , 'Lain_Lain4' , 'Lain_Lain5');

            $names = array('PNBP Sertifikat Konstruksi','Jasa Urus Sertifikat','PNBP Sertifikat Perlengkapan',
                            'PNBP Sertifikat Radio','PNBP Sertifikat OWS','PNBP Garis Muat',
                            'PNBP Pemeriksaan Endorse SL','Pemeriksaan Sertifikat','Marine Inspektor',
                            'Biaya Clearance','PNBP Master Cable','Cover Deck LogBook',
                            'Cover Engine LogBook','Exibitum Dect LogBook','Exibitum Engine LogBook',
                            'PNBP Deck Logbook','PNBP Engine Logbook','Biaya Docking',
                            'Lain-lain','Biaya Labuh Tambat','Biaya Rambu',
                            'PNBP Pemeriksaan','Sertifikat Bebas Sanitasi & P3K','Sertifikat Garis Muat',
                            'PNBP SSCEC','Ijin Sekali Jalan', 'BKI Lambung', 'BKI Mesin', 'BKI Garis Muat',
                            'File extra 1' , 'File extra 2' , 'File extra 3' , 'File extra 4' , 'File extra 5');
            $time_upload ="time_upload".$a;
            $stats ="status".$a;
            $reason = "reason".$a;
            $dana = "dana".$a;
            $scan = $BERAU[$a-1];
        @endphp
        <input type="hidden" name='status' value={{$stats}}>
        @if(empty($d->$stats))
        @elseif($d->$stats == 'approved')
            <tr>
                <td>{{ $d->$time_upload }}</td>
                <td>{{ $d->no_mohon }}</td>
                <td>{{ $d->cabang }}</td>
                <td>{{$d->nama_kapal}}</td>                                        
                <td>{{$d->periode_awal}} To {{$d->periode_akhir}}</td>                                   
                <td>{{$names[$a-1]}}</td>     
                <td>{{str_replace(',', '', $d->$dana)}}</td>  
                <td>{{$d->$stats}} By {{$d->approved_by}}</td>                                      
                <td>{{$d->$reason}}</td>    
            </tr>
        @endif
        @endfor
        @endforeach
    {{-- Banjarmasin---------------------------------------------------------------------------------------------------------------------------------------------------------------------- --}}
        @foreach($documentbanjarmasin as $b )
        @for ( $a = 1 ; $a <= 39 ; $a++)
        @php
            $BANJARMASIN = array('perjalanan','sertifikat_keselamatan','sertifikat_anti_fauling','surveyor',
                                'drawing&stability','laporan_pengeringan','berita_acara_lambung',
                                'laporan_pemeriksaan_nautis','laporan_pemeriksaan_anti_faulin','laporan_pemeriksaan_radio',
                                'laporan_pemeriksaan_snpp','bki','snpp_permanen',
                                'snpp_endorse','surat_laut_endorse','surat_laut_permanen',
                                'compas_seren','keselamatan_(tahunan)','keselamatan_(pengaturan_dok)',
                                'keselamatan_(dok)','garis_muat','dispensasi_isr',
                                'life_raft_1_2_pemadam','sscec','seatrail',
                                'laporan_pemeriksaan_umum','laporan_pemeriksaan_mesin','nota_dinas_perubahan_kawasan',
                                'PAS','invoice_bki','safe_manning', 'bki_lambung', 'bki_mesin', 'bki_Garis_muat',
                                'Lain_Lain1' , 'Lain_Lain2' , 'Lain_Lain3' , 'Lain_Lain4' , 'Lain_Lain5');
            $names = array('Perjalanan','Sertifikat Keselamatan','Sertifikat Anti Fauling','Surveyor',
                        'Drawing & Stability','Laporan Pengeringan','Berita Acara Lambung',
                        'Laporan Pemeriksaan Nautis','Laporan Pemeriksaan Anti Faulin','Laporan Pemeriksaan Radio ',
                        'Berita Acara Lambung','Laporan Pemeriksaan SNPP','BKI',
                        'SNPP Permanen','SNPP Endorse','Surat Laut Endorse',
                        'Surat Laut Permanen','Compas Seren','Keselamatan (Tahunan)',
                        'Keselamatan (Pengaturan Dok)','Keselamatan (Dok)','Garis Muat',
                        'Dispensasi ISR','Life Raft 1 2, Pemadam',
                        'SSCEC','Seatrail','Laporan Pemeriksaan Umum',
                        'Laporan Pemeriksaan Mesin','Nota Dinas Perubahan Kawasan','PAS',
                        'Invoice BKI','Safe Manning', 'BKI Lambung', 'BKI Mesin', 'BKI Garis Muat',
                        'File extra 1' , 'File extra 2' , 'File extra 3' , 'File extra 4' , 'File extra 5');
            $time_upload ="time_upload".$a;
            $stats ="status".$a;
            $reason = "reason".$a;
            $dana = "dana".$a;
            $scan = $BANJARMASIN[$a-1];
        @endphp
        <input type="hidden" name='status' value={{$stats}}>
        @if(empty($b->$stats))
        @elseif($b->$stats == 'approved')
            <tr>
                <td>{{ $b->$time_upload }}</td>
                <td>{{ $b->no_mohon }}</td>
                <td>{{ $b->cabang }}</td>
                <td>{{$b->nama_kapal}}</td>                                        
                <td>{{$b->periode_awal}} To {{$b->periode_akhir}}</td>                                   
                <td>{{$names[$a-1]}}</td> 
                <td>{{str_replace(',', '', $b->$dana)}}</td>      
                <td>{{$b->$stats}} By {{$b->approved_by}}</td>                                      
                <td>{{$b->$reason}}</td>
            </tr>
        @endif
        @endfor
        @endforeach
    {{-- Samarinda------------------------------------------------------------------------------------------------------------------------------------------------------------------------ --}}
        @foreach($documentsamarinda as $s )
        @for ( $a = 1 ; $a <= 48 ; $a++)
        @php
            $SAMARINDA = array('sertifikat_keselamatan(perpanjangan)','perubahan_ok_13_ke_ok_1',
                                'keselamatan_(tahunan)','keselamatan_(dok)','keselamatan_(pengaturan_dok)',
                                'keselamatan_(penundaan_dok)','sertifikat_garis_muat','laporan_pemeriksaan_garis_muat',
                                'sertifikat_anti_fauling','surat_laut_permanen','surat_laut_endorse',
                                'call_sign','perubahan_sertifikat_keselamatan','perubahan_kawasan_tanpa_notadin',
                                'snpp_permanen','snpp_endorse','laporan_pemeriksaan_snpp',
                                'laporan_pemeriksaan_keselamatan','buku_kesehatan','sertifikat_sanitasi_water&p3k',
                                'pengaturan_non_ke_klas_bki','pengaturan_klas_bki_(dok_ss)','surveyor_endorse_tahunan_bki',
                                'pr_supplier_bki','balik_nama_grosse','kapal_baru_body_(set_dokumen)',
                                'halaman_tambahan_grosse','pnbp&pup','laporan_pemeriksaan_anti_teriti',
                                'surveyor_pengedokan','surveyor_penerimaan_klas_bki','nota_tagihan_jasa_perkapalan',
                                'gambar_kapal_baru_(bki)','dana_jaminan_(clc)','surat_ukur_dalam_negeri',
                                'penerbitan_sertifikat_kapal_baru','buku_stabilitas','grosse_akta',
                                'penerbitan_nota_dinas_pertama' , 'penerbitan_nota_dinas_kedua', 'BKI_Lambung', 'BKI_Mesin', 'BKI_Garis_Muat',
                                'Lain_Lain1' , 'Lain_Lain2' , 'Lain_Lain3' , 'Lain_Lain4' , 'Lain_Lain5');

            $names = array("Sertifikat Keselamatan (Perpanjangan)","Perubahan OK 13 ke OK 1","Keselamatan (Tahunan)",
                            "Keselamatan (Dok)","Keselamatan (Pengaturan Dok)","Keselamatan (Penundaan Dok)",
                            "Sertifikat Garis Muat","Laporan Pemeriksaan Garis Muat","Sertifikat Anti Fauling",
                            'Surat Laut Permanen','Surat Laut Endorse','Call Sign',
                            'Perubahan Sertifikat Keselamatan','Perubahan Kawasan Tanpa NotaDin',
                            'SNPP Permanen','SNPP Endorse','Laporan Pemeriksaan SNPP',
                            'Laporan Pemeriksaan Keselamatan','Buku Kesehatan','Sertifikat Sanitasi Water & P3K',
                            'Pengaturan Non ke Klas BKI','Pengaturan Klas BKI (Dok SS)','Surveyor Endorse Tahunan BKI',
                            'PR Supplier bki','Balik Nama Grosse','Kapal Baru Body (Set Dokumen)',
                            'Halaman Tambahan Grosse','PNBP & PUP','Laporan Pemeriksaan Anti Teriti',
                            'Surveyor Pengedokan','Surveyor Penerimaan Klas BKI','Nota Tagihan Jasa Perkapalan',
                            'Gambar Kapal Baru (BKI)','Dana Jaminan (CLC)','Surat Ukur Dalam Negeri',
                            'Penerbitan Sertifikat Kapal Baru','Buku Stabilitas','Grosse Akta',
                            'Penerbitan Nota Dinas Pertama', 'Penerbitan Nota Dinas Kedua', 'BKI Lambung', 'BKI Mesin', 'BKI Garis Muat',
                            'File extra 1' , 'File extra 2' , 'File extra 3' , 'File extra 4' , 'File extra 5');
            $time_upload ="time_upload".$a;
            $stats ="status".$a;
            $reason = "reason".$a;
            $dana = "dana".$a;
            $scan = $SAMARINDA[$a-1];
        @endphp
            <input type="hidden" name='status' value={{$stats}}>
            @if(empty($s->$stats))
            @elseif($s->$stats == 'approved')
                <tr>
                    <td>{{ $s->$time_upload }}</td>
                    <td>{{ $s->no_mohon }}</td>
                    <td>{{ $s->cabang }}</td>
                    <td>{{$s->nama_kapal}}</td>                                        
                    <td>{{$s->periode_awal}} To {{$s->periode_akhir}}</td>                                   
                    <td>{{$names[$a-1]}}</td>     
                    <td>{{str_replace(',', '', $s->$dana)}}</td>  
                    <td>{{$s->$stats}} By {{$s->approved_by}}</td>                                      
                    <td>{{$s->$reason}}</td>    
                </tr>
            @endif
            @endfor
                
            @endforeach
    {{-- Jakarta------------------------------------------------------------------------------------------------------------------------------------------------------------------------ --}}          
        @foreach($documentjakarta as $jkt )
            @for ( $a = 1 ; $a <= 47 ; $a++)
            @php
                $JAKARTA = array('pnbp_rpt','pps','pnbp_spesifikasi_kapal'
                                    ,'anti_fauling_permanen','pnbp_pemeriksaan_anti_fauling','snpp_permanen'
                                    ,'pengesahan_gambar','surat_laut_permanen','pnbp_surat_laut'
                                    ,'pnbp_surat_laut_(ubah_pemilik)','clc_bunker','nota_dinas_penundaan_dok_i'
                                    ,'nota_dinas_penundaan_dok_ii','nota_dinas_perubahan_kawasan' ,'call_sign'
                                    ,'perubahan_kepemilikan_kapal','nota_dinas_bendera_(baru)','pup_safe_manning'
                                    ,'corporate','dokumen_kapal_asing_(baru)','rekomendasi_radio_kapal'
                                    ,'izin_stasiun_radio_kapal','mmsi','pnbp_pemeriksaan_konstruksi'
                                    ,'ok_1_skb','ok_1_skp','ok_1_skr'
                                    ,'status_hukum_kapal','autorization_garis_muat','otorisasi_klas'
                                    ,'pnbp_otorisasi(all)','halaman_tambah_grosse_akta','pnbp_surat_ukur'
                                    ,'nota_dinas_penundaan_klas_bki_ss','uwild_pengganti_doking','update_nomor_call_sign '
                                    ,'clc_badan_kapal','wreck_removal' , 'biaya_percepatan_proses' , 'BKI_Lambung', 'BKI_Mesin', 'BKI_Garis_Muat'
                                    ,'Lain_Lain1' , 'Lain_Lain2' , 'Lain_Lain3' , 'Lain_Lain4' , 'Lain_Lain5');
                $names = array('PNBP RPT','PPS','PNBP Spesifikasi Kapal'
                                ,'Anti Fauling Permanen','PNBP Pemeriksaan Anti Fauling','SNPP Permanen'
                                ,'Pengesahan Gambar','Surat Laut Permanen','PNBP Surat Laut'
                                ,'PNBP Surat Laut (Ubah Pemilik)','CLC Bunker','Nota Dinas Penundaan Dok I'
                                ,'Nota Dinas Penundaan Dok II','Nota Dinas Perubahan Kawasan','Call Sign'
                                ,'Perubahan Kepemilikan Kapal','Nota Dinas Bendera (Baru)','PUP Safe Manning'
                                ,'Corporate','Dokumen Kapal Asing (Baru)'
                                ,'Rekomendasi Radio Kapal','Izin Stasiun Radio Kapal','MMSI'
                                ,'PNBP Pemeriksaan Konstruksi','OK 1 SKB','OK 1 SKP','OK 1 SKR'
                                ,'Status Hukum Kapal','Autorization Garis Muat','Otorisasi Klas'
                                ,'PNBP Otorisasi (AII)','Halaman Tambah Grosse Akta','PNBP Surat Ukur'
                                ,'Nota Dinas Penundaan Klas BKI SS','UWILD Pengganti Doking','Update Nomor Call Sign'
                                ,'CLC Badan Kapal','Wreck Removal', 'Biaya Percepatan Proses' , 'BKI Lambung', 'BKI Mesin', 'BKI Garis Muat',
                                'File extra 1' , 'File extra 2' , 'File extra 3' , 'File extra 4' , 'File extra 5'
                                );
                $time_upload ="time_upload".$a;
                $stats ="status".$a;
                $reason = "reason".$a;
                $dana = "dana".$a;
                $scan = $JAKARTA[$a-1];
            @endphp
                <input type="hidden" name='status' value={{$stats}}>
                @if(empty($jkt->$stats))
                @elseif($jkt->$stats == 'approved')
                    <tr>
                        <td>{{ $jkt->$time_upload }}</td>
                        <td>{{ $jkt->no_mohon }}</td>
                        <td>{{ $jkt->cabang }}</td>
                        <td>{{$jkt->nama_kapal}}</td>                                        
                        <td>{{$jkt->periode_awal}} To {{$jkt->periode_akhir}}</td>                                   
                        <td>{{$names[$a-1]}}</td>     
                        <td>{{str_replace(',', '', $jkt->$dana)}}</td>  
                        <td>{{$jkt->$stats}} By {{$jkt->approved_by}}</td>                                      
                        <td>{{$jkt->$reason}}</td>    
                    </tr>
                @endif
            @endfor
        @endforeach
    </tbody>
</table>