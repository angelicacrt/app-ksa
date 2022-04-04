@extends('../layouts.base')

@section('title', 'Incident Insurance Dashboard')

@section('container')

<div class="row">
    @include('insurance.insuranceSidebar')
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h2>Welcome back, {{ Auth::user()->name }} ! - Insurance Manager</h2>
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
            <h1 class="h1-responsive ; text-center">History</h1>
            {{-- searchbar --}}
                <form method="GET" action="/dashboard/searchspgr" role="search">
                    <div class="auto-cols-auto">
                        <div class="col-sm-3 my-1" style="margin-left:-1%" >
                            <div class="input-group">
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
            <table id="content" class="table" style="margin-top: 1%">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Nama File</th>
                        <th scope="col">Upload Time</th>
                        <th scope="col">Status</th>
                        <th scope="col">Reason</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>

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
                             {{-- agar tidak keluar hasil kosong --}}
                        </tr>
                    @elseif ($upspgr->$stats == 'approved')
                        <tr>
                            <td class="table-success" id="time">{{$upspgr->$time_upload}}</td>                                        
                            <td class="table-success">{{$upspgr->no_formclaim}}</td>
                            <td class="table-success" id="nama">{{$name[$r-1]}}</td>                                        
                            <td class="table-success" style="text-transform: uppercase;" id="status"><strong>{{$upspgr->$stats}}</td>                                      
                            <td class="table-success" id="reason">{{$upspgr->$reason}}</td>
                            <td>
                                <div class="col-md-auto">
                                    <form method="post" action="/dashboard/spgr/view" target="_blank">
                                        @csrf
                                        <input type="hidden" name='result' value={{$upspgr->$scan}} />
                                        <input type="hidden" name = 'tipefile' value='SPGR'>
                                        <input type="hidden" name='created_at_Year' value={{$upspgr->created_at->format('Y')}} />
                                        <input type="hidden" name='created_at_month' value={{$upspgr->created_at->format('m')}} />
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
                            <td class="table-danger" style="text-transform: uppercase;" id="status"><strong>{{$upspgr->$stats}}</td>                                      
                            <td class="table-danger" id="reason">{{$upspgr->$reason}}</td>
                            <td>
                                <div class="col-md-auto">
                                    <form method="post" action="/dashboard/spgr/view" target="_blank">
                                        @csrf
                                        <input type="hidden" name='result' value={{$upspgr->$scan}} />
                                        <input type="hidden" name = 'tipefile' value='SPGR'>
                                        <input type="hidden" name='created_at_month' value={{$upspgr->created_at->format('m')}} />
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
                    <tr>
                        <td> </td>
                    </tr>
                    @empty
                    <tr>
                        <td>Data Not Found or Not Uploaded This Month</td>
                    </tr>
                    @endforelse
                </tbody>
                {{ $uploadspgr->links() }}
            </table>
        </div>
    </main>
</div>
<script type="text/javascript">
    function refreshDiv(){
        $('#content').load(location.href + ' #content')
    }
    setInterval(refreshDiv, 60000);
</script>
@endsection 