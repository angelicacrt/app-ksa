<table class="table table-bordered">
    <thead class="thead bg-danger">
    <tr>
        <th scope="col">Item Barang</th>
        <th scope="col">Dari Cabang</th>
        <th scope="col">Cabang Tujuan</th>
        <th scope="col">Request Qty</th>
        <th scope="col">Stok Cabang Tujuan</th>
        <th scope="col">Nama Requester</th>
        <th scope="col">Status</th>
        <th scope="col">Action</th>
        <th scope="col">Approval</th>
    </tr>
    </thead>
    <tbody>
        @foreach($ongoingOrders as $key => $o)
            <tr>
                <td><strong>{{ $o -> item_requested -> itemName }}</strong></td>
                <td>{{ $o -> fromCabang}}</td>
                <td>{{ $o -> toCabang}}</td>
                <td><strong>{{ $o -> quantity}} {{ $o -> item_requested -> unit}}</strong></td>
                @if($o -> quantity > $o -> item_requested_from -> itemStock )
                    <td><span style="color: red; font-weight: bold">{{ $o -> item_requested_from -> itemStock}} {{ $o -> item_requested -> unit}}</span></td>
                @else
                    <td><span style="color: green; font-weight: bold">{{ $o -> item_requested_from -> itemStock}} {{ $o -> item_requested -> unit}}</span></td>
                @endif
                <td>{{ $o -> user -> name }}</td>
                @if(strpos($o -> status, 'Rejected') !== false)
                    <td><strong style="color: red">{{ $o -> status }}</strong></td>
                @elseif(strpos($o -> status, 'On Delivery') !== false)
                    <td><strong style="color: blue">{{ $o -> status }}</strong></td>
                @elseif(strpos($o -> status, 'Accepted') !== false)
                    <td><strong style="color: green">{{ $o -> status }}</strong></td>
                @else
                    <td>{{ $o -> status }}</td>
                @endif
                <td>
                    <a href="/supervisor/approval-do/{{ $o -> id }}/download"><span data-feather="download" class="icon"></span></a>
                </td>
                {{-- scenario #1 : If the order needs to be approved by the requested branches --}}
                @if($o -> fromCabang == Auth::user()->cabang and strpos($o -> status, 'In Progress By Supervisor Cabang ' . Auth::user()->cabang) !== false)
                    <td>
                        <div class="d-flex justify-content-around">
                            <a href="/supervisor/approval-do/{{ $o -> id }}/deny" class="btn btn-danger btn-sm">Reject</a>
                            <a href="/supervisor/approval-do/{{ $o -> id }}/forward" class="btn btn-success btn-sm">Approve</a>
                        </div>
                    </td>
                {{-- scenario #2 : If the order is already approved by their respective branches, then the destination branches also need to approve --}}
                @elseif($o -> toCabang == Auth::user()->cabang and strpos($o -> status, 'Waiting Approval By Supervisor Cabang '. Auth::user()->cabang) !== false)
                    <td>
                        <div class="d-flex justify-content-around">
                            <a href="/supervisor/approval-do/{{ $o -> id }}/reject" class="btn btn-danger btn-sm">Reject</a>
                            <a href="/supervisor/approval-do/{{ $o -> id }}/approve" class="btn btn-success btn-sm">Approve</a>
                        </div>
                    </td>
                @else
                    <td></td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>