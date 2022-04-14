@extends('../layouts.base')

@section('title', 'PicAdmin Dashboard')

@section('container')
<div class="row">
    @include('picadmin.picAdminsidebar')
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h2>Welcome back, {{ Auth::user()->name }} ! - Admin</h2>
            <h2>Cabang : {{ Auth::user()->cabang }}</h2>
            <h3>
                <div id="txt"></div>

                <script>
                    function startTime() {
                    const today = new Date();
                    let h = today.getHours();
                    let m = today.getMinutes();
                    let s = today.getSeconds();
                    m = checkTime(m);
                    s = checkTime(s);
                    document.getElementById('txt').innerHTML =  h + ":" + m + ":" + s;
                    setTimeout(startTime, 1000);
                    }
                    
                    function checkTime(i) {
                    if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
                        return i;
                    }
                </script>
            </h3>
            <div class="jumbotron jumbotron-fluid" >
                <h1 class="Header-1" style="margin-top: -3%;">Overview - Fund Request</h1>
                <hr class="my-4">
                <form method="GET" action="/dashboard/search" role="search">
                <div class="row">
                    <div class="col">
                        <div class="auto-cols-auto">
                            <div class="col" style="margin-left:-1%" >
                                <div class="input-group">
                                <div class="input-group-prepend">
                                </div>
                                <input type="text" style="text-transform: uppercase;" name="search_kapal" id="search_kapal" class="form-control" placeholder="Search Nama Kapal" autofocus>
                                <button type="submit" class="btn btn-info">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                    </svg>
                                </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <select name="search" id="cabangfilter"class="form-select" >
                            <option selected disabled hidden='true' value="">Pilih Cabang</option>
                            <option value="All">Semua Cabang</option>
                            <option value="Babelan">Babelan</option>
                            <option value="Berau">Berau</option>
                            <option value="Samarinda">Samarinda</option>
                            <option value="Banjarmasin">Banjarmasin</option>
                            <option value="Jakarta">Jakarta</option>
                            <option value="Kendari">Kendari</option>
                            <option value="Morosi">Morosi</option>
                        </select>
                    </div>
                    <div class="col">
                        <div class="d-flex justify-content-end">
                        @if($searchresult == 'Babelan')
                            {{$document->links()}}
                        @elseif($searchresult == 'Berau')
                            {{$documentberau->links()}}
                        @elseif($searchresult == 'Banjarmasin')
                            {{$documentbanjarmasin->links()}}
                        @elseif($searchresult == 'Samarinda' or $searchresult == 'Kendari' or $searchresult == 'Morosi')
                            {{$documentsamarinda->links()}}
                        @elseif($searchresult == 'Jakarta')
                            {{$documentjakarta->links()}}
                        @endif
                        </div>
                    </div>
                    <div class="col">
                        <a class="btn btn-outline-danger" style="margin-left:40%" href="/picadmin/dashboard-Realisasi">Change to Realisasi Dana</a>
                    </div>
                    <div class="col">
                        <a class="btn btn-outline-danger"  href="/picadmin/dashboard-RPK">Change to RPK</a>
                    </div>
                </div>
            </form>
                
                @if($errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="alert error alert-danger" id="error">{{ $error }}
                            <strong> No data found</strong>
                        </
                        <tr>
                    <td>
                        {{-- pisah beda nama kapal --}}
                    </td>
                </tr>div>
                    @endforeach
                @endif

                  <script>
                    setTimeout(function(){
                    $("div.alert").remove();
                    }, 5000 ); // 5 secs
                  </script>
                 <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

                <table id="content" class="table" style="margin-top: 1%">
                  <thead class="thead-dark">
                      <tr>
                        <th>Time Uploaded</th>
                        <th>No.Permohonan</th>
                        <th>Cabang</th>
                        <th>Nama Kapal</th>
                        <th>Periode (Y-M-D)</th>
                        <th>Nama File</th>
                        <th>Jenis File</th>
                        <th>Dana diajukan</th>
                        <th>Status</th>
                        <th>Reason</th>
                        <th>Action</th>
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
                                'Lain_Lain1' , 'Lain_Lain2' , 'Lain_Lain3' , 'Lain_Lain4' , 'Lain_Lain5');

                            $names = array('Sertifikat Keselamatan' , 'Sertifikat Garis Muat' , 'Penerbitan 1 Kali Jalan' , 'Sertifikat Safe Manning' ,
                                'Endorse Surat Laut' , 'Perpanjangan Sertifikat SSCEC' , 'Perpanjangan Sertifikat P3K' , 'Biaya Laporan Dok' , 
                                'PNPB Sertifikat Keselamatan' , 'PNPB Sertifikat Garis Muat' , 'PNPB Surat Laut'  , 'Sertifikat SNPP' ,
                                'Sertifikat Anti Teritip' , 'PNBP SNPP & SNAT', 'Biaya Survey' , 'PNPB SSCEC', 'BKI Lambung', 'BKI Mesin', 'BKI Garis Muat',
                                'File extra 1' , 'File extra 2' , 'File extra 3' , 'File extra 4' , 'File extra 5');
                          $time_upload ="time_upload".$a;
                          $stats ="status".$a;
                          $reason = "reason".$a;
                          $date = date('Y-m-28');
                          $dana = "dana".$a;
                          $scan = $BABELAN[$a-1];
                      @endphp
                    @if(empty($doc->$stats))
                        <tr>
                            {{-- agar tidak keluar hasil kosong --}}
                        </tr>
                    {{-- @elseif ($doc->$stats == 'on review')
                        <tr>
                            hasil on review
                            <td class="table-warning"><strong>{{ $doc->$time_upload }}</strong></td>
                            <td class="table-warning"><strong>{{ $doc->cabang }}</strong></td>
                            <td class="table-warning" style="text-transform: uppercase;"id="namakapal">{{$doc->nama_kapal}}</td>                                        
                            <td class="table-warning" id="periode"><strong>{{$doc->periode_awal}} To {{$doc->periode_akhir}}</strong></td>                                   
                            <td class="table-warning" id="namafile">{{$names[$a-1]}}</td>  
                            <td class="table-dark" id="jenisfile"><strong>DANA</strong></td>
                            <td class="table-primary"><strong>RP. {{$doc->$dana}}</strong></td>     
                            <td class="table-warning" style="text-transform: uppercase;" id="status"><strong>{{$doc->$stats}}</td>                                      
                            <td class="table-warning" id="reason">{{$doc ->$reason}}</td>
                            <td class="table-warning">
                                <form method="post" action="/dashboard/dana/view" target="_blank">
                                    @csrf
                                    <input type="hidden" name = 'cabang' value={{$doc->cabang}}>
                                    <input type="hidden" name='viewdoc' value={{$BABELAN[$a-1]}} />
                                    <input type="hidden" name='result' value={{$doc->$scan}} />
                                    <input type="hidden" name = 'tipefile' value='DANA'>
                                    <input type="hidden" name = 'kapal_nama' value={{$doc->nama_kapal}}>
                                    <button type="submit" name="views3" class="btn btn-dark">view</button>
                                </form>
                            </td>                                     
                        </tr> --}}
                    @elseif($doc->$stats == 'approved')
                        <tr>
                            <td class="table-success"><strong>{{ $doc->$time_upload }}</strong></td>
                            <td class="table-success"><strong>{{ $doc->no_mohon }}</strong></td>
                            <td class="table-success"><strong>{{ $doc->cabang }}</strong></td>
                            <td class="table-success" style="text-transform: uppercase;" id="namakapal">{{$doc->nama_kapal}}</td>                                        
                            <td class="table-success" id="periode"><strong>{{$doc->periode_awal}} To {{$doc->periode_akhir}}</strong></td>                                   
                            <td class="table-success" id="namafile">{{$names[$a-1]}}</td>    
                            <td class="table-dark" id="jenisfile"><strong>DANA</strong></td>
                            <td class="table-primary"><strong>RP. {{$doc->$dana}}</strong></td>   
                            <td class="table-success" style="text-transform: uppercase;" id="status"><strong>{{$doc->$stats}} By {{$doc->approved_by}}</td>                                      
                            <td class="table-success" id="reason">{{$doc ->$reason}}</td>                                        
                            <td class="table-success">
                                <form method="post" action="/dashboard/dana/view" target="_blank">
                                    @csrf
                                    <input type="hidden" name = 'cabang' value={{$doc->cabang}}>
                                    <input type="hidden" name='created_at_Year' value={{Carbon\Carbon::parse($doc->created_at)->format('Y')}} />
                                    <input type="hidden" name='created_at_month' value={{Carbon\Carbon::parse($doc->created_at)->format('m')}} />
                                    <input type="hidden" name='viewdoc' value={{$BABELAN[$a-1]}} />
                                    <input type="hidden" name='result' value={{$doc->$scan}} />
                                    <input type="hidden" name = 'tipefile' value='DANA'>
                                    <input type="hidden" name = 'kapal_nama' value={{$doc->nama_kapal}}>
                                    <button type="submit" name="views3" class="btn btn-dark">view</button>
                                </form>
                            </td>
                        </tr>
                    @elseif($doc->$stats == 'rejected')
                        <tr>
                            <td class="table-danger"><strong>{{ $doc->$time_upload }}</strong></td>
                            <td class="table-danger"><strong>{{ $doc->no_mohon }}</strong></td>
                            <td class="table-danger"><strong>{{ $doc->cabang }}</strong></td>
                            <td class="table-danger" style="text-transform: uppercase;" id="namakapal">{{$doc->nama_kapal}}</td>                                        
                            <td class="table-danger" id="periode"><strong>{{$doc->periode_awal}} To {{$doc->periode_akhir}}</strong></td>                                   
                            <td class="table-danger" id="namafile">{{$names[$a-1]}}</td>     
                            <td class="table-dark" id="jenisfile"><strong>DANA</strong></td>
                            <td class="table-primary"><strong>RP. {{$doc->$dana}}</strong></td>  
                            <td class="table-danger" style="text-transform: uppercase;" id="status"><strong>{{$doc->$stats}}</td>                                      
                            <td class="table-danger" id="reason">{{$doc ->$reason}}</td>
                            <td class="table-danger">
                                <form method="post" action="/dashboard/dana/view" target="_blank">
                                    @csrf
                                    <input type="hidden" name = 'cabang' value={{$doc->cabang}}>
                                    <input type="hidden" name='created_at_Year' value={{Carbon\Carbon::parse($doc->created_at)->format('Y')}} />
                                    <input type="hidden" name='created_at_month' value={{Carbon\Carbon::parse($doc->created_at)->format('m')}} />
                                    <input type="hidden" name='viewdoc' value={{$BABELAN[$a-1]}} />
                                    <input type="hidden" name='result' value={{$doc->$scan}} />
                                    <input type="hidden" name = 'tipefile' value='DANA'>
                                    <input type="hidden" name = 'kapal_nama' value={{$doc->nama_kapal}}>
                                    <button type="submit" name="views3" class="btn btn-dark">view</button>
                                </form>
                            </td>    
                        </tr>
                    @else
                        <tr>
                            
                        </tr>
                    @endif
                    @endfor
                    <tr>
                        <td>
                            {{-- pisah beda nama kapal --}}
                        </td>
                    </tr>
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
                    $date = date('Y-m-28');
                    $dana = "dana".$a;
                    $scan = $BERAU[$a-1];
                @endphp
                <input type="hidden" name='status' value={{$stats}}>
                @if(empty($d->$stats))
                <tr>
                    {{-- agar tidak keluar hasil kosong --}}
                </tr>
                {{-- @elseif ($d->$stats == 'on review')
                    <tr>
                        hasil on review
                        <td class="table-warning"><strong>{{ $d->$time_upload }}</strong></td>
                        <td class="table-warning"><strong>{{ $d->cabang }}</strong></td>
                        <td class="table-warning" style="text-transform: uppercase;" id="namakapal">{{$d->nama_kapal}}</td>                                        
                        <td class="table-warning" id="periode"><strong>{{$d->periode_awal}} To {{$d->periode_akhir}}</strong></td>                                   
                        <td class="table-warning" id="namafile">{{$names[$a-1]}}</td>  
                        <td class="table-dark" id="jenisfile"><strong>DANA</strong></td>
                        <td class="table-primary"><strong>RP. {{$doc->$dana}}</strong></td>     
                        <td class="table-warning" style="text-transform: uppercase;" id="status"><strong>{{$d->$stats}}</td>                                      
                        <td class="table-warning" id="reason">{{$d ->$reason}}</td>
                        <td class="table-warning">
                            <form method="post" action="/dashboard/dana/view" target="_blank">
                                @csrf
                                <input type="hidden" name = 'cabang' value={{$d->cabang}}>
                                <input type="hidden" name='viewdoc' value={{$BERAU[$a-1]}} />
                                <input type="hidden" name='result' value={{$d->$scan}} />
                                <input type="hidden" name = 'tipefile' value='DANA'>
                                <input type="hidden" name = 'kapal_nama' value={{$d->nama_kapal}}>
                                <button type="submit" name="views3" class="btn btn-dark">view</button>
                            </form>
                        </td>    
                    </tr> --}}
                @elseif($d->$stats == 'approved')
                    <tr>
                        <td class="table-success"><strong>{{ $d->$time_upload }}</strong></td>
                        <td class="table-success"><strong>{{ $d->no_mohon }}</strong></td>
                        <td class="table-success"><strong>{{ $d->cabang }}</strong></td>
                        <td class="table-success" style="text-transform: uppercase;" id="namakapal">{{$d->nama_kapal}}</td>                                        
                        <td class="table-success" id="periode"><strong>{{$d->periode_awal}} To {{$d->periode_akhir}}</strong></td>                                   
                        <td class="table-success" id="namafile">{{$names[$a-1]}}</td>     
                        <td class="table-dark" id="jenisfile"><strong>DANA</strong></td>
                        <td class="table-primary"><strong>RP. {{$d->$dana}}</strong></td>  
                        <td class="table-success" style="text-transform: uppercase;" id="status"><strong>{{$d->$stats}} By {{$d->approved_by}}</td>                                      
                        <td class="table-success" id="reason">{{$d->$reason}}</td>    
                        <td class="table-success">
                            <form method="post" action="/dashboard/dana/view" target="_blank">
                                @csrf
                                <input type="hidden" name = 'cabang' value={{$d->cabang}}>
                                <input type="hidden" name='created_at_Year' value={{Carbon\Carbon::parse($d->created_at)->format('Y')}} />
                                <input type="hidden" name='created_at_month' value={{Carbon\Carbon::parse($d->created_at)->format('m')}} />
                                <input type="hidden" name='viewdoc' value={{$BERAU[$a-1]}} />
                                <input type="hidden" name='result' value={{$d->$scan}} />
                                <input type="hidden" name = 'tipefile' value='DANA'>
                                <input type="hidden" name = 'kapal_nama' value={{$d->nama_kapal}}>
                                <button type="submit" name="views3" class="btn btn-dark">view</button>
                            </form>
                        </td>                                        
                    </tr>
                @elseif($d->$stats == 'rejected')
                    <tr>
                        <td class="table-danger"><strong>{{ $d->$time_upload }}</strong></td>
                        <td class="table-danger"><strong>{{ $d->no_mohon }}</strong></td>
                        <td class="table-danger"><strong>{{ $d->cabang }}</strong></td>
                        <td class="table-danger" style="text-transform: uppercase;" id="namakapal">{{$d->nama_kapal}}</td>                                        
                        <td class="table-danger" id="periode"><strong>{{$d->periode_awal}} To {{$d->periode_akhir}}</strong></td>                                   
                        <td class="table-danger" id="namafile">{{$names[$a-1]}}</td>
                        <td class="table-dark" id="jenisfile"><strong>DANA</strong></td>
                        <td class="table-primary"><strong>RP. {{$d->$dana}}</strong></td>       
                        <td class="table-danger" style="text-transform: uppercase;" id="status"><strong>{{$d->$stats}} </td>                                      
                        <td class="table-danger" id="reason">{{$d->$reason}}</td>   
                        <td class="table-danger">
                            <form method="post" action="/dashboard/dana/view" target="_blank">
                                @csrf
                                <input type="hidden" name = 'cabang' value={{$d->cabang}}>
                                <input type="hidden" name='created_at_Year' value={{Carbon\Carbon::parse($d->created_at)->format('Y')}} />
                                    <input type="hidden" name='created_at_month' value={{Carbon\Carbon::parse($d->created_at)->format('m')}} />
                                <input type="hidden" name='viewdoc' value={{$BERAU[$a-1]}} />
                                <input type="hidden" name='result' value={{$d->$scan}} />
                                <input type="hidden" name = 'tipefile' value='DANA'>
                                <input type="hidden" name = 'kapal_nama' value={{$d->nama_kapal}}>
                                <button type="submit" name="views3" class="btn btn-dark">view</button>
                            </form>
                        </td>     
                    </tr>
                @else
                    <tr>
                                
                    </tr>
                @endif
                @endfor
                <tr>
                    <td>
                        {{-- pisah beda nama kapal --}}
                    </td>
                </tr>
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
                $date = date('Y-m-28');
                $dana = "dana".$a;
                $scan = $BANJARMASIN[$a-1];
            @endphp
            <input type="hidden" name='status' value={{$stats}}>
                @if(empty($b->$stats))
                <tr>
                    {{-- agar tidak keluar hasil kosong --}}
                </tr>
                @elseif($b->$stats == 'approved')
                    <tr>
                        <td class="table-success"><strong>{{ $b->$time_upload }}</strong></td>
                        <td class="table-success"><strong>{{ $b->no_mohon }}</strong></td>
                        <td class="table-success"><strong>{{ $b->cabang }}</strong></td>
                        <td class="table-success" style="text-transform: uppercase;" id="namakapal">{{$b->nama_kapal}}</td>                                        
                        <td class="table-success" id="periode"><strong>{{$b->periode_awal}} To {{$b->periode_akhir}}</strong></td>                                   
                        <td class="table-success" id="namafile">{{$names[$a-1]}}</td> 
                        <td class="table-dark" id="jenisfile"><strong>DANA</strong></td>
                        <td class="table-primary"><strong>RP. {{$b->$dana}}</strong></td>      
                        <td class="table-success" style="text-transform: uppercase;" id="status"><strong>{{$b->$stats}} By {{$b->approved_by}}</td>                                      
                        <td class="table-success" id="reason">{{$b->$reason}}</td>
                        <td class="table-success">
                            <form method="post" action="/dashboard/dana/view" target="_blank">
                                @csrf
                                <input type="hidden" name = 'cabang' value={{$b->cabang}}>
                                <input type="hidden" name='created_at_Year' value={{Carbon\Carbon::parse($b->created_at)->format('Y')}} />
                                <input type="hidden" name='created_at_month' value={{Carbon\Carbon::parse($b->created_at)->format('m')}} />
                                <input type="hidden" name='viewdoc' value={{$BANJARMASIN[$a-1]}} />
                                <input type="hidden" name='result' value={{$b->$scan}} />
                                <input type="hidden" name = 'tipefile' value='DANA'>
                                <input type="hidden" name = 'kapal_nama' value={{$b->nama_kapal}}>
                                <button type="submit" name="views3" class="btn btn-dark">view</button>
                            </form>
                        </td>                                          
                    </tr>
                @elseif($b->$stats == 'rejected')
                    <tr>
                        <td class="table-danger"><strong>{{ $b->$time_upload }}</strong></td>
                        <td class="table-danger"><strong>{{ $b->no_mohon }}</strong></td>
                        <td class="table-danger"><strong>{{ $b->cabang }}</strong></td>
                        <td class="table-danger" style="text-transform: uppercase;" id="namakapal">{{$b->nama_kapal}}</td>                                        
                        <td class="table-danger" id="periode"><strong>{{$b->periode_awal}} To {{$b->periode_akhir}}</strong></td>                                   
                        <td class="table-danger" id="namafile">{{$names[$a-1]}}</td>   
                        <td class="table-dark" id="jenisfile"><strong>DANA</strong></td>
                        <td class="table-primary"><strong>RP. {{$b->$dana}}</strong></td>    
                        <td class="table-danger" style="text-transform: uppercase;" id="status"><strong>{{$b->$stats}}</td>                                      
                        <td class="table-danger" id="reason">{{$b->$reason}}</td>
                        <td class="table-danger">
                            <form method="post" action="/dashboard/dana/view" target="_blank">
                                @csrf
                                <input type="hidden" name = 'cabang' value={{$b->cabang}}>
                                <input type="hidden" name='created_at_Year' value={{Carbon\Carbon::parse($b->created_at)->format('Y')}} />
                                    <input type="hidden" name='created_at_month' value={{Carbon\Carbon::parse($b->created_at)->format('m')}} />
                                <input type="hidden" name='viewdoc' value={{$BANJARMASIN[$a-1]}} />
                                <input type="hidden" name='result' value={{$b->$scan}} />
                                <input type="hidden" name = 'tipefile' value='DANA'>
                                <input type="hidden" name = 'kapal_nama' value={{$b->nama_kapal}}>
                                <button type="submit" name="views3" class="btn btn-dark">view</button>
                            </form>
                        </td>        
                    </tr>
                @else
                    <tr>
                        
                    </tr>
                @endif
                @endfor
                <tr>
                    <td>
                        {{-- pisah beda nama kapal --}}
                    </td>
                </tr>
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
                  $date = date('Y-m-28');
                  $dana = "dana".$a;
                  $scan = $SAMARINDA[$a-1];
                @endphp
                    <input type="hidden" name='status' value={{$stats}}>
                    @if(empty($s->$stats))
                    <tr>
                        {{-- agar tidak keluar hasil kosong --}}
                    </tr>
                    @elseif($s->$stats == 'approved')
                        <tr>
                            <td class="table-success"><strong>{{ $s->$time_upload }}</strong></td>
                            <td class="table-success"><strong>{{ $s->no_mohon }}</strong></td>
                            <td class="table-success"><strong>{{ $s->cabang }}</strong></td>
                            <td class="table-success" style="text-transform: uppercase;" id="namakapal">{{$s->nama_kapal}}</td>                                        
                            <td class="table-success" id="periode"><strong>{{$s->periode_awal}} To {{$s->periode_akhir}}</strong></td>                                   
                            <td class="table-success" id="namafile">{{$names[$a-1]}}</td>     
                            <td class="table-dark" id="jenisfile"><strong>DANA</strong></td>
                            <td class="table-primary"><strong>RP. {{$s->$dana}}</strong></td>  
                            <td class="table-success" style="text-transform: uppercase;" id="status"><strong>{{$s->$stats}} By {{$s->approved_by}}</td>                                      
                            <td class="table-success" id="reason">{{$s->$reason}}</td>    
                            <td class="table-success">
                                <form method="post" action="/dashboard/dana/view" target="_blank">
                                    @csrf
                                    <input type="hidden" name = 'cabang' value={{$s->cabang}}>
                                    <input type="hidden" name='created_at_Year' value={{Carbon\Carbon::parse($s->created_at)->format('Y')}} />
                                    <input type="hidden" name='created_at_month' value={{Carbon\Carbon::parse($s->created_at)->format('m')}} />
                                    <input type="hidden" name='viewdoc' value={{$SAMARINDA[$a-1]}} />
                                    <input type="hidden" name='result' value={{$s->$scan}} />
                                    <input type="hidden" name = 'tipefile' value='DANA'>
                                    <input type="hidden" name = 'kapal_nama' value={{$s->nama_kapal}}>
                                    <button type="submit" name="views3" class="btn btn-dark">view</button>
                                </form>
                            </td>                                                      
                        </tr>
                    @elseif($s->$stats == 'rejected')
                        <tr>
                            <td class="table-danger"><strong>{{ $s->$time_upload }}</strong></td>
                            <td class="table-danger"><strong>{{ $s->no_mohon }}</strong></td>
                            <td class="table-danger"><strong>{{ $s->cabang }}</strong></td>
                            <td class="table-danger" style="text-transform: uppercase;" id="namakapal">{{$s->nama_kapal}}</td>                                        
                            <td class="table-danger" id="periode"><strong>{{$s->periode_awal}} To {{$s->periode_akhir}}</strong></td>                                   
                            <td class="table-danger" id="namafile">{{$names[$a-1]}}</td>   
                            <td class="table-dark" id="jenisfile"><strong>DANA</strong></td>
                            <td class="table-primary"><strong>RP. {{$s->$dana}}</strong></td>    
                            <td class="table-danger" style="text-transform: uppercase;" id="status"><strong>{{$s->$stats}}</td>                                      
                            <td class="table-danger" id="reason">{{$s->$reason}}</td>    
                            <td class="table-danger">
                                <form method="post" action="/dashboard/dana/view" target="_blank">
                                    @csrf
                                    <input type="hidden" name='created_at_Year' value={{Carbon\Carbon::parse($s->created_at)->format('Y')}} />
                                    <input type="hidden" name='created_at_month' value={{Carbon\Carbon::parse($s->created_at)->format('m')}} />
                                    <input type="hidden" name = 'cabang' value={{$s->cabang}}>
                                    <input type="hidden" name='viewdoc' value={{$SAMARINDA[$a-1]}} />
                                    <input type="hidden" name='result' value={{$s->$scan}} />
                                    <input type="hidden" name = 'tipefile' value='DANA'>
                                    <input type="hidden" name = 'kapal_nama' value={{$s->nama_kapal}}>
                                    <button type="submit" name="views3" class="btn btn-dark">view</button>
                                </form>
                            </td>                  
                        </tr>
                        @else
                            <tr>
                                
                            </tr>
                        @endif
                        @endfor
                        <tr>
                            <td>
                                {{-- pisah beda nama kapal --}}
                            </td>
                        </tr>
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
                            $date = date('Y-m-28');
                            $dana = "dana".$a;
                            $scan = $JAKARTA[$a-1];
                        @endphp
                            <input type="hidden" name='status' value={{$stats}}>
                            @if(empty($jkt->$stats))
                            <tr>
                                {{-- agar tidak keluar hasil kosong --}}
                            </tr>
                            {{-- @elseif ($jkt->$stats == 'on review')
                            <tr>
                                hasil on review
                                <td class="table-warning"><strong>{{ $jkt->$time_upload }}</strong></td>
                                <td class="table-warning"><strong>{{ $jkt->cabang }}</strong></td>
                                <td class="table-warning" style="text-transform: uppercase;" id="namakapal">{{$jkt->nama_kapal}}</td>                                        
                                <td class="table-warning" id="periode"><strong>{{$jkt->periode_awal}} To {{$jkt->periode_akhir}}</strong></td>                                   
                                <td class="table-warning" id="namafile">{{$names[$a-1]}}</td>  
                                <td class="table-dark" id="jenisfile"><strong>DANA</strong></td>
                                <td class="table-primary"><strong>RP. {{$doc->$dana}}</strong></td>     
                                <td class="table-warning" style="text-transform: uppercase;" id="status"><strong>{{$jkt->$stats}}</td>                                      
                                <td class="table-warning" id="reason">{{$jkt ->$reason}}</td>   
                                <td class="table-warning">
                                    <form method="post" action="/dashboard/dana/view" target="_blank">
                                        @csrf
                                        <input type="hidden" name = 'cabang' value={{$jkt->cabang}}>
                                        <input type="hidden" name = 'kapal_nama' value={{$jkt->nama_kapal}}>
                                        <input type="hidden" name='viewdoc' value={{$JAKARTA[$a-1]}} />
                                        <input type="hidden" name='result' value={{$jkt->$scan}} />
                                        <input type="hidden" name = 'tipefile' value='DANA'>
                                        <button type="submit" name="views3" class="btn btn-dark">view</button>
                                    </form>
                                </td>                                                                   
                            </tr> --}}
                        @elseif($jkt->$stats == 'approved')
                            <tr>
                                <td class="table-success"><strong>{{ $jkt->$time_upload }}</strong></td>
                                <td class="table-success"><strong>{{ $jkt->no_mohon }}</strong></td>
                                <td class="table-success"><strong>{{ $jkt->cabang }}</strong></td>
                                <td class="table-success" style="text-transform: uppercase;" id="namakapal">{{$jkt->nama_kapal}}</td>                                        
                                <td class="table-success" id="periode"><strong>{{$jkt->periode_awal}} To {{$jkt->periode_akhir}}</strong></td>                                   
                                <td class="table-success" id="namafile">{{$names[$a-1]}}</td>     
                                <td class="table-dark" id="jenisfile"><strong>DANA</strong></td>
                                <td class="table-primary"><strong>RP. {{$jkt->$dana}}</strong></td>  
                                <td class="table-success" style="text-transform: uppercase;" id="status"><strong>{{$jkt->$stats}} By {{$jkt->approved_by}}</td>                                      
                                <td class="table-success" id="reason">{{$jkt->$reason}}</td>    
                                <td class="table-success">
                                    <form method="post" action="/dashboard/dana/view" target="_blank">
                                        @csrf
                                        <input type="hidden" name='created_at_Year' value={{Carbon\Carbon::parse($jkt->created_at)->format('Y')}} />
                                        <input type="hidden" name='created_at_month' value={{Carbon\Carbon::parse($jkt->created_at)->format('m')}} />
                                        <input type="hidden" name = 'cabang' value={{$jkt->cabang}}>
                                        <input type="hidden" name = 'kapal_nama' value={{$jkt->nama_kapal}}>
                                        <input type="hidden" name='viewdoc' value={{$JAKARTA[$a-1]}} />
                                        <input type="hidden" name='result' value={{$jkt->$scan}} />
                                        <input type="hidden" name = 'tipefile' value='DANA'>
                                        <button type="submit" name="views3" class="btn btn-dark">view</button>
                                    </form>
                                </td>                                                      
                            </tr>
                        @elseif($jkt->$stats == 'rejected')
                            <tr>
                                <td class="table-danger"><strong>{{ $jkt->$time_upload }}</strong></td>
                                <td class="table-danger"><strong>{{ $jkt->no_mohon }}</strong></td>
                                <td class="table-danger"><strong>{{ $jkt->cabang }}</strong></td>
                                <td class="table-danger" style="text-transform: uppercase;" id="namakapal">{{$jkt->nama_kapal}}</td>                                        
                                <td class="table-danger" id="periode"><strong>{{$jkt->periode_awal}} To {{$jkt->periode_akhir}}</strong></td>                                   
                                <td class="table-danger" id="namafile">{{$names[$a-1]}}</td>   
                                <td class="table-dark" id="jenisfile"><strong>DANA</strong></td>
                                <td class="table-primary"><strong>RP. {{$jkt->$dana}}</strong></td>    
                                <td class="table-danger" style="text-transform: uppercase;" id="status"><strong>{{$jkt->$stats}}</td>                                      
                                <td class="table-danger" id="reason">{{$jkt->$reason}}</td>    
                                <td class="table-danger">
                                    <form method="post" action="/dashboard/dana/view" target="_blank">
                                        @csrf
                                        <input type="hidden" name='created_at_Year' value={{Carbon\Carbon::parse($jkt->created_at)->format('Y')}} />
                                        <input type="hidden" name='created_at_month' value={{Carbon\Carbon::parse($jkt->created_at)->format('m')}} />
                                        <input type="hidden" name = 'cabang' value={{$jkt->cabang}}>
                                        <input type="hidden" name = 'kapal_nama' value={{$jkt->nama_kapal}}>
                                        <input type="hidden" name='viewdoc' value={{$JAKARTA[$a-1]}} />
                                        <input type="hidden" name='result' value={{$jkt->$scan}} />
                                        <input type="hidden" name = 'tipefile' value='DANA'>
                                        <button type="submit" name="views3" class="btn btn-dark">view</button>
                                    </form>
                                </td>                  
                            </tr>
                        @else
                            <tr>
                                
                            </tr>
                        @endif
                        @endfor
                        <tr>
                            <td>
                                {{-- pisah beda nama kapal --}}
                            </td>
                        </tr>
                        @endforeach
                  </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
    <style>
        h1 {text-align: center;}
    </style>
    <script type="text/javascript">
        function refreshDiv(){
            $('#content').load(location.href + ' #content')
        }
        setInterval(refreshDiv, 60000);
    </script>
@endsection 