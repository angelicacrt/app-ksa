@extends('../layouts.base')

@section('title', 'insiden-insurance-spgr')

@section('container')

<div class="form-row">
    @include('picincident.sidebarincident')
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="form-row">
        <div class="col" style="margin-top: 1%">
            <div class="jumbotron jumbotron-fluid" >
                <div class="container">
                  <h4 class="display-4" style="margin-top: -6%; "><strong>Upload SPGR Request document</strong></h4>
                    <p class="lead" style="font-size:16px; margin-top: 2%;">please upload your SPGR Request form size of 3MB and only upload file .PDF format only .</p>

                    <br>

                    @if($errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="alert error alert-danger" id="error">{{ $error }}
                            <strong>Please check the file is a PDF and Size 3MB. </strong>
                        </div>
                    @endforeach
                    @endif
                    @if ($success = Session::get('success'))
                        <div class="alert alert-success alert-block" id="success">
                            <strong>{{ $success }}</strong>
                        </div>
                    @endif

                    <div class="row d-flex justify-content-center mt-100">
                        <div class="col-md-20">
                            <div class="card Light card">
                                <div class="card-header">
                                    <h4>File Upload</h4>
                                </div>

                                <div class="card-body">
                                    <div class="text-lg-center" style="margin-bottom: 2%">
                                        <button id="top" type="button" class="btn btn-outline-danger">Upload Files Now</button>
                                    </div>
                                    
                                    <form method="POST" action="/picincident/uploadSPGR" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-floating" style="margin-bottom: 2%">
                                            <input type="text" id ="no_formclaim" name="no_formclaim" {{-- style="text-transform: uppercase;" --}}class="form-control" required placeholder="Insert Nomor FormClaim"> 
                                            <label for="no_formclaim">Insert No FormClaim Here</label>
                                        </div>
                                               
                                        <table class="table">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th scope="col">No.</th>
                                                    <th scope="col">Nama File</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @for ($a = 1 ; $a <= 7 ; $a++)
                                                @php
                                                    $viewspgr = array('spgr','Letter_of_Discharge','CMC','surat_laut',
                                                                        'spb','load_line','surat_keterangan_bank');
                                                    $name = array('SPGR','LETTER OF DISCHARGE','CMC','SURAT LAUT',
                                                                    'SPB','LOAD LINE','SURAT KETERANGAN BANK');
                                                    $spgrfile = 'spgrfile'.$a;
                                                    $time_upload ="time_upload".$a;
                                                    $stats ="status".$a;
                                                    $reason ="reason".$a;
                                                @endphp
                                                <tr>
                                                    <td class="table-info">{{ $a }}</td>
                                                    <td class="table-info" id="nama"><strong>{{$name[$a-1]}}</td>
                                                    <td><input name="{{$spgrfile}}" class=form-control type="file"/></td>
                                                    <button class="btn btn-danger" id="realsub" style="margin-left: 50%; display: none;" type="submit" name="Submit" value="Upload" onClick="">Submit</button>
                                                </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 

        <script>
            document.getElementById('top').addEventListener('click', openDialog);
            function openDialog() {
                document.getElementById('realsub').click();
                console.log("test");
            }
        </script>
        <script>
            setTimeout(function(){
            $("div.alert").remove();
            }, 3000 ); // 3 secs
        </script>
        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

    </main>
</div>
@endsection