@extends('../layouts.base')

@section('title', 'PicAdmin Record RPK Documents')

@section('container')
<div class="row">
    @include('picadmin.picAdminsidebar')
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <div class="jumbotron jumbotron-fluid" >
                <h1 class="text-center" style="center">Record  RPK Documents</h1>
                <hr class="my-4">
                    
                <form method="GET" action="/picadmin/RecordDocumentsRPK/search" role="search">
                <div class="row">
                    <div class="col">
                            <div class="auto-cols-auto">
                                <div class="col" style="margin-left:-1%" >
                                    <div class="input-group">
                                    <div class="input-group-prepend">
                                    </div>
                                    <input type="text" style="text-transform: uppercase;" name="search_kapal" id="search_kapal" class="form-control" placeholder="Search Nama Kapal" autofocus>
                                    <button type="submit" class="btn btn-info">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                        </svg>
                                    </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                        <select name="search" id="cabangfilter"class="form-select" >
                        <option selected disabled hidden='true' value="">Pilih Cabang</option>
                        <option value="All">Semua Cabang</option>
                        <option value="Babelan">Babelan</option>
                        <option value="Berau">Berau</option>
                        <option value="Samarinda">Samarinda</option>
                        <option value="Banjarmasin">Banjarmasin</option>
                        <option value="Jakarta">Jakarta</option>
                        <option value="Bunati">Bunati</option>
                        <option value="Kendari">Kendari</option>
                        <option value="Morosi">Morosi</option>
                    </select>
                </div>
                <div class="col">
                    <a class="btn btn-outline-danger" style="margin-left:40%" href="/picadmin/RecordDocuments">Change to Dana</a>
                </div>
                <div class="col">
                    <div class="d-flex justify-content-end">
                        {{ $docrpk->links() }}
                    </div>
                </div>
            </div>
        </form>

            @if($errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="alert error alert-danger" id="error">{{ $error }}
                            <strong> No data found</strong>
                        </div>
                    @endforeach
                @endif

                  <script>
                    setTimeout(function(){
                    $("div.alert").remove();
                    }, 5000 ); // 5 secs
                  </script>
                 <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

                <table id="content" class="table" style="margin-top: 1%">
                  <thead class="thead-dark">
                      <tr>
                        <th>Time Uploaded</th>
                        <th>cabang</th>
                        <th>Nama Kapal</th>
                        <th>Periode (Y-M-D)</th>
                        <th>Nama File</th>
                        <th>Jenis File</th>
                        <th>status</th>
                        <th>Reason</th>
                        <th>Action</th>
                      </tr>
                  </thead>
                  <tbody>
{{-- RPK----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- --}}
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
                    @endphp
                    <input type="hidden" name='status' value={{$stats}}>
                    @if(empty($d->$stats))
                        <tr>
                            {{-- agar tidak keluar hasil kosong --}}
                        </tr>
                    @elseif($d->$stats == 'approved')
                        <tr>
                            <td class="table-success">{{ $d->$time_upload }}</td>
                            <td class="table-success"><strong>{{ $d->cabang }}</strong></td>
                            <td class="table-success" style="text-transform: uppercase;" id="namakapal">{{$d->nama_kapal}}</td>                                        
                            <td class="table-success" id="periode"><strong>{{$d->periode_awal}} To {{$d->periode_akhir}}</strong></td>                                   
                            <td class="table-success" id="namafile">{{$names[$r-1]}}</td>     
                            <td class="table-dark" id="jenisfile"><strong>RPK</strong></td>     
                            <td class="table-success" style="text-transform: uppercase;" id="status"><strong>{{$d->$stats}}</td>                                      
                            <td class="table-success" id="reason">{{$d->$reason}}</td>
                            <td class="table-success" >
                                <form method="post" action="/picadmin/RecordDocuments/RPK/view" target="_blank">
                                    @csrf
                                    <input type="hidden" name = 'cabang' value={{$d->cabang}}>
                                    <input type="hidden" name = 'tipefile' value='RPK'>
                                    <input type="hidden" name = 'kapal_nama' value={{$d->nama_kapal}}>
                                    <input type="hidden" name='viewdocrpk' value={{$RPK[$r-1]}} />
                                    <input type="hidden" name='result' value={{$d->$scan}} />
                                    <button type="submit" name="views3" class="btn btn-dark">view</button>
                                </form>
                            </td>                                        
                        </tr>
                    @elseif($d->$stats == 'rejected')
                        <tr>
                            <td class="table-danger">{{ $d->$time_upload }}</td>
                            <td class="table-danger"><strong>{{ $d->cabang }}</strong></td>
                            <td class="table-danger" style="text-transform: uppercase;" id="namakapal">{{$d->nama_kapal}}</td>                                        
                            <td class="table-danger" id="periode"><strong>{{$d->periode_awal}} To {{$d->periode_akhir}}</strong></td>                                   
                            <td class="table-danger" id="namafile">{{$names[$r-1]}}</td>   
                            <td class="table-dark" id="jenisfile"><strong>RPK</strong></td>    
                            <td class="table-danger" style="text-transform: uppercase;" id="status"><strong>{{$d->$stats}}</td>                                      
                            <td class="table-danger" id="reason">{{$d->$reason}}</td>
                            <td class="table-danger" >
                                <form method="post" action="/picadmin/RecordDocuments/RPK/view" target="_blank">
                                    @csrf
                                    <input type="hidden" name = 'cabang' value={{$d->cabang}}>
                                    <input type="hidden" name = 'tipefile' value='RPK'>
                                    <input type="hidden" name = 'kapal_nama' value={{$d->nama_kapal}}>
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
                                {{-- pisah beda nama kapal --}}
                            </td>
                        </tr>
                    @empty
                    
                    @endforelse
                  </tbody>
                </table>
            </div>
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