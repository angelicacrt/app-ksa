@extends('../layouts.base')

@section('title', 'Insurance-Check-Spgr')

@section('container')
<x-guest-layout>
<div class="row">
    @include('insurance.insuranceSidebar')
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="col" style="margin-top: 15px">
            <div class="jumbotron jumbotron-fluid" >
                <div class="container">
                  <h1 class="display-4">Review Form Request SPGR</h1>
                  
                  {{-- searchbar --}}
                    <form method="GET" action="/insurance/CheckSpgr/searchspgr" role="search">
                        <div class="auto-cols-auto">
                            <div class="col-sm-3 my-1" style="margin-left:-1%" >
                                <div class="input-group">
                                <div class="input-group-prepend">
                                </div>
                                <input type="text" name="search_no_formclaim" id="search_no_formclaim" class="form-control" placeholder="Search No.FormClaim" autofocus>
                                <button type="submit" class="btn btn-info">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                    </svg>
                                </button>
                                </div>
                            </div>
                        </div>
                    </form>

                {{-- table data --}}
                    <table class="table" style="margin-top: 1%">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">No.</th>
                                <th scope="col">No. FormClaim</th>
                                <th scope="col">Nama File</th>

                                <th scope="col">Status</th>
                                <th scope="col">Reason</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        @error('reasonbox')
                        <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                            Alasan Wajib Diisi
                        </div>
                        @enderror
                        <tbody>
                            @forelse($uploadspgr as $upspgr )
                            @for ( $r = 1 ; $r <= 7 ; $r++)
                            @php
                                $viewspgrfile = array('spgr','Letter_of_Discharge','CMC','surat_laut',
                                                    'spb','load_line','surat_keterangan_bank');
                                $name = array('SPGR','LETTER OF DISCHARGE','CMC','SURAT LAUT',
                                        'SPB','LOAD LINE','SURAT KETERANGAN BANK');
                                $spgrfile = 'spgrfile'.$r;
                                $time_upload ="time_upload".$r;
                                $stats ="status".$r;
                                $reason ="reason".$r;
                                $scan=$viewspgrfile[$r-1];
                            @endphp
                            @if(empty($upspgr->$stats))
                                <tr>
                                   <td> </td> {{-- agar tidak keluar hasil kosong --}}
                                </tr>
                            @elseif ($upspgr->$stats == "on review")
                            <tr>
                                <td class="table-warning" id="time">{{$upspgr->$time_upload}}</td>                                        
                                <td class="table-warning">{{$upspgr->no_formclaim}}</td>
                                <td class="table-warning" id="nama">{{$name[$r-1]}}</td>                                        
                                <td class="table-warning" id="status">{{$upspgr->$stats}}</td>                                      
                                <td class="table-warning" id="reason">{{$upspgr->$reason}}</td>                                    
                                <td scope="col">
                                        <div class="form-row">
                                            <div class="col-md-auto">
                                                <form method="POST" action="/insurance/approvespgr">
                                                    @csrf
                                                    <input type="hidden" name='result' value={{$upspgr->$scan}} />
                                                    <input type="hidden" name = 'no_claim' value={{$upspgr->no_formclaim}}>
                                                    <input type="hidden" name='viewspgrfile' value={{$viewspgrfile[$r-1]}} />
                                                    <input type="hidden" name = 'cabang' value={{$upspgr->cabang}}>
                                                    <input type="hidden" name = 'tipefile' value='SPGR'>
                                                    <input type="hidden" name='status' value={{$stats}}>
                                                    <button type="submit" class="btn btn-outline-success">Approve File</button>
                                                </form>
                                            </div>
                                            <div class="col-md-auto">
                                                <button type="button" class="btn btn-outline-danger"  data-toggle="modal" data-target="#rejectTitle-{{$reason}}">
                                                    Reject File
                                                </button>
                                            </div>
                                            <div class="col-md-auto">
                                                <form method="post" action="/insurance/viewspgr" target="_blank">
                                                    @csrf
                                                    <input type="hidden" name = 'tipefile' value='SPGR'>
                                                    <input type="hidden" name='result' value={{$upspgr->$scan}} />
                                                    <input type="hidden" name = 'cabang' value={{$upspgr->cabang}}>
                                                    <input type="hidden" name = 'no_claim' value={{$upspgr->no_formclaim}}>
                                                    <input type="hidden" name='viewspgrfile' value={{$viewspgrfile[$r-1]}} />
                                                    <button type="submit" name="views3" class="btn btn-dark">view</button>
                                                </form>
                                            </div>
                                        </div>
        
                                {{-- Modal  --}}
                                    <div class="modal fade" id="rejectTitle-{{$reason}}" tabindex="-1" role="dialog" aria-labelledby="rejectTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle"><strong>Reject Document ?</strong></h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="/insurance/rejectspgr">
                                                        @csrf
                                                        <input type="hidden" name = 'tipefile' value='SPGR'>
                                                        <input type="hidden" name='status' value={{$stats}}>
                                                        <input type="hidden" name='reason' value={{$reason}}>
                                                        <input type="hidden" name='result' value={{$upspgr->$scan}} />
                                                        <input type="hidden" name = 'cabang' value={{$upspgr->cabang}}>
                                                        <input type="hidden" name = 'no_claim' value={{$upspgr->no_formclaim}}>
                                                        <input type="hidden" name='viewspgrfile' value={{$viewspgrfile[$r-1]}} />
                                                        <div class="form-group">
                                                            <label for="reason"><strong>Reason</strong></label>
                                                            <textarea class="form-control" name="reasonbox" required id="reason" rows="3"></textarea>
                                                        </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" id="submitreject2" class="btn btn-danger">Reject File</button>
                                                </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                @elseif ($upspgr->$stats == 'approved')
                                    <tr>
                                        <td class="table-success" id="time">{{$upspgr->$time_upload}}</td>                                        
                                        <td class="table-success">{{$upspgr->no_formclaim}}</td>
                                        <td class="table-success" id="nama">{{$name[$r-1]}}</td>                                        
                                        <td class="table-success" id="status">{{$upspgr->$stats}}</td>                                      
                                        <td class="table-success" id="reason">{{$upspgr->$reason}}</td>
                                        <td>
                                            <div class="col-md-auto">
                                                <form method="post" action="/insurance/viewspgr" target="_blank">
                                                    @csrf
                                                    <input type="hidden" name='result' value={{$upspgr->$scan}} />
                                                    <input type="hidden" name = 'tipefile' value='SPGR'>
                                                    <input type="hidden" name = 'no_claim' value={{$upspgr->no_formclaim}}>
                                                    <input type="hidden" name = 'cabang' value={{$upspgr->cabang}}>
                                                    <input type="hidden" name='viewspgrfile' value={{$viewspgrfile[$r-1]}} />
                                                    <button type="submit" name="views3" class="btn btn-dark">view</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @elseif ($upspgr->$stats == 'rejected')
                                    <tr>
                                        <td class="table-danger" id="time">{{$upspgr->$time_upload}}</td>                                        
                                        <td class="table-danger">{{$upspgr->no_formclaim}}</td>
                                        <td class="table-danger" id="nama">{{$name[$r-1]}}</td>                                        
                                        <td class="table-danger" id="status">{{$upspgr->$stats}}</td>                                      
                                        <td class="table-danger" id="reason">{{$upspgr->$reason}}</td>
                                        <td>
                                            <div class="col-md-auto">
                                                <form method="post" action="/insurance/viewspgr" target="_blank">
                                                    @csrf
                                                    <input type="hidden" name='result' value={{$upspgr->$scan}} />
                                                    <input type="hidden" name = 'tipefile' value='SPGR'>
                                                    <input type="hidden" name = 'no_claim' value={{$upspgr->no_formclaim}}>
                                                    <input type="hidden" name = 'cabang' value={{$upspgr->cabang}}>
                                                    <input type="hidden" name='viewspgrfile' value={{$viewspgrfile[$r-1]}} />
                                                    <button type="submit" name="views3" class="btn btn-dark">view</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                @endfor
                                @empty
                                <tr>
                                    <td>Data not found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
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
<style>
    .modal-backdrop {
          height: 100%;
          width: 100%;
      }
</style>
@endsection