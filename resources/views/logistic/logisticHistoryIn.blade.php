@if(Auth::user()->hasRole('logistic'))
    @extends('../layouts.base')

    @section('title', 'Logistic Order')

    @section('container')
        <div class="row">
            @include('logistic.sidebar')
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 mt-3 wrapper">
                    <h1 class="d-flex justify-content-center">Goods In Report</h1>
                    <br>
                    
                    <div class="d-flex justify-content-start mb-3">
                        <a href="{{ Route('logistic.historyOut') }}" class="btn btn-outline-success mr-3">Goods Out</a>
                        <a href="{{ Route('logistic.historyIn') }}" class="btn btn-outline-secondary">Goods In</a>
                        
                        <input type="text" class="form-control ml-5 w-25" id="search" onkeyup="myFunction()" placeholder="Search By Tanggal Masuk, Item, Serial Number, Nama Supplier...">
                        
                        @if(count($orderHeads) > 0)
                            <a href="{{ Route('logistic.downloadIn') }}" class="btn btn-outline-danger ml-auto mr-3" target="_blank">Export</a>
                        @endif
                    </div>
                    
                    <div class="table-wrapper-scroll-y my-custom-scrollbar tableFixHead" style="overflow-x:auto;">
                        <table class="table table-bordered sortable" id="myTable">
                            <thead class="thead bg-danger">
                            <tr>
                                <th scope="col">Nomor</th>
                                <th scope="col">Tanggal Masuk</th>
                                <th scope="col">Item Barang Masuk</th>
                                <th scope="col">Serial Number</th>
                                <th scope="col">Qty</th>
                                <th scope="col">Golongan</th>
                                <th scope="col">Nama Supplier</th>
                                <th scope="col">Note</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($orderHeads as $key => $oh)
                                    <tr>
                                        <td class="bg-white">{{ $key + 1 }}</td>
                                        <td class="bg-white">{{ $oh -> approved_at }}</td>
                                        <td class="bg-white"><strong>{{ $oh -> item -> itemName }}</strong></td>
                                        <td class="bg-white">{{ $oh -> item -> serialNo}}</td>
                                        <td class="bg-white"><strong>{{ $oh -> quantity}} {{ $oh -> item -> unit}}</strong></td>
                                        <td class="bg-white">{{ $oh -> item -> golongan}}</td>
                                        <td class="bg-white">{{ $oh -> supplierName}}</td>
                                        <td class="bg-white">{{ $oh -> note}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>

        <style>
            body{
                /* background-image: url('/images/logistic-background.png'); */
                background-repeat: no-repeat;
                background-size: cover;
            }
            .wrapper{
                padding: 10px;
                border-radius: 10px;
                background-color: antiquewhite;
                height: 1000px;
                /* height: 100%; */
            }
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
            .modal-backdrop {
                height: 100%;
                width: 100%;
            }
        </style>

        <script>
            function myFunction() {
                // Declare variables
                var input, filter, table, tr, td, i, txtValue;
                input = document.getElementById("search");
                filter = input.value.toUpperCase();
                table = document.getElementById("myTable");
                tr = table.getElementsByTagName("tr");
                
                // Loop through all table rows, and hide those who don't match the search query
                for (i = 0; i < tr.length; i++) {
                    td = tr[i].getElementsByTagName("td")[1];
                    td1 = tr[i].getElementsByTagName("td")[2];
                    td2 = tr[i].getElementsByTagName("td")[3];
                    td3 = tr[i].getElementsByTagName("td")[6];
                    if (td) {
                        if ( (td.innerHTML.toUpperCase().indexOf(filter) > -1) || (td1.innerHTML.toUpperCase().indexOf(filter) > -1) || (td2.innerHTML.toUpperCase().indexOf(filter) > -1) || (td3.innerHTML.toUpperCase().indexOf(filter) > -1))  {            
                            tr[i].style.display = "";
                        } else {
                            tr[i].style.display = "none";
                        }
                    }
                }
            }
        </script>

        <script src="https://www.kryogenix.org/code/browser/sorttable/sorttable.js"></script>
    @endsection
@else
    @include('../layouts/notAuthorized')
@endif