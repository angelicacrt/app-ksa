@extends('../layouts.base')

@section('title', 'Staff Legal Upload RPK')

@section('container')
<div class="row">
    @include('StaffLegal.StaffLegalSidebar')
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="row">
            <div class="col" style="margin-top: 15px">
                <div class="jumbotron jumbotron-fluid" >
                    <div class="container">
                      <h1 class="display-4">Upload your RPK Documents</h1>
                        <p class="lead">please only upload file size max 1MB with .PDF format only .
                          <br>
                            Please upload your RPK Document !
                        </p>
                        <button class="btn btn-danger"  id="top" style="margin-left: 80%; width: 20%;">Submit</button>

                        @if($errors->any())
                            @foreach ($errors->all() as $error)
                                <div class="alert error alert-danger" id="error">{{ $error }}
                                    <strong>    Please check the file is a PDF and Size 1MB. </strong>
                                </div>
                            @endforeach
                        @endif
                        @if ($success = Session::get('success'))
                            <div class="alert alert-success alert-block" id="message">
                                <strong>{{ $success }}</strong>
                            </div>
                        @endif

                        <form action="/Staff_Legal/uploadrpk" method="POST" enctype="multipart/form-data" name="formUploadrpk" id="formUploadrpk">
                            @csrf
                            <div class="form-row">
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
                                    <input type="date" name="tgl_awal" class="form-control" requiredplaceholder="Periode Awal">
                                </div>
                                <div class="col-md-3">
                                    <label>to</label>
                                    <input type="date" name="tgl_akhir" class="form-control" required placeholder="Periode Akhir">
                                </div>
                            </div>
                       
                        <table class="table" style="margin-top: 1%">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">No.</th>
                                    <th scope="col">Nama File</th>
                                    {{-- <th scope="col">Upload Time</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Reason</th>
                                    <th scope="col">Due Date</th> --}}
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                    {{-- Jakarta --}}
                                @if (Auth::user()->cabang == 'Jakarta')
                                @for ($a = 1 ; $a <= 7 ; $a++)
                                @php
                                    $name = array('Surat Keterangan Asal Barang','Cargo Manifest','Voyage Report/ Term Sheet'
                                                    ,'Bill of Lading','Ijin Olah Gerak Kapal',
                                                    'Docking','Surat Keterangan Persiapan Kapal');
                                    $jktfile = 'jktfile'.$a;
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
                                            <input type="file" class="form-control" name="{{$jktfile}}" id="rfile">
                                          </div>
                                    </td>
                                     
                                </tr>
                                @endfor
                                @endif
                            </tbody>
                        </table>
                        {{-- @if(date("d") < 28) --}}
                            <button class="btn btn-danger" id="realsub" style="margin-left: 50%; display: none;" type="submit" name="Submit" value="Upload" onClick="">Submit</button>
                         {{-- @endif --}}
                        <script>
                            document.getElementById('top').addEventListener('click', openDialog);
                            function openDialog() {
                                document.getElementById('realsub').click();
                            }
                        </script>
                        <script>
                            setTimeout(function(){
                            $("div.alert").remove();
                            }, 5000 ); // 5 secs
                        </script>
                        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
                        </form>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </main>
</div>
@endsection