<table class="table">
    <thead class="thead bg-danger">
        <tr>
            <th scope="col">Order ID</th>
            <th scope="col">Status</th>
            <th scope="col">Keterangan</th>
            <th scope="col" class="text-center">Action/Detail</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orderHeads as $o)
        <tr>
            <td><strong>{{ $o -> order_id}}</strong></td>
            @if(strpos($o -> status, 'Rejected') !== false)
                <td style="color: red; font-weight: bold">{{ $o -> status}}</td>
            @elseif(strpos($o -> status, 'Completed') !== false)
                <td style="color: green; font-weight: bold">{{ $o -> status}}</td>
            @elseif($o -> status == 'On Delivery' || $o -> status == 'Items Ready')
                <td style="color: blue; font-weight: bold">{{ $o -> status}}</td>
            @else
                <td>{{ $o -> status}}</td>
            @endif
            
            @if(strpos($o -> status, 'Rejected') !== false)
                <td style="word-wrap: break-word;min-width: 250px;max-width: 250px;">{{ $o -> reason}}</td>
            @else
                <td style="word-wrap: break-word;min-width: 250px;max-width: 250px;">{{ $o -> descriptions}}</td>
            @endif

            @if($o -> status == 'On Delivery' || $o -> status == 'Items Ready')
                <td >
                    <button type="button" class="btn btn-info" data-toggle="modal" id="detail" data-target="#editItem-{{ $o -> id }}">
                        Detail
                    </button>
                    <a href="/crew/order/{{ $o->id }}/accept" class="btn btn-primary ml-3">Accept</a>
                </td>
            @else
            <td>
                <button type="button" class="btn btn-info" data-toggle="modal" id="detail" data-target="#editItem-{{ $o -> id }}">
                    Detail
                </button>
            </td>
            @endif
        </tr>
        @endforeach
    </tbody>

</table>

@foreach($orderHeads as $o)
    <div class="modal fade" id="editItem-{{ $o->id }}" tabindex="-1" role="dialog" aria-labelledby="editItemTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <div class="d-flex-column">
                        <h5 class="modal-title" id="detailTitle" style="color: white"><strong>Nama Kapal</strong></h5>
                        <h5 class="modal-title" id="detailTitle" style="color: white">{{ $o->boatName }}</h5>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Item Barang</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Department</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orderDetails as $od)
                                @if($od -> orders_id == $o -> id)
                                    <tr>
                                        <td>{{ $od -> item -> itemName }}</td>
                                        <td>{{ $od -> quantity }} {{ $od -> item -> unit }}</td>
                                        <td>{{ $od -> department }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endforeach