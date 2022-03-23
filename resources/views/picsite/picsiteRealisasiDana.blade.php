@extends('../layouts.base')

@section('title', 'PicSite Realisasi Dana')

@section('container')
<div class="row">
    @include('picsite.sidebarpic')
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="row">
            <div class="col" style="margin-top: 15px">
                <div class="jumbotron jumbotron-fluid" >
                    <div class="container">
                      <h1 class="display-4">Upload Realisasi Fund Form</h1>
                        <p class="lead">please only upload file with .PDF format only and size is not more than 3 MB.</p>
                        <br>
                        <button class="btn btn-danger" id="topsubmit" style="margin-left: 80%; width: 20%;" onClick="">Submit</button>

                        @if($errors->any())
                            @foreach ($errors->all() as $error)
                                <div class="alert error alert-danger" id="error">{{ $error }}
                                    <strong>Please check the file is a PDF and Size 3MB.</strong>
                                </div>
                            @endforeach
                        @endif
                        
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success alert-block" id="success">
                                <strong>{{ $message }}</strong>
                            </div>
                        @endif
                       
                        <form action="/picsite/upload" method="post" enctype="multipart/form-data" name="formUpload" id="formUpload">
                            @csrf
                            <input type="hidden" name='type_upload' value="Fund_Real" />
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Nama Kapal</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">Tug</span>
                                        <input list="Nama_kapals" name="nama_kapal" id="nama_kapal" placeholder="Nama tug" style="text-transform: uppercase;" class="col-lg-full custom-select custom-select-md">
                                        <datalist id="Nama_kapals">
                                            @foreach ($tug as $t)
                                                <option value="{{ $t -> tugName }}">{{ $t -> tugName }}</option>
                                            @endforeach
                                        </datalist>
                                        <span class="input-group-text">Barge</span>
                                        <input list="nama_tug_barges" class="col-lg-full custom-select custom-select-md" style="text-transform: uppercase;" placeholder="Nama Barge" name="Nama_Barge" id="Nama_Barge" >
                                        <datalist id="nama_tug_barges">
                                            @foreach ($barge as $b)
                                                <option value="{{ $b -> bargeName }}">{{ $b -> bargeName }}</option>
                                            @endforeach
                                        </datalist>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label>from</label>
                                    <input type="date" class="form-control" name="tgl_awal"  required placeholder="Periode Awal">
                                </div>
                                <div class="col-md-3">
                                    <label>to</label>
                                    <input type="date" class="form-control" name="tgl_akhir" required placeholder="Periode Akhir">
                                </div>
                        
                        <table class="table"style="margin-top: 1%">
                            <thead class="thead-dark" >
                                <tr>
                                    <th scope="col">No.</th>
                                    <th scope="col">Nama File</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
{{--Babelan ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- --}}
                                    @if (Auth::user()->cabang == 'Babelan')
                                    @for ($a = 1 ; $a <= 24 ; $a++)
                                        @php
                                            $name = array('Sertifikat Keselamatan','Sertifikat Garis Muat' ,'Penerbitan 1 Kali Jalan','Sertifikat Safe Manning',
                                                        'Endorse Surat Laut','Perpanjangan Sertifikat SSCEC','Perpanjangan Sertifikat P3K',
                                                        'Biaya Laporan Dok','PNPB Sertifikat Keselamatan','PNPB Sertifikat Garis Muat',
                                                        'PNPB Surat Laut','Sertifikat SNPP','Sertifikat Anti Teritip',
                                                        'PNBP SNPP & SNAT','Biaya Survey','PNPB SSCEC' , 'BKI Lambung', 'BKI Mesin', 'BKI Garis Muat' ,
                                                        'File extra 1' , 'File extra 2' , 'File extra 3' , 'File extra 4' , 'File extra 5');
                                            $ufile = 'ufile'.$a;
                                            $time_upload ="time_upload".$a;
                                            $stats ="status".$a;
                                            $reason ="reason".$a;
                                            $dana = "dana".$a;
                                            $date = date('Y-m-28');
                                        @endphp
                                        <tr>
                                            <td class=table-primary>{{ $a }}</td>
                                            <td class=table-primary id="nama"><strong>{{$name[$a-1]}}</td>
                                            <td class=table-light>
                                                <div class="input-group mb-3">
                                                    <input type="file" class="form-control" name={{$ufile}} id="ufile">
                                                </div>
                                            </td>  
                                        </tr>
                                    @endfor
                                    @endif
{{--Berau ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- --}}
                                    @if (Auth::user()->cabang == 'Berau')
                                    @for ($a = 1 ; $a <= 34 ; $a++)    
                                    @php
                                        $name = array('PNBP Sertifikat Konstruksi','Jasa Urus Sertifikat','PNBP Sertifikat Perlengkapan',
                                        'PNBP Sertifikat Radio','PNBP Sertifikat OWS','PNBP Garis Muat',
                                        'PNBP Pemeriksaan Endorse SL','Pemeriksaan Sertifikat','Marine Inspektor',
                                        'Biaya Clearance','PNBP Master Cable','Cover Deck LogBook',
                                        'Cover Engine LogBook','Exibitum Dect LogBook','Exibitum Engine LogBook',
                                        'PNBP Deck Logbook','PNBP Engine Logbook','Biaya Docking',
                                        'Lain-lain','Biaya Labuh Tambat','Biaya Rambu',
                                        'PNBP Pemeriksaan','Sertifikat Bebas Sanitasi & P3K','Sertifikat Garis Muat',
                                        'PNBP SSCEC','Ijin Sekali Jalan' , 'BKI Lambung', 'BKI Mesin', 'BKI Garis Muat' ,
                                        'File extra 1' , 'File extra 2' , 'File extra 3' , 'File extra 4' , 'File extra 5');
                                        $beraufile = 'beraufile'.$a;
                                        $time_upload ="time_upload".$a;
                                        $stats ="status".$a;
                                        $reason ="reason".$a;
                                        $date = date('Y-m-28');
                                    @endphp
                                        <tr>
                                            <td class=table-primary>{{$a}}</td>   
                                            <td class=table-primary id="nama"><strong>{{$name[$a-1]}}</td>
                                            <td class=table-light>
                                                <div class="input-group mb-3">
                                                    <input type="file" class="form-control" name="{{$beraufile}}" id="beraufile">
                                                </div>
                                            </td>  
                                        </tr>
                                    @endfor
                                    @endif
{{--Banjarmasin --------------------------------------------------------------------------------------------------------------------------------------------------------------- --}}
                            @if (Auth::user()->cabang == 'Banjarmasin' or Auth::user()->cabang == 'Bunati')
                                @for ($a = 1 ; $a <= 39 ; $a++)
                                    @php
                                        $name = array('Perjalanan','Sertifikat Keselamatan','Sertifikat Anti Fauling','Surveyor',
                                                    'Drawing & Stability','Laporan Pengeringan','Berita Acara Lambung',
                                                    'Laporan Pemeriksaan Nautis','Laporan Pemeriksaan Anti Faulin','Laporan Pemeriksaan Radio ',
                                                    'Laporan Pemeriksaan SNPP','BKI',
                                                    'SNPP Permanen','SNPP Endorse','Surat Laut Endorse',
                                                    'Surat Laut Permanen','Compas Seren','Keselamatan (Tahunan)',
                                                    'Keselamatan (Pengaturan Dok)','Keselamatan (Dok)','Garis Muat',
                                                    'Dispensasi ISR','Life Raft 1 2, Pemadam',
                                                    'SSCEC','Seatrail','Laporan Pemeriksaan Umum',
                                                    'Laporan Pemeriksaan Mesin','Nota Dinas Perubahan Kawasan','PAS',
                                                    'Invoice BKI','Safe Manning', 'BKI Lambung', 'BKI Mesin', 'BKI Garis Muat',
                                                    'File extra 1' , 'File extra 2' , 'File extra 3' , 'File extra 4' , 'File extra 5');
                                        $banjarmasinfile = 'banjarmasinfile'.$a;
                                        $time_upload ="time_upload".$a;
                                        $stats ="status".$a;
                                        $reason ="reason".$a;
                                        $date = date('Y-m-28');
                                    @endphp
                                    <tr>   
                                        <td class=table-primary>{{ $a }}</td>
                                        <td class=table-primary id="nama"><strong>{{$name[$a-1]}}</td>
                                            <td class=table-light>
                                                <div class="input-group mb-3">
                                                    <input type="file" class="form-control" name="{{$banjarmasinfile}}" id="banjarmasinfile">
                                                </div>
                                            </td>  
                                    </tr>      
                                @endfor
                            @endif
{{--Samarinda ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- --}}
                                    @if (Auth::user()->cabang == 'Samarinda' or Auth::user()->cabang == 'Kendari' or Auth::user()->cabang == 'Morosi')
                                    @for ($a = 1 ; $a <= 48 ; $a++)
                                    @php
                                        $name = array("Sertifikat Keselamatan (Perpanjangan)","Perubahan OK 13 ke OK 1","Keselamatan (Tahunan)",
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
                                                        'Penerbitan Sertifikat Kapal Baru','Buku Stabilitas','Grosse Akta' ,
                                                        'Penerbitan Nota Dinas Pertama' , 'Penerbitan Nota Dinas Kedua',  'BKI Lambung', 'BKI Mesin', 'BKI Garis Muat',
                                                        'File extra 1' , 'File extra 2' , 'File extra 3' , 'File extra 4' , 'File extra 5');
                                        $samarindafile = 'samarindafile'.$a;
                                        $time_upload ="time_upload".$a;
                                        $stats ="status".$a;
                                        $reason ="reason".$a;
                                        $date = date('Y-m-28');
                                    @endphp
                                        <tr>
                                            <td class=table-primary>{{ $a }}</td>   
                                            <td class=table-primary id="nama"><strong>{{$name[$a-1]}}</td>
                                                <td class=table-light>
                                                    <div class="input-group mb-3">
                                                        <input type="file" class="form-control" name="{{$samarindafile}}" id="samarindafile">
                                                    </div>
                                                </td>  
                                        </tr>     
                                        @endfor
                                    @endif
{{-- jakarta--------------------------------------------------------------------------------------------------------------------------------------------------------------------------- --}}
                                    @if (Auth::user()->cabang == 'Jakarta')
                                    @for ($a = 1 ; $a <= 47 ; $a++)
                                        @php
                                            $name = array('PNBP RPT','PPS','PNBP Spesifikasi Kapal'
                                            ,'Anti Fauling Permanen','PNBP Pemeriksaan Anti Fauling','SNPP Permanen'
                                            ,'Pengesahan Gambar','Surat Laut Permanen','PNBP Surat Laut'
                                            ,'PNBP Surat Laut (Ubah Pemilik)','CLC Bunker','Nota Dinas Penundaan Dok I'
                                            ,'Nota Dinas Penundaan Dok II','Nota Dinas Perubahan Kawasan','Call Sign'
                                            ,'Perubahan Kepemilikan Kapal','Nota Dinas Bendera (Baru)','PUP Safe Manning'
                                            ,'Corporate','Dokumen Kapal Asing (Baru)','Rekomendasi Radio Kapal'
                                            ,'Izin Stasiun Radio Kapal','MMSI'
                                            ,'PNBP Pemeriksaan Konstruksi','OK 1 SKB','OK 1 SKP','OK 1 SKR'
                                            ,'Status Hukum Kapal','Autorization Garis Muat','Otorisasi Klas'
                                            ,'PNBP Otorisasi (AII)','Halaman Tambah Grosse Akta','PNBP Surat Ukur'
                                            ,'Nota Dinas Penundaan Klas BKI SS','UWILD Pengganti Doking','Update Nomor Call Sign'
                                            ,'CLC Badan Kapal','Wreck Removal' , 'Biaya Percepatan Proses','BKI Lambung', 'BKI Mesin', 'BKI Garis Muat' ,
                                            'File extra 1' , 'File extra 2' , 'File extra 3' , 'File extra 4' , 'File extra 5'
                                            );
                                            $jktfile = 'jktfile'.$a;
                                            $time_upload ="time_upload".$a;
                                            $stats ="status".$a;
                                            $reason ="reason".$a;
                                            $dana ="dana".$a;
                                            $date = date('Y-m-28');
                                        @endphp
                                        <tr>
                                            <td class=table-primary>{{ $a }}</td>
                                            <td class=table-primary id="nama"><strong>{{$name[$a-1]}}</td>
                                            <td class=table-light>
                                                <div class="input-group mb-3">
                                                    <input type="file" class="form-control" name={{$jktfile}} id="jktfile">
                                                </div>
                                            </td>  
                                        </tr>
                                    @endfor
                                    @endif
                                </tbody>   
                        </table>
                    </form>
                        <button class="btn btn-danger" id="realsubmit" style="margin-left: 50%; display: none;" type="submit" name="Submit" value="Upload" onClick="">Submit</button>
                        <script>
                            //tombol submit
                            document.getElementById('topsubmit').addEventListener('click', openDialog);
                            function openDialog() {
                                document.getElementById('realsubmit').click();
                            }
                            // timeout notification
                            setTimeout(function(){
                            $("div.alert").remove();
                            }, 5000 ); // 5 secs
                        </script>
                        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
                    </div>
                </div>
            </div>   
            </div>
        </div>
    </main>
</div>
@endsection