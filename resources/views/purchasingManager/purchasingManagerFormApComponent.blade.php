<table class="table">
    <thead class="thead bg-danger">
    <tr>
        <th scope="col">Time Created</th>
        <th scope="col">Status</th>
        <th scope="col">Nomor PO</th>
        <th scope="col">Action</th>
    </tr>
    </thead>
    <tbody>
        @foreach($apList as $ap)
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
                <div class="modal-header bg-danger">
                    <div class="d-flex justify-content-start">
                        <h3 style="color: white">{{ $ap -> orderHead -> noPo }}</h3>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    @if(session('openApListModalWithId'))
                        <div class="alert alert-success" style="width: 40%; margin-left: 30%">
                            Saved Successfully
                        </div>
                    @endif

                    @if($ap -> orderHead -> itemType == 'Barang')
                        <div class="table-modal">
                            <table class="table">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="table-header">Date Uploaded</th>
                                        <th class="table-header">Name</th>
                                        <th class="table-header">Status</th>
                                        <th class="table-header">Description</th>
                                        <th class="table-header">Action</th>
                                    </tr>
                                </thead>
                                <div>
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
                                                    @if($ap -> $status == 'On Review')
                                                        <div class="d-flex justify-content-between">
                                                            <form action="/purchasing-manager/form-ap/download" method="POST" target="_blank">
                                                                @csrf
                                                                <input type="hidden" name="filename" value="{{ $filename }}">
                                                                <input type="hidden" name="apListId" value="{{ $ap -> id }}">
                                                                <input type="hidden" name="pathToFile" value="{{ $path_to_file }}">
                                                                <button class="btn_download" type="submit"><span class="icon" data-feather="download"></span></button>
                                                            </form>
                                                            
                                                            {{-- Modal to reject the file --}}
                                                            {{-- <button class="btn btn-danger btn-sm text-white">Reject</button> --}}
                                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#reject-{{ $ap -> id }}-{{ $filename }}">Reject</button>

                                                            {{-- Reject Modal --}}
                                                            <div class="modal fade" id="reject-{{ $ap -> id }}-{{ $filename }}" tabindex="-1" role="dialog" aria-labelledby="reject-orderTitle" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header bg-danger">
                                                                            <h5 class="modal-title text-white" id="rejectTitle">Reject Document</h5>
                                                                        </div>
                                                                        <form method="POST" action="/purchasing-manager/form-ap/reject">
                                                                            @csrf
                                                                            @method('patch')

                                                                            <input type="hidden" name="apListId" value="{{ $ap -> id }}">
                                                                            <input type="hidden" name="statusColumn" value="{{ $status }}">
                                                                            <input type="hidden" name="description" value="{{ $description }}">
                                                                            <input type="hidden" name="default_branch" value="{{ $default_branch }}">

                                                                            <div class="modal-body"> 
                                                                                <div class="d-flex justify-content-start ml-2">
                                                                                    <label class="text-left" for="reason">Alasan</label>
                                                                                </div>
                                                                                <textarea class="form-control" name="reason" id="reason" rows="3" placeholder="Input Alasan" required></textarea>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="submit" class="btn btn-primary">Submit</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <form action="/purchasing-manager/form-ap/approve" method="POST">
                                                                @csrf
                                                                @method('patch')
                                                                <input type="hidden" name="statusColumn" value="{{ $status }}">
                                                                <input type="hidden" name="apListId" value="{{ $ap -> id }}">
                                                                <input type="hidden" name="default_branch" value="{{ $default_branch }}">
                                                                <button class="btn btn-success btn-sm text-white">Approve</button>
                                                            </form>
                                                        </div>
                                                    @elseif($ap -> $status == 'Approved')
                                                        <div class="d-flex justify-content-center">
                                                            <form action="/purchasing-manager/form-ap/download" method="POST" target="_blank">
                                                                @csrf
                                                                <input type="hidden" name="filename" value="{{ $filename }}">
                                                                <input type="hidden" name="apListId" value="{{ $ap -> id }}">
                                                                <input type="hidden" name="pathToFile" value="{{ $path_to_file }}">
                                                                <button class="btn_download" type="submit"><span class="icon" data-feather="download"></span></button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </div>
                            </table>
                        </div>
                    @endif
                        
                    <div class="mt-4">
                        @foreach($apListDetail as $apDetail)
                            @if(!in_array($ap -> id, $check_ap_in_array))
                                <h5>Data Not Found.</h5>
                            @elseif($apDetail['aplist_id'] == $ap -> id)
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="supplierName">Nama Supplier</label>
                                        <input type="text" class="form-control" id="supplierName" value="{{ $apDetail['supplierName'] }}" readonly>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="noPr">Nomor PR</label>
                                        <input type="text" class="form-control" id="noPr" value="{{ $ap -> orderHead -> noPr }}" readonly>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="noInvoice">Nomor Invoice</label>
                                        <input type="text" class="form-control" id="noInvoice" placeholder="Input Nomor Invoice" value="{{ $apDetail['noInvoice'] }}" readonly>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="nominalInvoice">Nominal Invoice Yang Harus Dibayar</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="nominalInvoice" placeholder="Input Nominal Invoice" value="Rp. {{ number_format($apDetail['nominalInvoice'], 2, ",", ".") }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="noFaktur">Nomor Faktur Pajak</label>
                                        <input type="text" class="form-control" id="noFaktur" placeholder="Input Nomor Faktur Pajak" value="{{ $apDetail['noFaktur'] }}" readonly>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="noDo">Nomor DO</label>
                                        <input type="text" class="form-control" id="noDo" placeholder="Input Nomor DO" value="{{ $apDetail['noDo'] }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="additionalInformation">Keterangan (optional)</label>
                                    <textarea class="form-control" name="additionalInformation" id="additionalInformation" placeholder="Input Keterangan..." rows="4" readonly>{{ $apDetail['additionalInformation'] }}</textarea>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div> 
            </div>
        </div>
    </div>
@endforeach