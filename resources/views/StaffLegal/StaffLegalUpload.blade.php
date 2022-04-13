@extends('../layouts.base')

@section('title', 'Staff Legal Upload Form')

@section('container')
<div class="row">
    @include('StaffLegal.StaffLegalSidebar')
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="row">
            <div class="col" style="margin-top: 15px">
                <div class="jumbotron jumbotron-fluid" >
                    <div class="container">
                      <h1 class="display-4">Upload your Fund Request Form</h1>
                        <p class="lead">please only upload file with .PDF format only and size is not more than 3 MB.
                          <br>
                            Please upload your document request & fund request form  !
                        </p>
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
                       <br>
                        <form action="/Staff_Legal/upload" method="post" enctype="multipart/form-data" name="formUpload" id="formUpload">
                            @csrf
                            {{-- no.permohonan tidak di specify typenya karena secara default sudah text --}}
                            {{-- baca script di paling bawah --}}
                            <div class="col-md-6" style="margin-left: -1%">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">No.Permohonan</span>
                                    <input class="form-control" required name="no_mohon" placeholder="No.Permohonan">
                                </div>
                            </div>
                            <div class="col-md-6" style="margin-left: -1%">
                                <div class="input-group mb-3">
                                    <span class="input-group-text">No.PR</span>
                                    <input class="form-control" required name="no_PR" placeholder="No.PR">
                                </div>
                            </div>
                            <input type="hidden" name='type_upload' value="Fund_Req" />
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">Tug</span>
                                        <input list="Nama_kapals" name="nama_kapal" id="nama_kapal" placeholder="Nama tug" style="text-transform: uppercase;" class="col-lg-full custom-select custom-select-md">
                                        <datalist id="Nama_kapals">
                                            <option value="-">None</option>
                                            @foreach ($tug as $t)
                                                <option value="{{ $t -> tugName }}">{{ $t -> tugName }}</option>
                                            @endforeach
                                        </datalist>
                                        <span class="input-group-text">Barge</span>
                                        <input list="nama_tug_barges" class="col-lg-full custom-select custom-select-md" style="text-transform: uppercase;" placeholder="Nama Barge" name="Nama_Barge" id="Nama_Barge" >
                                        <datalist id="nama_tug_barges">
                                            <option value="-">None</option>
                                            @foreach ($barge as $b)
                                                <option value="{{ $b -> bargeName }}">{{ $b -> bargeName }}</option>
                                            @endforeach
                                        </datalist>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">From</span>
                                        <input type="date" class="form-control" name="tgl_awal"  required placeholder="Periode Awal">
    
                                        <span class="input-group-text">To</span>
                                        <input type="date" class="form-control" name="tgl_akhir" required placeholder="Periode Akhir">
                                    </div>
                                </div>
                                
                                <table class="table"style="margin-top: 1%">
                                <thead class="thead-dark" >
                                    <tr>
                                        <th scope="col">No.</th>
                                        <th scope="col">Nama File</th>
                                        <th scope="col">Dana Yang Diajukan</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
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
                                            <td class=table-primary>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">RP.</span>
                                                    </div>
                                                    <input type="text" class="form-control" id="dana_num" name={{$dana}} placeholder="maks. 15 Digit" id="currency-field">
                                                </div>
                                            </td>
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
                    </div>
                </div>
            </div>   
        </div>
    </main>
</div>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
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
<script>
    // set comma every 3 digits & receieve no alphabets
    function updateTextView(_obj){
    var num = getNumber(_obj.val());
        if(num==0){
            _obj.val('');
        }else{
            _obj.val(num.toLocaleString());
        }
    }
    // cek panjang text
    function getNumber(_str){
        var arr = _str.split('');
        var out = new Array();
        for(var cnt=0;cnt<arr.length;cnt++){
            if(isNaN(arr[cnt])==false){
            out.push(arr[cnt]);
            }
        }
        return Number(out.join(''));
    }
    // cari semua input type=text
    $(document).ready(function(){
        $('input[type=text]').on('keyup',function(){
            updateTextView($(this));
        });
    });
</script>
@endsection