@extends('../layouts.base')

@section('title', 'Picsite Dashboard')

@section('container')
<div class="row">
    @include('picsite.sidebarpic')
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h2>Welcome back, {{ Auth::user()->name }} !</h2>
            <h4>Cabang : {{ Auth::user()->cabang }}</h4>
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
                <hr class="rounded">
            </h3>
            
            <style>{
                {{-- /* Rounded border */ --}}
                hr.rounded {
                border-top: 8px solid rgb(32, 13, 13);
                border-radius: 5px;
                }
            }
            </style>

            <form method="GET" action="/picsite/dashboard/rpk-search" role="search">
                @if (Auth::user()->cabang == "Bunati" or Auth::user()->cabang == 'Batu Licin')
                    <div class="col">
                        
                    </div>
                @else
                    <div class="form-inline">
                        <div class="form-check mb-2 mr-sm-2">
                            <a class="btn btn-outline-danger" style="right: 50%" href="/dashboard">Change to Dana</a>
                        </div>
                        <div class="form-check mb-2 mr-sm-2">
                            <a class="btn btn-outline-danger" href="/dashboard">Change to Fund Realization</a>
                        </div>
                    </div>
                @endif
                <div class="row">
                    <div class="col">
                        <div class="d-flex justify-content-end">
                            {{ $docrpk->links() }}
                        </div>
                    </div>

                    <div class="col">
                        <label class="sr-only" for="search_kapal">Nama Kapal</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                            <div class="input-group-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                </svg>
                            </div>
                            </div>
                            <input type="text" style="text-transform: uppercase;" name="search_kapal" id="search_kapal" class="form-control" placeholder="Search Nama Kapal" autofocus>
                            <button type="submit" class="btn btn-info">
                                <span class="glyphicon glyphicon-search"></span> Search 
                            </button>
                        </div>
                    </div>
                    {{-- in progress --}}
                    {{-- <div class="auto-cols-auto">
                        <div class="col-sm-3 my-1" style="margin-left:-1%" >
                            <select name="search_status" id="search_status" class="form-control" >
                                <option value="" hidden>Choose Status</option>
                                <option value="">All</option>
                                <option value="on review">On Review</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                    </div> --}}
                </div>
            </form>

            <table  id="content" class="table"style="margin-top: 1%">
                <thead class="thead-dark" >
                    <tr>
                        <th class="table-info">Time Uploaded</th>
                        <th class="table-info">Nama Kapal</th>
                        <th class="table-info">Periode (Y-M-D)</th>
                        <th class="table-info">Nama File</th>
                        <th class="table-info">Jenis File</th>
                        <th class="table-info">Status</th>
                        <th class="table-info">Reason</th>
                        <th class="table-info">Action</th>
                    </tr>
                </thead>
                <tbody>
                {{-- RPK --}}
                    @forelse($docrpk as $d )
                    @for ( $r = 1 ; $r <= 7 ; $r++)
                    @php
                        $RPK = array('surat_barang', 'cargo_manifest',
                                    'voyage','bill_lading',
                                    'gerak_kapal','docking',
                                    'surat_kapal');
                        $names = array('Surat Keterangan Asal Barang', 'Cargo Manifest',
                                        'Voyage Report/ Term Sheet','Bill of Lading',
                                        'Ijin Olah Gerak Kapal','Docking',
                                        'Surat Keterangan Persiapan Kapal');
                        $time_upload ="time_upload".$r;
                        $stats ="status".$r;
                        $reason = "reason".$r;
                        $date = date('Y-m-28');
                        $scan = $RPK[$r-1];
                        $tipefile = 'RPK'
                    @endphp
                    <input type="hidden" name='status' value={{$stats}}>
                    @if(empty($d->$stats))
                        <tr>
                            {{-- agar tidak keluar hasil kosong --}}
                        </tr>
                    @elseif ($d->$stats == 'on review')
                    <tr>
                        <td class="table-info"><strong>{{ $d->$time_upload }}</strong></td>
                        <td class="table-info" style="text-transform: uppercase;" id="namakapal">{{$d->nama_kapal}}</td>                                        
                        <td class="table-info" id="periode"><strong>{{$d->periode_awal}} To {{$d->periode_akhir}}</strong></td>                                   
                        <td class="table-info" id="namafile">{{$names[$r-1]}}</td>     
                        <td class="table-secondary" id="jenisfile"><strong>RPK</strong></td>     
                        <td class="table-info" style="text-transform: uppercase;" id="status"><strong>{{$d->$stats}}</td>                                      
                        <td class="table-info" id="reason">{{$d ->$reason}}</td>       
                        <td class="table-info" >
                            <form method="post" action="/dashboard/rpk/view" target="_blank">
                                @csrf
                                <input type="hidden" name = 'cabang' value={{$d->cabang}}>
                                <input type="hidden" name='created_at_Year' value={{Carbon\Carbon::parse($d->created_at)->format('Y')}} />
                                <input type="hidden" name='created_at_month' value={{Carbon\Carbon::parse($d->created_at)->format('m')}} />
                                <input type="hidden" name = 'kapal_nama' value={{$d->nama_kapal}}>
                                <input type="hidden" name = 'tipefile' value='RPK'>
                                <input type="hidden" name='viewdocrpk' value={{$RPK[$r-1]}} />
                                <input type="hidden" name='result' value={{$d->$scan}} />
                                <button type="submit" name="views3" class="btn btn-dark">view</button>
                            </form>
                        </td>                                 
                    </tr>
                    @elseif($d->$stats == 'approved')
                        <tr>
                            <td class="table-success">{{ $d->$time_upload }}</td>
                            <td class="table-success" style="text-transform: uppercase;" id="namakapal">{{$d->nama_kapal}}</td>                                        
                            <td class="table-success" id="periode"><strong>{{$d->periode_awal}} To {{$d->periode_akhir}}</strong></td>                                   
                            <td class="table-success" id="namafile">{{$names[$r-1]}}</td>     
                            <td class="table-secondary" id="jenisfile"><strong>RPK</strong></td>     
                            <td class="table-success" style="text-transform: uppercase;" id="status"><strong>{{$d->$stats}}</td>                                      
                            <td class="table-success" id="reason">{{$d->$reason}}</td>
                            <td class="table-success" >
                                <form method="post" action="/dashboard/rpk/view" target="_blank">
                                    @csrf
                                    <input type="hidden" name = 'cabang' value={{$d->cabang}}>
                                    <input type="hidden" name='created_at_Year' value={{Carbon\Carbon::parse($d->created_at)->format('Y')}} />
                                <input type="hidden" name='created_at_month' value={{Carbon\Carbon::parse($d->created_at)->format('m')}} />
                                    <input type="hidden" name = 'kapal_nama' value={{$d->nama_kapal}}>
                                    <input type="hidden" name = 'tipefile' value='RPK'>
                                    <input type="hidden" name='viewdocrpk' value={{$RPK[$r-1]}} />
                                    <input type="hidden" name='result' value={{$d->$scan}} />
                                    <button type="submit" name="views3" class="btn btn-dark">view</button>
                                </form>
                            </td>                                        
                        </tr>
                    @else
                        <tr>
                            <td class="table-danger">{{ $d->$time_upload }}</td>
                            <td class="table-danger" style="text-transform: uppercase;" id="namakapal">{{$d->nama_kapal}}</td>                                        
                            <td class="table-danger" id="periode"><strong>{{$d->periode_awal}} To {{$d->periode_akhir}}</strong></td>                                   
                            <td class="table-danger" id="namafile">{{$names[$r-1]}}</td>   
                            <td class="table-secondary" id="jenisfile"><strong>RPK</strong></td>    
                            <td class="table-danger" style="text-transform: uppercase;" id="status"><strong>{{$d->$stats}}</td>                                      
                            <td class="table-danger" id="reason">{{$d->$reason}}</td>
                            <td class="table-danger" >
                                <form method="post" action="/dashboard/rpk/view" target="_blank">
                                    @csrf
                                    <input type="hidden" name = 'cabang' value={{$d->cabang}}>
                                    <input type="hidden" name='created_at_Year' value={{Carbon\Carbon::parse($d->created_at)->format('Y')}} />
                                <input type="hidden" name='created_at_month' value={{Carbon\Carbon::parse($d->created_at)->format('m')}} />
                                    <input type="hidden" name = 'kapal_nama' value={{$d->nama_kapal}}>
                                    <input type="hidden" name = 'tipefile' value='RPK'>
                                    <input type="hidden" name='viewdocrpk' value={{$RPK[$r-1]}} />
                                    <input type="hidden" name='result' value={{$d->$scan}} />
                                    <button type="submit" name="views3" class="btn btn-dark">view</button>
                                </form>
                            </td>    
                        </tr>
                    @endif
                    @endfor
                    <tr>
                        <td>
                            {{-- untuk pisahin RPK --}}
                        </td>
                    </tr>
                    @empty
                    {{-- <tr>
                        <td> No RPK Data Found </td>
                    </tr> --}}
                    @endforelse
                </tbody>
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