<table class="table">
    <thead class="thead bg-danger">
        <tr>
            <th scope="col">Order ID</th>
            <th scope="col">Status</th>
            <th scope="col">Description</th>
            <th scope="col">Detail</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orderHeads as $oh)
        <tr>
            <td><strong>{{ $oh -> order_id }}</strong></td>
            @if(strpos($oh -> status, 'Rejected') !== false || strpos($oh -> status, 'Rechecked') !== false || strpos($oh -> status, 'Revised') !== false)
                <td style="color: red; font-weight: bold">{{ $oh -> status}}</td>
            @elseif(strpos($oh -> status, 'Completed') !== false)
                <td style="color: green; font-weight: bold">{{ $oh -> status}}</td>
            @elseif(strpos($oh -> status, 'Item Delivered') !== false)
                <td style="color: blue; font-weight: bold">{{ $oh -> status}}</td>
            @else
                <td>{{ $oh -> status }}</td>
            @endif
            <td>
                @if(strpos($oh -> status, 'Rejected') !== false || strpos($oh -> status, 'Rechecked') !== false || strpos($oh -> status, 'Revised') !== false)
                    {{ $oh -> reason }}
                @else
                    {{ $oh -> descriptions }}
                @endif
            </td>
            <td>
                {{-- Modal button for order details --}}
                <button type="button" class="btn btn-info mb-2" data-toggle="modal" data-target="#detail-{{ $oh -> id }}">Detail</button>
                @if(strpos($oh -> status, 'Delivered') !== false || strpos($oh -> status, 'Completed') !== false || strpos($oh -> status, 'Revised') !== false || strpos($oh -> status, 'Finalized') !== false)
                {{-- @if(strpos($oh -> status, 'Delivered') !== false || strpos($oh -> status, 'Completed') !== false) --}}
                    {{-- <a href="/purchasing/{{ $oh -> id }}/download-po" class="btn btn-warning mb-2" target="_blank">Download PO</a> --}}
                    <button type="button" class="btn btn-dark mb-2" data-toggle="modal" data-target="#downloadPO-order-{{ $oh -> id }}">Download</button>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@foreach($orderHeads as $oh)
    <div class="modal fade" id="detail-{{ $oh->id }}" tabindex="-1" role="dialog" aria-labelledby="detailTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <div class="d-flex justify-content-around">
                        <h5><span style="color: white">Order : {{ $oh -> order_id }}</span></h5>
                        <h5 class="ml-5"><span style="color: white">Processed By : {{ $oh -> approvedBy }}</span></h5>
                        <h5 class="ml-5"><span style="color: white">Tipe Order : {{ $oh -> orderType }}</span></h5>
                        <h5 class="ml-5"><span style="color: white">Tipe Pesanan : {{ $oh -> itemType }}</span></h5>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-around mb-3">
                        <h5>Nomor PR : {{ $oh -> noPr }}</h5>
                        <h5>Nomor PO : {{ $oh -> noPo }}</h5>
                    </div>
                    <table class="table">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Item Barang</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Accepted Quantity</th>
                                <th scope="col">Department</th>
                                <th scope="col">Note</th>
                                <th scope="col">Status Barang</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orderDetails as $od)
                                @if($od -> orders_id == $oh -> id)
                                    <tr>
                                        <td><strong>{{ $od -> item -> itemName }}</strong></td>
                                        <td>{{ $od -> quantity }} {{ $od -> item -> unit }}</td>
                                        <td><strong>{{ $od -> acceptedQuantity }} {{ $od -> item -> unit }}</strong></td>
                                        <td>{{ $od -> department }}</td>
                                        <td>{{ $od -> note }}</td>
                                        <td>
                                            @if($od -> orderItemState == 'Accepted')
                                                <span style="color: green; font-weight: bold;">{{ $od -> orderItemState }}</span>
                                            @else
                                                <span style="color: red; font-weight: bold;">{{ $od -> orderItemState }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div> 
                <div class="modal-footer">
                    {{-- Check if the order is already progressed to the next stage/rejected, then do not show the approve & reject button --}}
                    @if($oh -> status == 'Order In Progress By Purchasing')
                        {{-- Button to trigger modal 2 --}}
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#reject-order-{{ $oh -> id }}">Reject</button>
                        <a href="/purchasing/order/{{ $oh -> id }}/approve" class="btn btn-primary mr-3">Approve</a>
                    @elseif(strpos($oh -> status, 'Rechecked') !== false)
                        <a href="/purchasing/order/{{ $oh -> id }}/approve" class="btn btn-primary mr-3">Review Order</a>
                    @elseif(strpos($oh -> status, 'Revised') !== false)
                        <a href="/purchasing/order/{{ $oh -> id }}/revise" class="btn btn-primary mr-3">Revise Order</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="reject-order-{{ $oh -> id }}" tabindex="-1" role="dialog" aria-labelledby="reject-orderTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="rejectTitle" style="color: white">Reject Order {{ $oh -> order_id }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/purchasing/order/{{ $oh -> id }}/reject">
                @csrf
                <div class="modal-body"> 
                    <label for="reason">Alasan</label>
                    <textarea class="form-control" name="reason" id="reason" rows="3" placeholder="Input Alasan Reject Order"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
        </div>
    </div>
    <div class="modal fade" id="downloadPO-order-{{ $oh -> id }}" tabindex="-1" role="dialog" aria-labelledby="reject-orderTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="rejectTitle" style="color: white">Download PO {{ $oh -> order_id }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    @csrf
                    <div class="modal-body"> 
                        <div class="d-flex flex-column justify-content-center align-items-center">
                            <span class="text-warning" data-feather="download" style="height: 15%; width: 15%;"></span>
                            <h5 class="font-weight-bold mt-3">Choose Download As</h5>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="/purchasing/{{ $oh -> id }}/download-po" class="btn btn-secondary mb-2" target="_blank">Download as Excel</a>
                        <a href="/purchasing/{{ $oh -> id }}/download-po-pdf" class="btn btn-success mb-2" target="_blank">Download as PDF</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach