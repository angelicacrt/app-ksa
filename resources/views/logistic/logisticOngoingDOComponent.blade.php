<table class="table table-bordered">
    <thead class="thead bg-danger">
    <tr>
        <th scope="col" style="width: 100px">Nomor</th>
        <th scope="col">Item Barang</th>
        <th scope="col">Cabang Tujuan</th>
        <th scope="col">Request Qty</th>
        <th scope="col">Status</th>
        <th scope="col">Download</th>
        <th scope="col">Action</th>
    </tr>
    </thead>
    <tbody>
        @foreach($ongoingOrders as $key => $o)
            <tr>
                <td class="bg-white">{{ $key + 1 }}</td>
                <td class="bg-white"><strong>{{ $o -> item_requested -> itemName }}</strong></td>
                <td class="bg-white">{{ $o -> toCabang}}</td>
                <td class="bg-white"><strong>{{ $o -> quantity}} {{ $o -> item_requested -> unit}}</strong></td>
                @if(strpos($o -> status, 'Rejected') !== false)
                    <td class="bg-white"><strong style="color: red">{{ $o -> status }}</strong></td>
                @elseif(strpos($o -> status, 'On Delivery') !== false)
                    <td class="bg-white"><strong style="color: blue">{{ $o -> status }}</strong></td>
                @elseif(strpos($o -> status, 'Accepted') !== false)
                    <td class="bg-white"><strong style="color: green">{{ $o -> status }}</strong></td>
                @else
                    <td class="bg-white">{{ $o -> status }}</td>
                @endif
                <td class="bg-white">
                    <a href="/logistic/request-do/{{ $o -> id }}/download" target="_blank"><span data-feather="download" class="icon mr-2"></span></a>
                </td>
                <td class="bg-white">
                    @if(strpos($o -> status, 'On Delivery') !== false)
                        <a href="/logistic/request-do/{{ $o -> id }}/accept-do" class="btn btn-info">Accept Delivery</a>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>