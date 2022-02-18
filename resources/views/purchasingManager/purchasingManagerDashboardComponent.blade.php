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
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#detail-{{ $oh -> id }}">Detail</button>
                {{-- @if(strpos($oh -> status, 'Delivered') !== false || strpos($oh -> status, 'Completed') !== false || strpos($oh -> status, 'Purchasing Manager') !== false || strpos($oh -> status, 'Rechecked') !== false) --}}
                {{-- @if(strpos($oh -> status, 'Delivered') !== false || strpos($oh -> status, 'Completed') !== false)
                    <a href="/purchasing-manager/{{ $oh -> id }}/download-po" class="btn btn-warning" target="_blank">Download PO</a>
                @endif --}}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- Modal detail --}}
@foreach($orderHeads as $o)
    <div class="modal fade" id="detail-{{ $o->id }}" tabindex="-1" role="dialog" aria-labelledby="detailTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <div class="d-flex justify-content-around">
                        <h5><span style="color: white">Order : {{ $o->order_id }}</span></h5>
                        <h5 class="ml-5"><span style="color: white">Processed By : {{ $o->approvedBy }}</span></h5>
                        <h5 class="ml-5"><span style="color: white">Tipe Order : {{ $o -> orderType }}</span></h5>
                        <h5 class="ml-5"><span style="color: white">Tipe Pesanan{{ $o -> itemType }}</span></h5>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-around mb-3">
                        <h5>Nomor PR : {{ $o -> noPr }}</h5>
                        <h5 class="ml-3">Nomor PO : {{ $o -> noPo }}</h5>
                        {{-- @if($o -> status == 'Order Being Finalized By Purchasing Manager')
                            <div class="ml-auto mr-3">
                                <button class="btn-sm btn-primary">Finalized</button>
                            </div>
                        @endif --}}
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
                                @if($od -> orders_id == $o -> id)
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
                <div class="modal-footer d-flex justify-content-center">
                    {{-- Check if the order is rejected, then do not show the approve & reject button --}}
                    {{-- @if(strpos($o -> status, 'In Progress By Purchasing Manager') !== false) --}}
                    {{-- <a href="/purchasing-manager/order/{{ $o -> id }}/approve" class="btn btn-primary">Review Order</a> --}}
                    {{-- @endif --}}
                    {{-- @if($o -> retries < 2 && $o -> status == 'Order Being Finalized By Purchasing Manager')
                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#revise-{{ $o -> id }}">Revise Order</button>
                    @endif --}}
                    @if(strpos($o -> status, 'In Progress By Purchasing Manager') !== false || strpos($o -> status, 'Delivered') !== false || strpos($o -> status, 'Completed') !== false || strpos($o -> status, 'Finalized') !== false)
                        <a href="/purchasing-manager/order/{{ $o -> id }}/order-detail" class="btn btn-primary">Order Detail</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach