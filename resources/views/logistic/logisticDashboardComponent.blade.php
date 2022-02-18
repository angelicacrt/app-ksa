<table class="table" id="myTable">
    <thead class="thead bg-danger">
        <tr>
            <th scope="col">Order ID</th>
            <th scope="col">Status</th>
            <th scope="col">Keterangan</th>
            <th scope="col">Detail</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orderHeads as $oh)
        <tr>
            <td class="bg-white"><strong>{{ $oh -> order_id}}</strong></td>
            @if(strpos($oh -> status, 'Rejected') !== false)
                <td class="bg-white"><span style="color: red;font-weight: bold;">{{ $oh -> status}}</span></td>
            @elseif(strpos($oh -> status, 'Completed') !== false)
                <td class="bg-white"><span style="color: green;font-weight: bold;">{{ $oh -> status}}</span></td>
            @elseif(strpos($oh -> status, 'On Delivery') !== false || strpos($oh -> status, 'Items Ready') !== false)
                <td class="bg-white"><span style="color: blue;font-weight: bold;">{{ $oh -> status}}</span></td>
            @elseif(strpos($oh -> status, 'Delivered') !== false)
                <td class="bg-white"><span style="color: #16c9e9;font-weight: bold;">{{ $oh -> status }}</span></td>
            @else
                <td class="bg-white">{{ $oh -> status}}</td>
            @endif

            @if(strpos($oh -> status, 'Rejected') !== false)
                <td class="bg-white">{{ $oh -> reason}}</td>
            @else
                <td class="bg-white">{{ $oh -> descriptions}}</td>
            @endif

            {{-- @if(strpos($oh -> status, 'Approved') !== false || strpos($oh -> status, 'Order Completed') !== false) --}}

            {{-- @if(strpos($oh -> status, 'Order') !== false || strpos($oh -> status, 'Delivered') !== false)
                <td>
                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#detail-{{ $oh -> id }}">Detail</button>
                    <a href="/logistic/{{ $oh -> id }}/download-pr" style="color: white" class="btn btn-warning" target="_blank">Download PR</a>
                </td>
            @else --}}
                {{-- Button to trigger the modal detail --}}
                <td class="bg-white"><button type="button" class="btn btn-info" data-toggle="modal" data-target="#detail-{{ $oh -> id }}">
                    Detail
                </button></td>
            {{-- @endif --}}

            <td class="bg-white">
                {{-- @if(strpos($oh -> status, 'Order In Progress') !== false || strpos($oh -> status, 'Delivered') !== false || strpos($oh -> status, 'Order Completed') !== false || strpos($oh -> status, 'Rechecked') !== false || strpos($oh -> status, 'Being Finalized') !== false || strpos($oh -> status, 'Revised') !== false) --}}
                @if(strpos($oh -> status, 'Delivered') !== false || strpos($oh -> status, 'Order Completed') !== false)
                    <a href="/logistic/{{ $oh -> id }}/download-pr" style="color: white" class="btn btn-warning" target="_blank">Download PR</a>
                @endif
                @if(strpos($oh -> status, 'Delivered') !== false)
                    <a href="/logistic/stock-order/{{ $oh -> id }}/accept-order" class="btn btn-primary">Accept</a>
                @endif
            </td>

        </tr>
        @endforeach
    </tbody>
</table>

