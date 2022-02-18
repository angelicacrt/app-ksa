<table class="table" id="myTable">
    <thead class="thead bg-danger">
        <tr>
            <th scope="col">Order ID</th>
            <th scope="col">Status</th>
            <th scope="col">Keterangan</th>
            <th scope="col">Detail</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orderHeads as $oh)
        <tr>
            <td><strong>{{ $oh -> order_id}}</strong></td>
            @if(strpos($oh -> status, 'Rejected') !== false)
                <td><span style="color: red; font-weight: bold">{{ $oh -> status}}</span></td>
            @elseif(strpos($oh -> status, 'Completed') !== false)
                <td><span style="color: green; font-weight: bold">{{ $oh -> status}}</span></td>
            @elseif(strpos($oh -> status, 'Delivered') !== false)
                <td><span style="color: blue; font-weight: bold">{{ $oh -> status}}</span></td>
            @else
                <td>{{ $oh -> status}}</td>
            @endif

            @if(strpos($oh -> status, 'Rejected') !== false)
                <td>{{ $oh -> reason}}</td>
            @else
                <td>{{ $oh -> descriptions}}</td>
            @endif

            <td>
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#detail-{{ $oh -> id }}">Detail</button>
                {{-- @if(strpos($oh -> status, 'Rejected') !== false)
                @else --}}
                @if(strpos($oh -> status, 'Delivered') !== false || strpos($oh -> status, 'Order Completed') !== false)
                    <a href="/supervisor/{{ $oh -> id }}/download-pr" class="btn btn-warning" target="_blank">Download PR</a>
                @endif
            </td>

        </tr>
        @endforeach
    </tbody>
</table>

{{-- Modal detail --}}
@foreach($orderHeads as $o)
    <div class="modal fade" id="detail-{{ $o->id }}" tabindex="-1" role="dialog" aria-labelledby="detailTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex-column">
                            <h5 class="modal-title" id="detailTitle" style="color: white"><strong>Order ID</strong></h5>
                            <h5 class="modal-title" id="detailTitle" style="color: white">{{ $o->order_id }}</h5>
                        </div>
                        <div class="d-flex-column ml-5">
                            <h5 class="modal-title" id="detailTitle" style="color: white"><strong>Nama Kapal</strong></h5>
                            <h5 class="modal-title" id="detailTitle" style="color: white">{{ $o->boatName }}</h5>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-around">
                        <h5>Nomor PR : {{ $o -> noPr }}</h5>
                        <h5>Tipe Order : {{ $o -> orderType }}</h5>
                    </div>
                    <table class="table">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Item Barang</th>
                                @if(strpos($o -> order_id, 'ROID') !== false)
                                    <th scope="col">Accepted Quantity</th>
                                @else
                                    <th scope="col">Quantity</th>
                                @endif
                                {{-- <th scope="col">Umur Barang</th> --}}
                                <th scope="col">Department</th>
                                <th scope="col">Terakhir Diberikan</th>
                                @if(strpos($o -> status, 'Order In Progress By Supervisor') !== false)
                                    <th scope="col">Stok Barang</th>
                                @endif
                                <th scope="col">Note</th>
                                <th scope="col">Status Barang</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orderDetails as $od)
                                @if($od -> orders_id == $o -> id)
                                    <tr>
                                        <td><strong>{{ $od -> item -> itemName }}</strong></td>
                                        <td>
                                            <strong>{{ $od -> acceptedQuantity }} {{ $od -> item -> unit }}</strong>
                                        </td>
                                        {{-- <td>{{ $od -> item -> itemAge }}</td> --}}
                                        <td>{{ $od -> department }}</td>
                                        <td>{{ $od -> item -> lastGiven }}</td>
                                        @if(strpos($o -> status, 'Order In Progress By Supervisor') !== false)
                                            <td><strong>{{ $od -> item -> itemStock }} {{ $od -> item -> unit }}</strong></td>
                                        @endif
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
                    {{-- Check if the order is rejected, then do not show the approve & reject button --}}
                    @if(strpos($o -> status, 'In Progress By Supervisor') !== false)

                        {{-- Button to trigger modal 2 --}}
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#reject-order-{{ $o -> id }}">Reject</button>

                        <a href="/supervisor/{{ $o -> id }}/approve-order" class="btn btn-primary">Approve</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="reject-order-{{ $o -> id }}" tabindex="-1" role="dialog" aria-labelledby="reject-orderTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" style="color: white" id="rejectTitle">Reject Order {{ $o -> order_id }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="/supervisor/{{ $o -> id }}/reject-order">
                @method('put')
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