@if(Auth::user()->hasRole('purchasing'))
    @extends('../layouts.base')

    @section('title', 'Purchasing Reports')

    @section('container')
    <div class="row">
        @include('purchasing.sidebar')
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            
            <div class="flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 mt-3">
                <h1 class="d-flex justify-content-center mb-4">Reports JO Cabang {{ $default_branch }} ({{ $str_month }})</h1>
                
                <div class="d-flex justify-content-between mr-3">
                    <div class="p-2">
                        <select name="cabang" class="form-select" onchange="window.location = this.value;">
                            <option selected disabled>Pilih Cabang</option>
                            <option value="/purchasing/report/Jakarta" 
                                @php
                                    if($default_branch == 'Jakarta'){
                                        echo('selected');
                                    }
                                @endphp
                            >Jakarta</option>
                            <option value="/purchasing/report-JO/Banjarmasin"
                                @php
                                    if($default_branch == 'Banjarmasin'){
                                        echo('selected');
                                    }
                                @endphp
                            >Banjarmasin</option>
                            <option value="/purchasing/report-JO/Samarinda"
                                @php
                                    if($default_branch == 'Samarinda'){
                                        echo('selected');
                                    }
                                @endphp
                            >Samarinda</option>
                            <option value="/purchasing/report-JO/Bunati"
                                @php
                                    if($default_branch == 'Bunati'){
                                        echo('selected');
                                    }
                                @endphp
                            >Bunati</option>
                            <option value="/purchasing/report-JO/Babelan"
                                @php
                                    if($default_branch == 'Babelan'){
                                        echo('selected');
                                    }
                                @endphp
                            >Babelan</option>
                            <option value="/purchasing/report-JO/Berau"
                                @php
                                    if($default_branch == 'Berau'){
                                        echo('selected');
                                    }
                                @endphp
                            >Berau</option>
                        </select>
                    </div>
                        @if(count($jobs) > 0)
                            <div class="p-2">
                                <a href="/purchasing/report_JO/download/{{ $default_branch }}" class="btn btn-outline-danger mb-3" target="_blank">Export</a>
                            </div>
                        @endif
                </div>

                <div class="table-wrapper-scroll-y my-custom-scrollbar tableFixHead" style="overflow-x:auto;">
                    <table class="table table-bordered sortable">
                        <thead class="thead bg-danger">
                        <tr>
                            <th scope="col">Nomor</th>
                            <th scope="col">Tanggal PR</th>
                            <th scope="col">Nomor PR</th>
                            <th scope="col">Supplier</th>
                            <th scope="col">Tanggal JO</th>
                            <th scope="col">Nomor JO</th>
                            <th scope="col">Nama Kapal</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Harga</th>
                            <th scope="col">Keterangan</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($jobs as $key=>$o)
                                <tr>
                                    <td>{{ $key + 1  }}</td>
                                    <td>{{ $o -> jrDate }}</td>
                                    <td>{{ $o -> noJr }}</td>
                                    @if(isset($o -> supplier))
                                        <td>{{ $o -> supplier }}</td>
                                    @else
                                        <td></td>
                                    @endif
                                    <td>{{ $o -> JODate}}</td>
                                    <td>{{ $o -> JO_id}}</td>
                                    <td>{{ $o -> tugName}}/{{$o -> bargeName}}</td>
                                    <td>{{ $o -> quantity}}</td>
                                    <td>Rp. {{ number_format($o -> totalHargaJob, 2, ",", ".") }}</td>
                                    <td>{{ $o -> note}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
        </main>
    </div>

    <style>
        .tableFixHead          { overflow: auto; height: 250px; }
        .tableFixHead thead th { position: sticky; top: 0; z-index: 1; }

        .my-custom-scrollbar {
            position: relative;
            height: 800px;
            overflow: auto;
        }
        .table-wrapper-scroll-y {
            display: block;
        }

        th{
            color: white;
        }

        td, th{
            word-wrap: break-word;
            min-width: 160px;
            max-width: 160px;
            text-align: center;
        }
    </style>
    <script src="https://www.kryogenix.org/code/browser/sorttable/sorttable.js"></script>
    @endsection
@else
    @include('../layouts/notAuthorized')
@endif