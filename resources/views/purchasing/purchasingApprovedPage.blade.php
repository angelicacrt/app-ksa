@if(Auth::user()->hasRole('purchasing'))
    @extends('../layouts.base')

    @section('title', 'Purchasing Approve Order')

    @section('container')
    <div class="row">
        @include('purchasing.sidebar')
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">

            <h2 class="mt-3" style="text-align: center">Order {{ $orderHeads -> order_id }}</h2>
            
            @if(session('status'))
                <div class="alert alert-success" style="width: 40%; margin-left: 30%">
                    {{ session('status') }}
                </div>
            @endif

            @if(session('dropStatus'))
                <div class="alert alert-success" style="width: 40%; margin-left: 30%">
                    Dropped Successfully, <a href="/purchasing/order/{{ $orderHeads -> id }}/{{ Session::get('dropStatus') }}/undo">Click Here To Undo !</a>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    {{ session('error') }}
                </div>
            @endif

            @error('boatName')
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    Nama Kapal Invalid
                </div>
            @enderror

            @error('noPr')
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    Nomor PR Invalid
                </div>
            @enderror

            @error('noPo')
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    Nomor PO Invalid
                </div>
            @enderror

            @error('invoiceAddress')
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    Alamat Pengiriman Invoice Invalid
                </div>
            @enderror

            @error('itemAddress')
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    Alamat Barang Invoice Invalid
                </div>
            @enderror

            @error('supplier')
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    Nama Supplier Invalid
                </div>
            @enderror

            @error('discount')
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    Diskon Invalid
                </div>
            @enderror

            @error('itemPrice')
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    Harga Item Invalid
                </div>
            @enderror

            @error('supplier_id')
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    Nama Supplier Invalid
                </div>
            @enderror

            @error('ppn')
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    PPN Invalid
                </div>
            @enderror

            @error('itemType')
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    Tipe Order Invalid
                </div>
            @enderror

                <div class="row mt-4">
                    <div class="col">
                        @php
                            if(strpos($orderHeads -> status, 'Revised') !== false){
                                $route = 'revise';
                            }else{
                                $route = 'approve';
                            }
                        @endphp
                        <form method="POST" action="/purchasing/order/{{ $orderHeads -> id }}/{{ $route }}">
                            @csrf
                            <div class="form-group">
                                <label for="approvedBy">Approved By</label>
                                <input type="text" class="form-control" id="boatName" name="boatName" value="{{ Auth::user()->name }}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="boatName">Nama Kapal</label>
                                <input type="text" class="form-control" id="boatName" name="boatName" value="{{ $orderHeads -> boatName }}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="noPr">Nomor Purchase Requisition</label>
                                <input type="text" class="form-control" id="noPr" name="noPr" value="{{ $orderHeads -> noPr }}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="noPo">Nomor Purchase Order</label>
                                <input type="text" class="form-control" id="noPo" name="noPo" value="{{ $poNumber }}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="invoiceAddress">Alamat Pengiriman Invoice</label>
                                <select name="invoiceAddress" id="invoiceAddress" class="form-control" onchange="showfield(this.options[this.selectedIndex].value)" required> 
                                    <option value="" disabled>Choose</option>
                                    <option value="Gran Rubina, Jl. HR Rasuna Said Lt.12, Karet Kuningan, Setia Budi, Jak-sel">Gran Rubina, Jl. HR Rasuna Said Lt.12, Karet Kuningan, Setia Budi, Jak-sel</option>
                                    <option value="Jl. Jelawat No.23 RT.002 RW.001 Kel. Sidomulyo-Samarinda">Jl. Jelawat No.23 RT.002 RW.001 Kel. Sidomulyo-Samarinda</option>
                                    <option value="Jl. Olah Bebaya 04 RW 02 Sungai Lais, Kel. Pulau Atas Kec. Sambutan Samarinda - kalimantan Timur ">Jl. Olah Bebaya 04 RW 02 Sungai Lais, Kel. Pulau Atas Kec. Sambutan Samarinda - kalimantan Timur </option>
                                    <option value="Jl. Djok Mentaya no.27-28 Ruko Naga Mas, Banjarmasin">Jl. Djok Mentaya no.27-28 Ruko Naga Mas, Banjarmasin</option>
                                    <option value="Jl. Provinsi KM 150 Sebamban 2 Blok C, No 07 Rt.25 Desa Sumber Baru, Kec.Angsana, Kab. TanahBumbu - Kalimantan Selatan ">Jl. Provinsi KM 150 Sebamban 2 Blok C, No 07 Rt.25 Desa Sumber Baru, Kec.Angsana, Kab. TanahBumbu - Kalimantan Selatan </option>
                                    <option value="Jl. Gajah Mada no.531 RT 16 (Depan Hotel Mitra), Tanjung Redeb, Kab.Berau - Kalimantan timur">Jl. Gajah Mada no.531 RT 16 (Depan Hotel Mitra), Tanjung Redeb, Kab.Berau - Kalimantan timur</option>
                                    <option value="Jl. bunga seroja no 88 E, Kendari, Sulawesi Tenggara">Jl. bunga seroja no 88 E, Kendari, Sulawesi Tenggara</option>
                                    <option value="Perumahan Tre Vista residence blok A1 no 5, kelurahan kebalen, kec babelan, kab bekasi. 17610">Perumahan Tre Vista residence blok A1 no 5, kelurahan kebalen, kec babelan, kab bekasi. 17610</option>
                                    <option value="Jl. Cendana Gg. Belakang PolsekPlajau Rt.08 Rw.02, Desa Bersujud, Batu licin - Kalimantan Selatan ">Jl. Cendana Gg. Belakang PolsekPlajau Rt.08 Rw.02, Desa Bersujud, Batu licin - Kalimantan Selatan </option>
                                    <option value="Other">Alamat Lain, Input Manual</option>
                                </select>
                                <div id="div1"></div>
                            </div>
                            <div class="form-group">
                                <label for="itemAddress">Alamat Pengiriman Barang</label>
                                <select name="itemAddress" id="itemAddress" class="form-control" onchange="showfield2(this.options[this.selectedIndex].value)" required> 
                                    <option value="" disabled>Choose</option>
                                    <option value="Gran Rubina, Jl. HR Rasuna Said Lt.12, Karet Kuningan, Setia Budi, Jak-sel">Gran Rubina, Jl. HR Rasuna Said Lt.12, Karet Kuningan, Setia Budi, Jak-sel</option>
                                    <option value="Jl. Jelawat No.23 RT.002 RW.001 Kel. Sidomulyo-Samarinda">Jl. Jelawat No.23 RT.002 RW.001 Kel. Sidomulyo-Samarinda</option>
                                    <option value="Jl. Djok Mentaya no.27-28 Ruko Naga Mas, Banjarmasin">Jl. Djok Mentaya no.27-28 Ruko Naga Mas, Banjarmasin</option>
                                    <option value="Other2">Alamat Lain, Input Manual</option>
                                </select>
                                <div id="div2"></div>
                            </div>
                            <div class="form-group">
                                <label for="ppn">Tipe PPN</label>
                                <select class="form-control" id="ppn" name="ppn" required>
                                    <option value="10" 
                                        @if($orderHeads -> ppn == 10)
                                            {{ 'selected' }}
                                        @endif
                                    >PPN 10%</option>
                                    <option value="11" 
                                        @if($orderHeads -> ppn == 11)
                                            {{ 'selected' }}
                                        @endif
                                    >PPN 11%</option>
                                    <option value="0"
                                        @if($orderHeads -> ppn == 0)
                                            {{ 'selected' }}
                                        @endif
                                    >Non - PPN</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="discount">Discount (%)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-white">%</div>
                                    </div>
                                    <input type="number" class="form-control" id="discount" name="discount" min="0" max="100" step="0.1" placeholder="Input Diskon Dalam Angka" value="{{ $orderHeads -> discount }}" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="price" class="mb-2">Total Harga (sebelum ppn & diskon)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-white">Rp.</div>
                                    </div>
                                    {{-- <input type="text" class="form-control" id="totalPrice" name="totalPrice" value="{{ number_format($orderHeads -> totalPrice, 2, ",", ".") }}" readonly> --}}
                                    <input type="text" class="form-control" id="totalPrice" name="totalPrice" value="{{ number_format($orderHeads -> totalPriceBeforeCalculation, 2, ",", ".") }}" readonly>
                                </div>
                            </div>
                            <label for="radioButton">Tipe Order</label>
                            <div class="form-group">
                                <div class="form-check form-check-inline ml-3">
                                    <input class="form-check-input" type="radio" name="itemType" id="itemType1" value="Barang" checked>
                                    <label class="form-check-label" for="itemType1">
                                        Barang
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="itemType" id="itemType2" value="Jasa" disabled>
                                    <label class="form-check-label" for="itemType2">
                                        Jasa
                                    </label>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center mt-2">
                                <a href="/dashboard" class="btn btn-danger mr-3">Cancel</a>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                    <div class="col mt-3 overflow-auto">
                        <table class="table" id="myTable">
                            <thead class="thead bg-danger">
                                    <th scope="col">Item Barang</th>
                                    <th scope="col" class="center">Quantity</th>
                                    <th scope="col" class="left-20">Harga per Barang</th>
                                    <th scope="col" class="center">Harga</th>
                                    <th scope="col" class="center">Supplier</th>
                                    <th scope="col" class="center">" class="center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderDetails as $od)
                                    <tr>
                                        <td>
                                            <h5>{{ $od -> item -> itemName }} - ({{ $od -> department }})</h5>
                                        </td>

                                        <td class="center">
                                            <h5>{{ $od -> acceptedQuantity }} {{ $od -> item -> unit }}</h5>
                                        </td>

                                        <form action="/purchasing/order/{{ $orderHeads -> id }}/{{ $od -> id }}/edit" method="POST">
                                            @csrf
                                            @method('patch')
                                            <td>
                                                <div class="form-group d-flex">
                                                    <h5 class="mr-2">Rp. </h5>
                                                    <input class="input" style="width: 100%;" type="number" class="form-control" id="itemPrice" name="itemPrice" value="{{ $od -> itemPrice }}" min="1" step="0.01">
                                                </div>
                                            </td>
                                            
                                            <td class="center">
                                                <h5>Rp. {{ number_format($od -> totalItemPrice, 2, ",", ".")}}</h5>
                                            </td>
                                            
                                            <td>
                                                <div class="form-group">
                                                    <select class="form-control form-control-sm" id="supplier" name="supplier">
                                                        <option class="h-25 w-50" value="" disabled>Choose Supplier...</option>
                                                        @foreach($suppliers as $s)
                                                            <option class="h-25 w-50" value="{{ $s -> supplierName }}">{{ $s -> supplierName }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>

                                            <td class="center">
                                                @if(count($orderDetails) > 1)
                                                    <button type="button" class="btn btn-sm mr-2" data-toggle="modal" data-target="#drop-{{ $od -> id }}"><span data-feather="trash-2"></span></button>
                                                @endif
                                                <button type="submit" class="btn btn-info btn-sm">Save</button>
                                            </td>
                                        </form>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
    </div>

    @foreach($orderDetails as $od)
        <div class="modal fade" id="drop-{{ $od -> id }}" tabindex="-1" role="dialog" aria-labelledby="reject-orderTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="rejectTitle" style="color: white">Reject Item - {{ $od -> item -> itemName }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="/purchasing/order/{{ $od -> id }}/drop">
                    @csrf
                    @method('patch')
                    <input type="hidden" name="orderHeadsId" value="{{ $orderHeads -> id }}">
                    <div class="modal-body"> 
                        <div class="d-flex flex-column justify-content-center align-items-center">
                            <span class="text-danger" data-feather="alert-circle" style="height: 15%; width: 15%;"></span>
                            <h5 class="font-weight-bold mt-3">Are You Sure To Reject This Item ?</h5>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    @endforeach

    <script type="text/javascript">
        function showfield(name){
            if(name == 'Other') {
                document.getElementById('div1').innerHTML = '<div class="form-group mt-3 mb-3"><input type="text" class="form-control" id="invoiceAddress" name="invoiceAddress" placeholder="Input Alamat..."></div>';
            }
            else {
                document.getElementById('div1').innerHTML='';
            }
        }
        function showfield2(name){
            if(name == 'Other2') {
                document.getElementById('div2').innerHTML = '<div class="form-group mt-3 mb-3"><input type="text" class="form-control" id="itemAddress" name="itemAddress" placeholder="Input Alamat..."></div>';
            }
            else {
                document.getElementById('div2').innerHTML='';
            }
        }

        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 3000);
    </script>

    <script type="text/javascript">
        function trim_text(el) {
            el.value = el.value.
            replace(/(^\s*)|(\s*$)/gi, ""). // removes leading and trailing spaces
            replace(/[ ]{2,}/gi, " "). // replaces multiple spaces with one space
            replace(/\n +/, "\n"); // Removes spaces after newlines
            return;
        }
        $(function(){
            $("textarea").change(function() {
                trim_text(this);
            });

            $("input").change(function() {
                trim_text(this);
            });
        }); 
    </script>

    <style>
        h5{
            font-size: 16px;
        }
        label{
            font-weight: bold;
        }
        th{
            color: white;
        }
        td, th{
            word-wrap: break-word;
            min-width: 140px;
            max-width: 140px;
            text-align: left;
            vertical-align: middle;
        }
        .center{
            text-align: center;
        }
        .alert{
                text-align: center;
        }
        .modal-backdrop {
            height: 100%;
            width: 100%;
        }
    </style>

    @endsection
@else
    @include('../layouts/notAuthorized')
@endif