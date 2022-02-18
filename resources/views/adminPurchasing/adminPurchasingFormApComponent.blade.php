<table class="table">
    <thead class="thead bg-danger">
    <tr>
        <th class="text-white" scope="col">Time Created</th>
        <th class="text-white" scope="col">Status</th>
        <th class="text-white" scope="col">Nomor PO</th>
        <th class="text-white" scope="col">Action</th>
    </tr>
    </thead>
    <tbody>
        @foreach($apList as $key => $ap)
        <tr>
            <td>{{ $ap -> creationTime }}</td>
            @if($ap -> status == 'OPEN')
                <td><span style="color: green; font-weight: bold; font-size: 18px">{{ $ap -> status }}</span></td>
            @else
                <td><span style="color: red; font-weight: bold; font-size: 18px">{{ $ap -> status }}</span></td>
            @endif
            <td>{{ $ap -> orderHead -> noPo }}</td>
            <td>
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#detail-{{ $ap -> id }}">Detail</button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- Modal Detail --}}
@foreach($apList as $key => $ap)
    @if(!empty(Session::get('openApListModalWithId')) && Session::get('openApListModalWithId') == $ap -> id)
        <script>
            let id = {!! json_encode($ap -> id) !!};
            $(document).ready(function(){
                $("#detail-" + id).modal('show');
            });
        </script>
    @endif

    <div class="modal fade" id="detail-{{ $ap -> id }}" tabindex="-1" role="dialog" aria-labelledby="detailTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                {{-- table-refresh{{ $key }} --}}
                <div class="modal-header bg-danger">
                    <div class="d-flex justify-content-start">
                        <h3 style="color: white">{{ $ap -> orderHead -> noPo }}</h3>
                    </div>
                </div>

                <div class="modal-body">
                    @if(session('openApListModalWithId'))
                        <div class="alert alert-success" style="width: 40%; margin-left: 30%">
                            Saved Successfully
                        </div>
                    @endif
                    
                    @if(session('errorClosePo'))
                        <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                            PO Already Been Closed
                        </div>
                    @endif

                    <div class="d-flex justify-content-end mb-3 mr-3">
                    <h5 class="mr-auto">Price To Paid : Rp. {{ number_format($ap -> orderHead -> totalPrice - $ap -> paidPrice, 2, ",", ".") }}</h5>
                    <form action="/admin-purchasing/form-ap/upload" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <input type="hidden" name="apListId" value="{{ $ap -> id }}">
                        @if($ap -> status == 'OPEN')
                            @if($ap -> orderHead -> itemType == 'Barang')
                                <button type="submit" class="btn btn-info mr-3">Submit</button>
                            @endif
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#close-{{ $ap -> id }}">Close PO</button>
                        @endif
                    </div>

                    <h5 class="mr-auto mb-3">Original Price : Rp. {{ number_format($ap -> orderHead -> totalPrice, 2, ",", ".") }}</h5>

                    @if($ap -> orderHead -> itemType == 'Barang')
                        <div class="table-modal">
                            <table class="table myTable table-refresh{{ $key }}">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="table-header">Date Uploaded</th>
                                        <th class="table-header">Name</th>
                                        <th class="table-header">Status</th>
                                        <th class="table-header">Description</th>
                                        <th class="table-header">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($i = 1 ; $i <= 20 ; $i++)
                                        @php
                                            // Helper var
                                            $status = 'status_partial' . $i;
                                            $uploadTime = 'uploadTime_partial' . $i;
                                            $description = 'description_partial' . $i;
                                            $filename = 'doc_partial' . $i;
                                            $path_to_file = 'path_to_file' . $i;
                                        @endphp
                                        <tr>
                                            <td>{{ $ap -> $uploadTime }}</td>
                                            <td>Partial {{ $i }}</td>
                                            <td>
                                                @if($ap -> $status == 'On Review')
                                                    <span style="color: gray; font-weight: bold">{{ $ap -> $status }}</span>
                                                @elseif($ap -> $status == 'Rejected')
                                                    <span style="color: Red; font-weight: bold">{{ $ap -> $status }}</span>
                                                @else
                                                    <span style="color: green; font-weight: bold">{{ $ap -> $status }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $ap -> $description }}</td>
                                            <td>
                                                @if($ap -> $status == 'On Review' || $ap -> $status == 'Approved' || $ap -> status == 'CLOSED')
                                                    <span>{{ $ap -> $filename }}</span>
                                                @else
                                                    <input type="hidden" name="apListId" value="{{ $ap -> id }}">
                                                    <input type="hidden" name="cabang" value="{{ $default_branch }}">
                                                    <input type="file" name="doc_partial{{ $i }}" class="form-control">
                                                @endif
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    @endif
                    </form>
                    <div class="mt-4">
                        <form action="/admin-purchasing/form-ap/ap-detail" method="POST">
                            @csrf

                            <input type="hidden" name="totalPrice" value="{{ $ap -> orderHead -> totalPrice }}">
                            <input type="hidden" name="apListId" value="{{ $ap -> id }}">
                            <input type="hidden" name="cabang" value="{{ $default_branch }}">

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="supplierName">Nama Supplier</label>
                                    <select class="form-control" id="supplierName" name="supplierName"
                                        @if($ap -> status == 'CLOSED')
                                            {{ 'disabled' }}
                                        @endif
                                    >
                                        <option class="h-25 w-50" value="" disabled>Choose Supplier...</option>
                                        @foreach($suppliers as $s)
                                            <option class="h-25 w-50" value="{{ $s -> supplierName }}">{{ $s -> supplierName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="noPr">Nomor PR</label>
                                    <input type="text" class="form-control" name="noPr" id="noPr" value="{{ $ap -> orderHead -> noPr }}" readonly>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="noInvoice">Nomor Invoice</label>
                                    <input type="text" class="form-control" name="noInvoice" id="noInvoice" placeholder="Input Nomor Invoice" required
                                        @if($ap -> status == 'CLOSED')
                                            {{ 'readonly' }}
                                        @endif
                                    >
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="dueDate">Due Date</label>
                                    <input type="date" class="form-control" id="dueDate" name="dueDate" required
                                        @if($ap -> status == 'CLOSED')
                                            {{ 'readonly' }}
                                        @endif
                                    >
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="noFaktur">Nomor Faktur Pajak</label>
                                    <input type="text" class="form-control" id="noFaktur" placeholder="Input Nomor Faktur Pajak" name="noFaktur" required
                                        @if($ap -> status == 'CLOSED')
                                            {{ 'readonly' }}
                                        @endif
                                    >
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="noDo">Nomor DO</label>
                                    <input type="text" class="form-control" id="noDo" placeholder="Input Nomor DO" name="noDo" required
                                        @if($ap -> status == 'CLOSED')
                                            {{ 'readonly' }}
                                        @endif
                                    >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="nominalInvoice">Nominal Invoice Yang Harus Dibayar</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">Rp. </div>
                                    </div>
                                    <input type="number" class="form-control" id="nominalInvoice" name="nominalInvoice" min="1" step="0.01" placeholder="Input Nominal Invoice" required 
                                        @if($ap -> status == 'CLOSED')
                                            {{ 'readonly' }}
                                        @endif
                                    >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="additionalInformation">Keterangan (optional)</label>
                                <textarea class="form-control" name="additionalInformation" id="additionalInformation" placeholder="Input Keterangan..." rows="4"
                                    @if($ap -> status == 'CLOSED')
                                        {{ 'readonly' }}
                                    @endif
                                ></textarea>
                            </div>
                            @if($ap -> status != 'CLOSED')
                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div> 
            </div>
        </div>
    </div>

    <div class="modal fade" id="close-{{ $ap -> id }}" tabindex="-1" role="dialog" aria-labelledby="detailTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <div class="d-flex justify-content-start">
                        <h5 class="text-white">Close PO</h5>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-center align-items-center">
                        <h5>Are you sure you want to close this PO?</h5>
                    </div>
                    <div class="d-flex justify-content-center align-items-center mt-2">
                        <span data-feather="alert-circle" style="width: 10vw; height: 10vh;stroke: red;
                        stroke-width: 2;"></span>
                    </div>
                    <div class="d-flex justify-content-center align-items-center mt-3">
                        <button type="button" data-dismiss="modal" class="btn btn-danger">No</button>
                        <form action="/admin-purchasing/form-ap/close" method="POST">
                            @csrf
                            @method('patch')
                            <input type="hidden" name="apListId" value="{{ $ap -> id }}">
                            <button type="submit" class="btn btn-primary ml-3">Yes</button>
                        </form>
                    </div>
                </div> 
            </div>
        </div>
    </div>
@endforeach