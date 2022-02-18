@if(Auth::user()->hasRole('logistic'))
    @extends('../layouts.base')

    @section('title', 'Logistic Approve Order')

    @section('container')
    <div class="row">
        @include('logistic.sidebar')
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">

                <div class="wrapper flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 mt-3">
                    <h1 class="mt-3 mb-2" style="text-align: center">Mail Of Goods Out</h1>
                    <h2 class="mt-3 mb-2" style="text-align: center">{{ $orderHeads -> noSbk }}</h2>
                    @if(session('error'))
                        <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if(session('status'))
                        <div class="alert alert-success" style="width: 40%; margin-left: 30%">
                            {{ session('status') }}
                        </div>
                    @endif

                    @error('acceptedQuantity')
                        <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                            Invalid Quantity
                        </div>
                    @enderror

                    <div class="row mt-5">
                        <div class="col">
                            <form method="POST" action="/logistic/order/{{ $orderHeads -> id }}/approve">
                                @csrf
                                <div class="form-group">
                                    <label for="boatName">Nama Kapal</label>
                                    <input type="text" class="form-control" id="boatName" name="boatName" value="{{ $orderHeads -> boatName }}" readonly>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="sender">Yang Menyerahkan</label>
                                            <input type="text" class="form-control" id="sender" name="sender" value="{{ Auth::user()->name }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <label for="receiver">Yang Menerima</label>
                                                <input type="text" class="form-control" id="receiver" name="receiver" value="{{ $orderHeads -> user -> name }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="expedition">Ekspedisi</label>
                                    <select class="form-control" id="expedition" name="expedition">
                                        <option value="onsite">Onsite</option>
                                        <option value="JNE">JNE</option>
                                        <option value="TIKI">TIKI</option>
                                        <option value="JWT">JWT</option>
                                        <option value="Lion">Lion</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="noResi">Nomor Resi (optional)</label>
                                    <input type="text" class="form-control" id="noResi" name="noResi"
                                        placeholder="Input Nomor Resi (optional)">
                                </div>
                                <div class="form-group">
                                    <label for="company">Perusahaan (PR Requirements)</label>
                                    <select class="form-control" name="company" id="company">
                                        <option value="KSA">KSA</option>
                                        <option value="ISA">ISA</option>
                                        <option value="SKB">SKB</option>
                                        <option value="KSAO">KSA OFFSHORE</option>
                                        <option value="KSAM">KSA MARITIME</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="orderType">Tipe Order</label>
                                    <select class="form-control" name="orderType" id="orderType">
                                        <option value="Real Time">Real Time</option>
                                        <option value="Susulan">Susulan</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="description">Deskripsi (optional)</label>
                                    <textarea class="form-control" name="description" id="description" rows="3"
                                        placeholder="Input Keterangan"></textarea>
                                </div>
                                
                                <div class="d-flex justify-content-center mt-5">
                                    <a href="/dashboard" class="btn btn-danger">Cancel</a>
                                    <button type="submit" class="btn btn-primary ml-2">Submit</button>
                                </div>
                            </form>
                        </div>
                        {{-- <div class="col mt-3" style="overflow-x:auto;">
                            <table class="table" id="myTable">
                                <thead class="thead bg-danger">
                                    <tr>
                                        <th scope="col">Nomor</th>
                                        <th scope="col">Item Barang</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Department</th>
                                        <th scope="col">Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orderDetails as $key => $od)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $od -> item -> itemName }}</td>
                                        <td>{{ $od -> quantity }} {{ $od -> item -> unit }}</td>
                                        <td>{{ $od -> department }}</td>
                                        @if(preg_replace('/[a-zA-z ]/', '', $od -> quantity) > $od -> item -> itemStock)
                                            <td style="color: red; font-weight: bold">{{ $od -> item -> itemStock}} {{ $od -> item -> unit }} (Stok Tidak Mencukupi)</td>
                                        @else
                                            <td style="color: green; font-weight: bold">{{ $od -> item -> itemStock}} {{ $od -> item -> unit }}</td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div> --}}
                        <div class="col mt-3 table-wrapper-scroll-y my-custom-scrollbar tableFixHead" style="overflow-x:auto;">
                            <table class="table">
                                <thead class="thead bg-danger">
                                    <tr>
                                        <th scope="col">Nama Barang</th>
                                        <th scope="col">Request Quantity</th>
                                        <th scope="col">Department</th>
                                        <th scope="col">Stok</th>
                                        <th scope="col">Accepted Qty</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orderDetails as $od)
                                    <tr>
                                        <td class="bg-white"><strong>{{ $od -> item -> itemName }}</strong></td>
                                        <td class="bg-white"><strong>{{ $od -> quantity }} {{ $od -> item -> unit }}</strong></td>
                                        <td class="bg-white">{{ $od -> department }}</td>
                                        @if(preg_replace('/[a-zA-z ]/', '', $od -> quantity) > $od -> item -> itemStock)
                                            <td class="bg-white" style="color: red"><strong>{{ $od -> item -> itemStock}} {{ $od -> item -> unit }} (Stok Tidak Mencukupi)</strong></td>
                                        @else
                                            <td class="bg-white" style="color: green"><strong>{{ $od -> item -> itemStock}} {{ $od -> item -> unit }}</strong></td>
                                        @endif
                                        <form action="/logistic/order/{{ $orderHeads -> id }}/edit/{{ $od -> id }}" method="POST">
                                            @csrf
                                            @method('patch')
                                            <td class="bg-white">
                                                <input class="w-75" type="number" min="1" max="{{ $od -> item -> itemStock }}" value="{{ $od -> acceptedQuantity }}" name="acceptedQuantity" id="acceptedQuantity"> {{ $od -> item -> unit }}
                                            </td>
                                            <td class="bg-white">
                                                <button type="submit" class="btn btn-info btn-sm">Save</button>
                                            </td>
                                        </form>
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
        th{
            color: white;
        }
        td, th{
            word-wrap: break-word;
            min-width: 120px;
            max-width: 120px;
            text-align: center;
            vertical-align: middle;
        }
        label{
            font-weight: bold;
        }
        .wrapper{
            padding: 15px;
            margin: 15px;
            border-radius: 10px;
            background-color: antiquewhite;
            height: 1280px;
            /* height: 100%; */
        }
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
        .alert{
            text-align: center;
        }
        .modal-backdrop {
            height: 100%;
            width: 100%;
        }
    </style>

    <script type="text/javascript">
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

    @endsection
@else
    @include('../layouts/notAuthorized')
@endif