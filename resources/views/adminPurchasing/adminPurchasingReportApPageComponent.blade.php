<div class="table-wrapper-scroll-y my-custom-scrollbar tableFixHead overflow-auto">
    <table id="myTable" class="table table-bordered">
        <thead class="thead bg-danger">
            <tr>
                <th scope="col">Nama Pembuat</th>
                <th scope="col">Nama Supplier</th>
                <th scope="col">No. Invoice</th>
                <th scope="col">No. Faktur Pajak</th>
                <th scope="col">No. DO</th>
                <th scope="col">No. PO</th>
                <th scope="col">No. PR</th>
                <th scope="col">Nominal Invoice</th>
                <th scope="col">Due Date</th>
                <th scope="col">Keterangan</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($apList as $ap)
                <tr>
                    <td>{{ $ap -> userWhoSubmitted }}</td>
                    <td>{{ $ap -> supplierName }}</td>
                    <td>{{ $ap -> noInvoice }}</td>
                    <td>{{ $ap -> noFaktur }}</td>
                    <td>{{ $ap -> noDo }}</td>
                    <td>{{ $ap -> orderHead -> noPo }}</td>
                    <td>{{ $ap -> orderHead -> noPr }}</td>
                    <td>Rp. {{ number_format($ap -> nominalInvoice, 2, ",", ".") }}</td>
                    <td>{{ date('d/m/Y', strtotime($ap -> dueDate)) }}</td>
                    <td>{{ $ap -> additionalInformation}}</td>
                    <td>
                        <form action="/admin-purchasing/report-ap/{{ $ap -> helper_cursor }}/delete" method="POST">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-warning text-white">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>