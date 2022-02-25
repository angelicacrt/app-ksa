@extends('../layouts.base')

@section('title', 'insiden-insurance-SPGR-Notes')

@section('container')
<x-guest-layout>
<div class="row">
    @include('picincident.sidebarincident')
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="col" style="margin-top: 15px">
            <div class="jumbotron jumbotron-fluid" >
                <div class="container">
                  <h1 class="display-4"><strong>Note SPGR Form</strong></h1>
                    @if ($success = Session::get('success'))
                        <div class="alert alert-success alert-block" id="success">
                            <strong>{{ $success }}</strong>
                        </div>
                    @endif
                    <div class="row">
                        @forelse($UploadNotes as $UpNotes )
                        @if ($loop->index == 0)
                        {{-- kosong --}}
                        @elseif ($loop->index == 1)
                            <div class="col">
                                <div class="text-md-left">
                                <form action="/picincident/NoteSpgr/destroyall" method="POST">
                                    @csrf
                                    @method('delete')
                                        <button type="submit" onClick="return confirm('Are you sure to delete all the notes?')" class="btn btn-outline-danger">
                                            Delete All Note
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="col">
                                <div class="text-md-center">
                                    <button class="btn btn-outline-primary"  id="top" style=" width: 70%;" data-toggle="modal" data-target="#downloadspgrnote">
                                        Download
                                    </button>
                                </div>
                                </div>
                            @elseif ($loop->index > 1)

                            @endif
                            @empty
                            {{-- kosong / hilangkan tombol --}}
                            @endforelse
                        <div class="col">
                            <div class="text-md-right">
                                <button class="btn btn-outline-info"  id="top" style=" width: 40%;" data-toggle="modal" data-target="#Addspgrnote">+ Add List</button>
                            </div>
                        </div>
                    </div>

                    
                    {{-- Modal download --}}
                        <div class="modal fade" id="downloadspgrnote" tabindex="-1" role="dialog" aria-labelledby="downloadspgrnote" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="downloadspgrnote">Download Notes</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <form method="POST" action="/picincident/exportExcel">
                                            @csrf
                                            <label for="downloadExcel">Download As Excel :</label>
                                            <button  name='downloadExcel' id="downloadExcel" class="btn btn-outline-dark">Download As Excel</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    {{-- Modal add note --}}
                        <div class="modal fade" id="Addspgrnote" tabindex="-1" role="dialog" aria-labelledby="Addspgrnote" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="Addspgrnote">Add Note ?</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="/picincident/addNoteSpgr">
                                        @csrf
                                        <div class="form-group">
                                            <label for="Datebox">Date</label>
                                            <input type="date" class="form-control" name="Datebox" required id="Datebox" >

                                            <br>
                                            <label for="No_SPGR">No.SPGR</label>
                                            <input type="text" class="form-control" name="No_SPGR" required id="No_SPGR" >

                                            <br>
                                            <label for="No_FormClaim">No Form Claim</label>
                                            <input type="text" class="form-control" name="No_FormClaim" required id="No_FormClaim" >

                                            <br>
                                            <label for="NamaKapal">Nama Kapal</label>
                                            <input type="text" class="form-control" name="NamaKapal" required id="NamaKapal" >
                                            
                                            <br>
                                            <label for="status_pembayaran">status pembayaran</label>
                                            <select name="status_pembayaran" id="status_pembayaran" class="block mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" required autofocus>
                                                <option selected disabled value="">Please Choose...</option>
                                                <option value="Paid" >Paid</option>
                                                <option value="unpaid" >unpaid</option>
                                                <option value="partial" >partial</option>
                                            </select>

                                            <br>
                                            <label for="Nilai">Nilai</label>
                                            <div class="input-group mb-1">
                                                <select class="btn btn-outline-secondary" name="mata_uang_nilai">
                                                    <option selected value="USD" id="">USD</option>
                                                    <option value="IDR" id="">IDR</option>
                                                </select>
                                                <input type="number" class="form-control" name="Nilai" required id="Nilai" >
                                            </div>

                                            <br>
                                            <label for="NilaiClaim">Nilai Claim yang di setujui</label>
                                            <div class="input-group mb-1">
                                                <select class="btn btn-outline-secondary" name="mata_uang_claim">
                                                    <option selected value="USD" id="">USD</option>
                                                    <option value="IDR" id="">IDR</option>
                                                </select>
                                                <input type="number" class="form-control" name="NilaiClaim" required id="Nilai_Claim" >
                                            </div>
                                        </div>
                                </div>
                                    <div class="modal-footer">
                                        <button type="submit" id="submitreject2" class="btn btn-outline-dark">Add Note</button>
                                    </form>
                                    </div>
                            </div>
                            </div>
                        </div>
                    {{-- table data --}}
                        <table class="table" style="margin-top: 1%">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col" style="text-align: center">No.</th>
                                    <th scope="col" style="text-align: center">Tahun/Bulan/Tanggal</th>
                                    <th scope="col" style="text-align: center">No SPGR</th>
                                    <th scope="col" style="text-align: center">No Form Claim</th>
                                    <th scope="col" style="text-align: center">Nama Kapal</th>
                                    <th scope="col" style="text-align: center">Status pembayaran</th>
                                    <th scope="col" style="text-align: center">Nilai</th>
                                    <th scope="col" style="text-align: center">Nilai Claim yang di setujui</th>
                                    <th scope="col" style="text-align: center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($UploadNotes as $UpNotes )
                                @php
                                    $date = date('Y-m-28');
                                @endphp
                                <tr>
                                    <td class="table-info" style="text-align: center">{{$loop->index+1}}</td>
                                    <td class="table-info" style="text-align: center">{{$UpNotes->DateNote}}</td>
                                    <td class="table-info" style="text-align: center">{{$UpNotes->No_SPGR}}</td>
                                    <td class="table-info" style="text-align: center">{{$UpNotes->No_FormClaim}}</td>
                                    <td class="table-info" style="text-align: center">{{$UpNotes->Nama_Kapal}}</td>
                                    <td class="table-info" style="text-align: center">{{$UpNotes->status_pembayaran}}</td>
                                    <td class="table-info" style="text-align: center">{{$UpNotes->Nilai}}</td>
                                    <td class="table-info" style="text-align: center">{{$UpNotes->Nilai_Claim}}</td>
                                    <td scope="col">
                                        <div class="row">
                                            <div class="col-md-auto">
                                                <form action="/picincident/NoteSpgr/destroy/{{$UpNotes->id}}" method="POST">
                                                    @csrf
                                                    @method('delete')
                                                    <a class="btn btn-outline-primary" href="/picincident/EditNoteSpgr/{{$UpNotes->id}}">Edit</a>
                                                    <button type="submit" id="realsub" onClick="return confirm('Are you sure?')" class="btn btn-outline-dark">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
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
                </div>
            </div>
        </div>   
    </main>
</div>
</x-guest-layout>
<style>
    .modal-backdrop {
          height: 100%;
          width: 100%;
      }
</style>
@endsection