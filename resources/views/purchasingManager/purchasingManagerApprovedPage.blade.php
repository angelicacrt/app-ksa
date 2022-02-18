@if(Auth::user()->hasRole('purchasing') || Auth::user()->hasRole('purchasingManager'))
    @extends('../layouts.base')

    @section('title', 'Purchasing Approve Order')

    @section('container')
    <div class="row">
        @include('purchasingManager.sidebar')
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">

                <h2 class="mt-3" style="text-align: center">Order {{ $orderHeads -> order_id }}</h2>

                @if(strpos($orderHeads -> status, 'Finalized') !== false)
                    <div class="d-flex justify-content-end mr-5">
                        <button class="btn btn-info" data-toggle="modal" data-target="#finalize-{{ $orderHeads -> id }}">Finalize Order</button>
                    </div>
                @endif
                
                @error('reason')
                    <div class="alert alert-danger alert-remove" style="width: 40%; margin-left: 30%">
                        Alasan Invalid
                    </div>
                @enderror

                <div class="row mt-4">
                    <div class="col">
                        <div class="form-group">
                            <label for="approvedBy">Approved By</label>
                            <input type="text" class="form-control" id="boatName" name="boatName" value="{{ $orderHeads -> approvedBy }}" readonly>
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
                            <input type="text" class="form-control" id="noPo" name="noPo" value="{{ $orderHeads -> noPo }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="invoiceAddress">Alamat Invoice</label>
                            <input type="text" class="form-control" id="invoiceAddress" name="invoiceAddress" value="{{ $orderHeads -> invoiceAddress }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="itemAddress">Alamat Barang</label>
                            <input type="text" class="form-control" id="itemAddress" name="itemAddress" value="{{ $orderHeads -> itemAddress }}" readonly>
                        </div>
                        @php
                            if($orderHeads -> ppn == 10){
                                $ppn_val = 'PPN (10%)';
                            }else{
                                $ppn_val = 'NON PPN';
                            }
                        @endphp
                        <div class="form-group">
                            <label for="ppn">PPN</label>
                            <input type="text" class="form-control" id="ppn" name="ppn" value="{{ $ppn_val }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="discount">Discount (%)</label>
                            <input type="text" class="form-control" id="discount" name="discount" value="{{ $orderHeads -> discount }}%" readonly>
                        </div>
                        <div class="form-group">
                            <label for="price">Total Harga (setelah ppn & diskon)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-white">Rp.</div>
                                </div>
                                <input type="text" class="form-control" id="totalPrice" name="totalPrice" value="{{ number_format($orderHeads -> totalPrice, 2, ",", ".") }}" readonly>
                            </div>
                        </div>
                        <label for="radioButton">Tipe Order</label>
                            <div class="form-group">
                                <div class="form-check form-check-inline ml-3">
                                    <input class="form-check-input" type="radio" name="itemType" id="itemType1" value="Barang" 
                                    @if($orderHeads -> itemType == 'Barang')
                                        {{ 'checked' }}
                                    @endif
                                    disabled
                                    >
                                    <label class="form-check-label" for="itemType1">
                                        Barang
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="itemType" id="itemType2" value="Jasa"
                                    @if($orderHeads -> itemType == 'Jasa')
                                        {{ 'checked' }}
                                    @endif
                                    disabled
                                    >
                                    <label class="form-check-label" for="itemType2">
                                        Jasa
                                    </label>
                                </div>
                            </div>
                        <div class="d-flex justify-content-center mt-4 mb-3">
                            @if(strpos($orderHeads -> status, 'Recheked') !== false || strpos($orderHeads -> status, 'In Progress By Purchasing Manager') !== false)
                                <button type="button" class="btn btn-danger mr-3" data-toggle="modal" data-target="#reject-order-{{ $orderHeads->id }}">Reject</button>
                                <form method="POST" action="/purchasing-manager/order/{{ $orderHeads -> id }}/approve">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">Approve</button>
                                </form>
                            @elseif($orderHeads -> retries < 2 && strpos($orderHeads -> status, 'Order Being Finalized By Purchasing Manager') !== false)
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#revise-{{ $orderHeads -> id }}">Revise Order</button>
                            @elseif($orderHeads -> retries == 2)
                                <div class="alert alert-danger" style="width: 40%;">
                                    Maximum Revision Reached !
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col mt-3">
                        <table class="table" id="myTable">
                            <thead class="thead bg-danger">
                                    <th scope="col">Item Barang</th>
                                    <th scope="col" class="center">Quantity</th>
                                    <th scope="col">Harga per Barang</th>
                                    <th scope="col">Harga</th>
                                    <th scope="col">Supplier</th>
                                    <th scope="col">Status Barang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderDetails as $od)
                                    <tr>
                                        <td>
                                            <h5>{{ $od -> item -> itemName }} - ({{ $od -> department }})</h5>
                                        </td>

                                        <td class="center">
                                            <h5>{{ $od -> quantity }} {{ $od -> item -> unit }}</h5>
                                        </td>

                                        <td>
                                            <div class="d-flex">
                                                <h5 class="mr-2">Rp. {{ number_format($od -> itemPrice, 2, ",", ".") }}</h5>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <h5>Rp. {{ number_format($od -> totalItemPrice, 2, ",", ".")}}</h5>
                                        </td>
                                        
                                        <td>
                                            <h5>
                                                {{ $od -> supplier }}
                                            </h5>
                                        </td>

                                        <td>
                                            <h5>
                                                @if($od -> orderItemState == 'Accepted')
                                                    <span style="color: green; font-weight: bold;">{{ $od -> orderItemState }}</span>
                                                @else
                                                    <span style="color: red; font-weight: bold;">{{ $od -> orderItemState }}</span>
                                                @endif
                                            </h5>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>

            {{-- Modal Reject Order --}}
            <div class="modal fade" id="reject-order-{{ $orderHeads -> id }}" tabindex="-1" role="dialog" aria-labelledby="reject-orderTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title" id="rejectTitle" style="color: white">Reject Order {{ $orderHeads -> order_id }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="POST" action="/purchasing-manager/order/{{ $orderHeads -> id }}/reject">
                        @csrf
                        @method('patch')
                        <div class="modal-body"> 
                            <label for="reason">Alasan</label>
                            <textarea class="form-control" name="reason" id="reason" rows="3" placeholder="Input Alasan Reject Order" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
                </div>
            </div>
            
            {{-- Modal Revise Order --}}
            <div class="modal fade" id="revise-{{ $orderHeads -> id }}" tabindex="-1" role="dialog" aria-labelledby="detailTitle"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-danger">
                            <h5 class="modal-title" id="reviseTitle" style="color: white">Revise Order</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="POST" action="/purchasing-manager/{{ $orderHeads -> id }}/revise-order">
                            @csrf
                            @method('patch')
                            <div class="modal-body"> 
                                <label for="reason">Alasan</label>
                                <textarea class="form-control" name="reason" id="reason" rows="3" placeholder="Input Alasan Revisi Order" required></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                        {{-- <div class="modal-body"> 
                            <div class="d-flex flex-column justify-content-center align-items-center">
                                <span class="text-danger" data-feather="alert-circle" style="height: 15%; width: 15%;"></span>
                                <h5 class="font-weight-bold mt-3">Are You Sure To Revise This Order ?</h5>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a class="btn btn-primary" href="/purchasing-manager/{{ $orderHeads -> id }}/revise-order">Submit</a>
                        </div> --}}
                    </div>
                </div>
            </div>

            {{-- Modal Finalize Order --}}
            <div class="modal fade" id="finalize-{{ $orderHeads -> id }}" tabindex="-1" role="dialog" aria-labelledby="detailTitle"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-danger">
                            <h5 class="modal-title" id="finalizeTitle" style="color: white">Finalize Order</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body"> 
                            <div class="d-flex flex-column justify-content-center align-items-center">
                                <span class="text-danger" data-feather="alert-circle" style="height: 15%; width: 15%;"></span>
                                <h5 class="font-weight-bold mt-3">Are You Sure To Finalize This Order ?</h5>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a class="btn btn-primary" href="/purchasing-manager/{{ $orderHeads -> id }}/finalize-order">Submit</a>
                        </div>
                    </div>
                </div>
            </div>
    </div>

    <script type="text/javascript">
        setTimeout(function() {
            $('.alert-remove').fadeOut('fast');
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
            min-width: 120px;
            max-width: 120px;
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