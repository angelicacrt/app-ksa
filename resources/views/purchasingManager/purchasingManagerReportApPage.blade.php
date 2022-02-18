@if(Auth::user()->hasRole('purchasingManager'))
    @extends('../layouts.base')

    @section('title', 'Report AP')

    @section('container')
    <div class="row">
        @include('purchasingManager.sidebar')
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            
            <div class="flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 mt-3">
                <h1 class="d-flex justify-content-center mb-4">Reports List AP Cabang {{ $default_branch }} ({{ $str_month }})</h1>
                
                <div class="d-flex justify-content-between mr-3">
                    <div class="p-2">
                        <select name="cabang" class="form-select" onchange="window.location = this.value;">
                            <option selected disabled>Pilih Cabang</option>
                            <option value="/purchasing-manager/report-ap/Jakarta" 
                                @php
                                    if($default_branch == 'Jakarta'){
                                        echo('selected');
                                    }
                                @endphp
                            >Jakarta</option>
                            <option value="/purchasing-manager/report-ap/Banjarmasin"
                                @php
                                    if($default_branch == 'Banjarmasin'){
                                        echo('selected');
                                    }
                                @endphp
                            >Banjarmasin</option>
                            <option value="/purchasing-manager/report-ap/Samarinda"
                                @php
                                    if($default_branch == 'Samarinda'){
                                        echo('selected');
                                    }
                                @endphp
                            >Samarinda</option>
                            <option value="/purchasing-manager/report-ap/Bunati"
                                @php
                                    if($default_branch == 'Bunati'){
                                        echo('selected');
                                    }
                                @endphp
                            >Bunati</option>
                            <option value="/purchasing-manager/report-ap/Babelan"
                                @php
                                    if($default_branch == 'Babelan'){
                                        echo('selected');
                                    }
                                @endphp
                            >Babelan</option>
                            <option value="/purchasing-manager/report-ap/Berau"
                                @php
                                    if($default_branch == 'Berau'){
                                        echo('selected');
                                    }
                                @endphp
                            >Berau</option>
                        </select>
                    </div>
                    <div class="p-2 mr-auto">
                        <input type="text" id="myInput" class="form-control" placeholder="Search by Invoice or Supplier" name="search" style="width: 15vw">
                    </div>
                    @if(count($apList) > 0)
                        <div class="p-2">
                            <a href="/purchasing-manager/report-ap/{{ $default_branch }}/export" class="btn btn-outline-danger mb-3" target="_blank">Export</a>
                        </div>
                    @endif
                </div>

                <div id="content">
                    <div class="table-wrapper-scroll-y my-custom-scrollbar tableFixHead" style="overflow-x:auto;">
                        <table id="myTable" class="table table-bordered sortable">
                            <thead class="thead bg-danger">
                                <tr>
                                    <th scope="col">Nama Pembuat</th>
                                    <th scope="col">Nama Supplier</th>
                                    <th scope="col">No. Invoice</th>
                                    <th scope="col">No. Faktur Pajak</th>
                                    <th scope="col">No. DO</th>
                                    <th scope="col">No. PO</th>
                                    <th scope="col">No. PR</th>
                                    <th scope="col">Nominal Invoice</th>
                                    <th scope="col">Due Date</th>
                                    <th scope="col">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($apList as $ap)
                                    <tr>
                                        <td>{{ $ap -> userWhoSubmittedz }}</td>
                                        <td>{{ $ap -> supplierName }}</td>
                                        <td>{{ $ap -> noInvoice }}</td>
                                        <td>{{ $ap -> noFaktur }}</td>
                                        <td>{{ $ap -> noDo }}</td>
                                        <td>{{ $ap -> orderHead -> noPo }}</td>
                                        <td>{{ $ap -> orderHead -> noPr }}</td>
                                        <td>Rp. {{ number_format($ap -> nominalInvoice, 2, ",", ".") }}</td>
                                        <td>{{ date('d/m/Y', strtotime($ap -> dueDate)) }}</td>
                                        <td>{{ $ap -> additionalInformation}}</td>
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
            height: 900px;
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
    
    <script type="text/javascript">
        function refreshDiv(){
            $('#content').load(location.href + ' #content')
        }
        setInterval(refreshDiv, 60000);

        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 3000);

        function filterTable(event) {
            var filter = event.target.value.toUpperCase();
            var rows = document.querySelector("#myTable tbody").rows;
            
            for (var i = 0; i < rows.length; i++) {
                var firstCol = rows[i].cells[0].textContent.toUpperCase();
                var secondCol = rows[i].cells[1].textContent.toUpperCase();
                if (firstCol.indexOf(filter) > -1 || secondCol.indexOf(filter) > -1) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }      
            }
        }

        document.querySelector('#myInput').addEventListener('keyup', filterTable, false);
    </script>
    <script src="https://www.kryogenix.org/code/browser/sorttable/sorttable.js"></script>

    @endsection
@else
    @include('../layouts/notAuthorized')
@endif