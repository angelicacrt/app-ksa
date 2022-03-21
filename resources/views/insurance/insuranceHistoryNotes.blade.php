@extends('../layouts.base')

@section('title', 'Insurance-Check-Spgr-Notes')

@section('container')
<x-guest-layout>
<div class="row">
    @include('insurance.insuranceSidebar')
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="col" style="margin-top: 15px">
            <div class="jumbotron jumbotron-fluid" >
                <div class="container">
                  <h1 class="display-4">History Notes SPGR</h1>
                  
                    <table id="content" class="table" style="margin-top: 1%">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">No.</th>
                                <th scope="col">Tahun/Bulan/Tanggal</th>
                                <th scope="col">No.SPGR</th>
                                <th scope="col">No Form Claim</th>
                                <th scope="col">Nama Kapal</th>
                                <th scope="col">status pembayaran</th>
                                <th scope="col">Nilai</th>
                                <th scope="col">Nilai Claim yang di setujui</th>
                                {{-- <th scope="col">Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($UploadNotes as $UpNotes )
                            <tr>
                                <td class="table-info">{{$loop->index+1}}</td>
                                <td class="table-info">{{$UpNotes->DateNote}}</td>
                                <td class="table-info">{{$UpNotes->No_SPGR}}</td>
                                <td class="table-info">{{$UpNotes->No_FormClaim}}</td>
                                <td class="table-info">{{$UpNotes->Nama_Kapal}}</td>
                                <td class="table-info">{{$UpNotes->status_pembayaran}}</td>
                                <td class="table-info" style="text-align: center">{{$UpNotes->Nilai}}</td>
                                <td class="table-info" style="text-align: center">{{$UpNotes->Nilai_Claim}}</td>
                            </tr>
                            @empty
                            <tr>
                                <td>No Notes Uploaded Yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
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