{{-- Modal detail --}}
@foreach($orderHeads as $oh)
    <div class="modal fade" id="detail-{{ $oh -> id }}" tabindex="-1" role="dialog" aria-labelledby="detailTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex-column">
                            <h5 class="modal-title" id="detailTitle" style="color: white"><strong>Order ID</strong></h5>
                            <h5 class="modal-title" id="detailTitle" style="color: white">{{ $oh -> order_id }}</h5>
                        </div>
                        <div class="d-flex-column ml-5">
                            <h5 class="modal-title" id="detailTitle" style="color: white"><strong>Nama Kapal</strong></h5>
                            <h5 class="modal-title" id="detailTitle" style="color: white">{{ $oh -> boatName }}</h5>
                        </div>
                        <div class="d-flex-column ml-5">
                            <h5 class="modal-title" id="detailTitle" style="color: white"><strong>Request By</strong></h5>
                            <h5 class="modal-title" id="detailTitle" style="color: white">{{ $oh -> user -> name }}</h5>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if(strpos($oh -> status, 'Order') !== false || strpos($oh -> status, 'Delivered') !== false)
                        <div class="d-flex justify-content-around">
                            <h5>Nomor PR : {{ $oh -> noPr }}</h5>
                            <h5>Tipe Order : {{ $oh -> orderType }}</h5>
                        </div>
                    @endif
                    <table class="table">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Item Barang</th>
                                <th scope="col">Request Quantity</th>
                                @if(strpos($oh -> status, 'Items Ready') !== false || strpos($oh -> status, 'On Delivery') !== false || strpos($oh -> status, 'Request Completed') !== false)
                                    <th scope="col">Accepted Quantity</th>
                                @endif

                                {{-- @if(strpos($oh -> status, 'Request') !== false || strpos($oh -> status, 'Items Ready') !== false || strpos($oh -> status, 'On Delivery') !== false) --}}
                                @if(strpos($oh -> order_id, 'COID') !== false)
                                    <th scope="col">Terakhir Diberikan</th>
                                @endif
                                <th scope="col">Umur Barang</th>
                                <th scope="col">Department</th>
                                <th scope="col">Golongan</th>
                                
                                @if(strpos($oh -> order_id, 'ROID') !== false || strpos($oh -> order_id, 'LOID') !== false)
                                    <th scope="col">Status Barang</th>
                                @endif

                                @if(strpos($oh -> status, 'Request In Progress') !== false)
                                    <th scope="col">Stok Barang</th>
                                @endif

                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orderDetails as $od)
                                @if($od -> orders_id == $oh -> id)
                                    <tr>
                                        <td><strong>{{ $od -> item -> itemName }}</strong></td>
                                        <td><strong>{{ $od -> quantity }} {{ $od -> item -> unit }}</strong></td>
                                        @if(strpos($oh -> status, 'Items Ready') !== false || strpos($oh -> status, 'On Delivery') !== false || strpos($oh -> status, 'Request Completed') !== false)
                                            <td><strong>{{ $od -> acceptedQuantity }} {{ $od -> item -> unit }}</strong></td>
                                        @endif

                                        @if(strpos($oh -> order_id, 'COID') !== false)
                                            <td>{{ $od -> item -> lastGiven }}</td>
                                        @endif

                                        <td>{{ $od -> item -> itemAge }}</td>
                                        <td>{{ $od -> department }}</td>
                                        <td>{{ $od -> item -> golongan }}</td>

                                        @if(strpos($oh -> order_id, 'ROID') !== false || strpos($oh -> order_id, 'LOID') !== false)
                                            <td>
                                                @if($od -> orderItemState == 'Accepted')
                                                    <span style="color: green; font-weight: bold;">{{ $od -> orderItemState }}</span>
                                                @else
                                                    <span style="color: red; font-weight: bold;">{{ $od -> orderItemState }}</span>
                                                @endif
                                            </td>
                                        @endif

                                        @if(strpos($oh -> status, 'Request In Progress') !== false)
                                            @if($od -> quantity > $od -> item -> itemStock)
                                                <td style="color: red; font-weight: bold;">{{ $od -> item -> itemStock}} {{ $od -> item -> unit }} (Stok Tidak Mencukupi)</td>
                                            @else
                                                <td style="color: green; font-weight: bold;">{{ $od -> item -> itemStock}} {{ $od -> item -> unit }}</td>
                                            @endif
                                        @endif
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div> 
                <div class="modal-footer">
                    {{-- Check if the order is rejected, then do not show the approve & reject button --}}
                    @if(strpos($oh -> status, 'In Progress By Logistic') !== false)
                        {{-- Button to trigger modal 2 --}}
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#reject-order-{{ $oh -> id }}">Reject</button>
                        <a href="/logistic/order/{{ $oh -> id }}/approve" class="btn btn-primary">Approve</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="reject-order-{{ $oh -> id }}" tabindex="-1" role="dialog" aria-labelledby="reject-orderTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" style="color: white" id="rejectTitle">Reject Order {{ $oh -> order_id }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/logistic/order/{{ $oh->id }}/reject">
                @csrf
                <div class="modal-body"> 
                    <label for="reason">Alasan</label>
                    <textarea class="form-control" name="reason" id="reason" rows="3" placeholder="Input Alasan Reject Order"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Submit</button>
                </div>
            </form>
        </div>
        </div>
    </div>
@endforeach