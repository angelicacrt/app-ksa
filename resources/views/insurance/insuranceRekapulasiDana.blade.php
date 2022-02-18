@extends('../layouts.base')

@section('title', 'insiden-insurance-SPGR-Notes')

@section('container')
<x-guest-layout>
<div class="row">
    @include('insurance.insuranceSidebar')
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="col" style="margin-top: 15px">
            <div class="jumbotron jumbotron-fluid" >
                <div class="container">
                    <h1 class="display-4"><strong>Rekapitulasi Dana</strong></h1>

                    <div class="col">
                        <div class="text-md-center">
                            <button class="btn btn-outline-success"  id="top" style=" width: 100%;" data-toggle="modal" data-target="#Download">Download</button>
                        </div>
                    </div>
    
                    {{-- Modal download --}}
                        <div class="modal fade" id="Download" tabindex="-1" role="dialog" aria-labelledby="Download" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="Download">Download Options</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <form method="POST" action="/insurance/exportPDF">
                                            @csrf
                                            <label for="downloadPDF">Download As PDF :</label>
                                            <button  name='downloadPDF' id="downloadPDF" class="btn btn-outline-dark">Download PDF</button>
                                        </form>
                                        
                                        <br>
                                        <br>
                                        
                                        <form method="POST" action="/insurance/exportExcel">
                                            @csrf
                                            <label for="downloadExcel">Download As Excel :</label>
                                            <button  name='downloadExcel' id="downloadExcel" class="btn btn-outline-dark">Download Excel</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    <table id="content" class="table" style="margin-top: 1%">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" style="text-align: center">No.</th>
                                <th scope="col" style="text-align: center">Periode</th>
                                <th scope="col" style="text-align: center">Cabang</th>
                                <th scope="col" style="text-align: center">Nama TugBoat/Barge</th>
                                <th scope="col" style="text-align: center">Nama File</th>
                                <th scope="col" style="text-align: center">Nilai Jumlah Di Ajukan</th>
                                {{-- <th scope="col">Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rekapdana as $rekap )
                            @php
                                $date = date('Y-m-28');
                            @endphp
                            <tr>
                                <td class="table-info" style="text-align: center">{{$loop->index+1}}</td>
                                <td class="table-info" style="text-align: center">{{$rekap->Nama_File}}</td>
                                <td class="table-info" style="text-align: center" style="text-transform: uppercase;"><strong>{{$rekap->Cabang}}</td>
                                <td class="table-info" style="text-align: center">{{Str::limit($rekap->NamaTug_Barge, 20)}}</td>
                                <td class="table-info" style="text-align: center" style="text-transform: uppercase;">{{$rekap->DateNote1}} - {{$rekap->DateNote2}}</td>
                                <td class="table-info" style="text-align: center">{{$rekap->Nilai}}</td>
                            </tr>
                            @empty
                            <tr>
                                <td>No Notes Uploaded Yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                        <script>
                            setTimeout(function(){
                            $("div.alert").remove();
                            }, 5000 ); // 5 secs
                        </script>
                        <script 
                            src="https://code.jquery.com/jquery-3.2.1.min.js">
                        </script>
                        @if ($success = Session::get('success'))
                        <div class="alert alert-success alert-block" id="success">
                            <strong>{{ $success }}</strong>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>   
    </main>
</div>
</x-guest-layout>
<script type="text/javascript">
    function refreshDiv(){
        $('#content').load(location.href + ' #content')
    }
    setInterval(refreshDiv, 60000);
</script>
@endsection