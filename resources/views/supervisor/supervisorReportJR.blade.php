@if(Auth::user()->hasRole('supervisorLogistic') || Auth::user()->hasRole('supervisorLogisticMaster'))
    @extends('../layouts.base')

    @section('title', 'Supervisor JR Reports')

    @section('container')
    <div class="row">
        @include('supervisor.sidebar')
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            
            <div class="flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 mt-3">
                <h1 class="d-flex justify-content-center mb-4">Reports JR ({{ $str_month }})</h1>

                @if(count($jobs) > 0)
                    <div class="d-flex justify-content-end mr-3">
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#exampleModalCenter">
                            Export
                        </button>
                    </div>
                @endif

            <div class="table-wrapper-scroll-y my-custom-scrollbar tableFixHead" style="overflow-x:auto;">
                <table class="table table-bordered sortable">
                    <thead class="thead bg-danger">
                    <tr>
                        <th scope="col">Nomor</th>
                        <th scope="col">cabang</th>
                        <th scope="col">#ID JR</th>
                        <th scope="col">Tanggal JR</th>
                        <th scope="col">Nomor JR</th>
                        <th scope="col">Created By</th>
                        <th scope="col">Nama Kapal</th>
                        <th scope="col">Lokasi</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Keterangan</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($jobs as $key=>$o)
                            <tr>
                                <td style="text-transform: uppercase"><strong>{{ $key + 1  }}</td>
                                <td style="text-transform: uppercase"><strong>{{ $o -> cabang}}</td>
                                <td style="text-transform: uppercase"><strong>{{ $o -> Headjasa_id }}</td>
                                <td style="text-transform: uppercase"><strong>{{ $o -> jrDate }}</td>
                                <td style="text-transform: uppercase"><strong>{{ $o -> noJr}}</td>
                                <td style="text-transform: uppercase"><strong>{{ $o -> created_by}}</td>
                                <td style="text-transform: uppercase"><strong>{{ $o -> tugName}} / {{ $o -> bargeName}}</td>
                                <td style="text-transform: uppercase"><strong>{{ $o -> lokasi}}</td>
                                <td style="text-transform: uppercase"><strong>{{ $o -> quantity}}</td>
                                <td style="text-transform: uppercase"><strong>{{ $o -> note}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal Download-->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Download Job Request Request</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                <a href="/supervisor/Jr_report/download" style="color: white" class="btn btn-dark" target="_blank">Download JR As Excel</a>
                <a href="/supervisor/Jr_report/download_pdf" style="color: white" class="btn btn-dark" target="_blank">Download JR As PDF</a>
            </div>
        </div>
        </div>
    </div>

    <style>
        .tableFixHead          { overflow: auto; height: 250px; }
        .tableFixHead thead th { position: sticky; top: 0; z-index: 1; }

        .my-custom-scrollbar {
            position: relative;
            height: 800px;
            overflow: auto;
        }
        .table-wrapper-scroll-y {
            display: block;
        }

        th{
            color: white;
        }

        td, th{
            word-wrap: break-word;
            min-width: 160px;
            max-width: 160px;
            text-align: center;
        }
        .modal-backdrop {
            height: 100%;
            width: 100%;
        }
    </style>
    <script src="https://www.kryogenix.org/code/browser/sorttable/sorttable.js"></script>
    @endsection
@else
    @include('../layouts/notAuthorized')
@endif