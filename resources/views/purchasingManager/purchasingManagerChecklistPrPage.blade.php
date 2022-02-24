@if(Auth::user()->hasRole('purchasingManager'))
    @extends('../layouts.base')

    @section('title', 'Report AP')

    @section('container')
    <div class="row">
        @include('purchasingManager.sidebar')
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            
            <div class="flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 mt-3">
                <h1 class="d-flex justify-content-center mb-4">Checklist PR Cabang {{ $default_branch }} ({{ $str_month }})</h1>
                
                <div class="d-flex justify-content-start ml-3">
                    <div class="p-2">
                        <select name="cabang" class="form-select" onchange="window.location = this.value;">
                            <option selected disabled>Pilih Cabang</option>
                            <option value="/purchasing-manager/checklist-pr/Jakarta" 
                                @php
                                    if($default_branch == 'Jakarta'){
                                        echo('selected');
                                    }
                                @endphp
                            >Jakarta</option>
                            <option value="/purchasing-manager/checklist-pr/Banjarmasin"
                                @php
                                    if($default_branch == 'Banjarmasin'){
                                        echo('selected');
                                    }
                                @endphp
                            >Banjarmasin</option>
                            <option value="/purchasing-manager/checklist-pr/Samarinda"
                                @php
                                    if($default_branch == 'Samarinda'){
                                        echo('selected');
                                    }
                                @endphp
                            >Samarinda</option>
                            <option value="/purchasing-manager/checklist-pr/Bunati"
                                @php
                                    if($default_branch == 'Bunati'){
                                        echo('selected');
                                    }
                                @endphp
                            >Bunati</option>
                            <option value="/purchasing-manager/checklist-pr/Babelan"
                                @php
                                    if($default_branch == 'Babelan'){
                                        echo('selected');
                                    }
                                @endphp
                            >Babelan</option>
                            <option value="/purchasing-manager/checklist-pr/Berau"
                                @php
                                    if($default_branch == 'Berau'){
                                        echo('selected');
                                    }
                                @endphp
                            >Berau</option>
                            <option value="/purchasing-manager/checklist-pr/Kendari"
                                @php
                                    if($default_branch == 'Kendari'){
                                        echo('selected');
                                    }
                                @endphp
                            >Kendari</option>
                            <option value="/purchasing-manager/checklist-pr/Morosi"
                                @php
                                    if($default_branch == 'Morosi'){
                                        echo('selected');
                                    }
                                @endphp
                            >Morosi</option>
                        </select>
                    </div>
                </div>

                <div id="content">
                    <div class="table-wrapper-scroll-y my-custom-scrollbar tableFixHead overflow-auto">
                        <table id="myTable" class="table table-bordered">
                            <thead class="thead bg-danger">
                                <tr>
                                    <th scope="col">Tanggal PR</th>
                                    <th scope="col">No. PR</th>
                                    <th scope="col">Pembuat PR</th>
                                    <th scope="col">Yang Menyetujui</th>
                                    <th scope="col">Supplier</th>
                                    <th scope="col">No. SBK</th>
                                    <th scope="col">Nama Kapal</th>
                                    <th scope="col">Nama Barang</th>
                                    <th scope="col">Serial/Part Number</th>
                                    <th scope="col">Qty</th>
                                    <th scope="col">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderDetails as $od)
                                    <tr>
                                        <td>{{ $od -> prDate }}</td>
                                        <td>{{ $od -> noPr }}</td>
                                        <td>{{ $od -> name }}</td>
                                        <td>{{ $od -> approvedBy }}</td>
                                        <td>{{ $od -> supplier }}</td>
                                        <td>{{ $od -> noSbk }}</td>
                                        <td>{{ $od -> boatName }}</td>
                                        <td>{{ $od -> item -> itemName }}</td>
                                        <td>{{ $od -> serialNo }}</td>
                                        <td>{{ $od -> acceptedQuantity }} {{ $od -> item -> unit }}</td>
                                        <td>{{ $od -> note }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
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
            min-width: 120px;
            max-width: 120px;
            text-align: center;
        }
    </style>
    
    <script type="text/javascript">
        function refreshDiv(){
            $('#content').load(location.href + ' #content')
        }
        setInterval(refreshDiv, 60000);

        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 3000);
    </script>
    <script src="https://www.kryogenix.org/code/browser/sorttable/sorttable.js"></script>

    @endsection
@else
    @include('../layouts/notAuthorized')
@endif