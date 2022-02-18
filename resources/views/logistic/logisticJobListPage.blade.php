@if(Auth::user()->hasRole('logistic'))
    @extends('../layouts.base')

    @section('title', 'Job Request Order List')

    @section('container')
    <div class="row">
        @include('logistic.sidebar')
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4" style="padding-bottom: 30px">
            
            @include('../layouts/time')
            <div class="wrapper">
            <h2 class="mt-3 mb-2" style="text-align: center">Job Request List Cabang {{ Auth::user()->cabang }}</h2>

            @if(session('error'))
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('status'))
                <div class="alert alert-success" style="width: 40%; margin-left: 30%">
                    {{ session('status') }}
                </div>
            @endif

            @error('descriptions')
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    Alasan Wajib Diisi
                </div>
            @enderror

            <br>

            <div class="d-flex justify-content-end">
                {{ $JobRequestHeads->links() }}
            </div>

            <div class="d-flex mb-3">
                <form class="mr-auto w-50" action="">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search by Order ID or Status..." name="search" id="search" value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                </form>
                <div>
                    <button class="btn btn-outline-success mr-3">Job Request completed({{  $job_completed }})</button>
                    <button class="btn btn-outline-primary mr-3">Job Request In Progress({{ $job_in_progress }})</button>
                </div>
            </div>

            <div id="content" style="overflow-x:auto;">
                <table class="table" id="myTable">
                    <thead class="thead bg-danger">
                        <tr>
                            <th scope="col">Order ID</th>
                            <th scope="col">Status</th>
                            <th scope="col">Keterangan</th>
                            <th scope="col">Detail</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- job dashboard Details --}}
                        @forelse ($JobRequestHeads as $jr )
                        <tr>
                            <td><strong>{{ $jr -> Headjasa_id}}</strong></td>
                            @if(strpos($jr -> status, 'Job Request Rejected By Logistics') !== false)
                            <td style="color: red; font-weight: bold">{{ $jr -> status}}</td>
                            @elseif(strpos($jr -> status, 'Job Request Approved By Logistics') !== false)
                                <td style="color: green; font-weight: bold">{{ $jr -> status}}</td>
                            @elseif($jr -> status == 'Job Request In Progress By Logistics')
                                <td style="color: blue; font-weight: bold">{{ $jr -> status}}</td>
                            @else
                                <td>{{ $jr -> status}}</td>
                            @endif
                            
                            @if(strpos($jr -> status, 'Rejected') !== false)
                                <td style="word-wrap: break-word;min-width: 250px;max-width: 250px; text-transform: uppercase;"><strong>{{ $jr -> reason}}</td>
                            @elseif(strpos($jr -> status, 'Approved') !== false)
                                <td style="word-wrap: break-word;min-width: 250px;max-width: 250px;">This Job Request is now on Progress By Purchasing</td>
                            @else
                                <td style="word-wrap: break-word;min-width: 250px;max-width: 250px;">This Job Request is Awaiting for Review</td>
                            @endif

                            @if($jr -> status == 'Job Request In Progress By Logistics')
                                <td>
                                    <button type="button" class="btn btn-info" data-toggle="modal" id="detail" data-target="#editJob-{{ $jr -> id }}">
                                        Detail
                                    </button>
                                </td>
                                <td>
                                    <div class="row">
                                        <div class="col">
                                            {{-- <button class="btn btn-success btn-sm">Approve Request</button> --}}
                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#approvecompany-{{$jr-> id}}">
                                                Approve Request
                                            </button>
                                        </div>
                                        <div class="col">
                                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#rejectjob-{{ $jr -> id }}">Reject Request</button>
                                        </div>
                                    </div>
                                </td>
                            @elseif(strpos($jr -> status, 'Rejected') !== false)
                                <td>
                                    {{-- show nothing --}}
                                </td>
                                <td>
                                    {{-- show nothing --}}
                                </td>
                            @else
                                <td>
                                    <button type="button" class="btn btn-info" data-toggle="modal" id="detail" data-target="#editJob-{{ $jr -> id }}">
                                        Detail
                                    </button>
                                </td>
                                <td>
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#downloadJR-{{ $jr->id }}">
                                        Download JR
                                    </button>
                                </td>
                            @endif
                        </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
                
                <!-- Modal download-->
                    @foreach ($JobRequestHeads as $jr)
                        <div class="modal fade" id="downloadJR-{{ $jr->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Download JR : {{$jr -> Headjasa_id }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                </div>
                                <div class="modal-body">
                                    <a href="/logistic/{{ $jr -> id }}/download-JR" style="color: white" class="btn btn-dark" target="_blank">Download JR As Excel</a>
                                    <a href="/logistic/{{ $jr -> id }}/download-JR_pdf" style="color: white" class="btn btn-dark" target="_blank">Download JR As PDF</a>
                                </div>
                            </div>
                            </div>
                        </div>
                    @endforeach
                {{-- modal job details --}}
                    @foreach ($JobRequestHeads as $jr)
                        <div class="modal fade" id="editJob-{{ $jr->id }}" tabindex="-1" role="dialog" aria-labelledby="editJobTitle" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <div class="d-flex-column">
                                            <h5 class="modal-title" id="detailTitle" style="color: white"><strong>Detail Job Request</strong></h5>
                                        </div>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th scope="col">Nama Tugboat / barge</th>
                                                    <th scope="col">Lokasi Perbaikan</th>
                                                    <th scope="col">description</th>
                                                    <th scope="col">quantity</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($jobDetails as $c)
                                                    @if($c -> jasa_id == $jr -> id)
                                                        <tr>
                                                            <td class="bg-white" style="text-transform: uppercase;"><strong>{{ $c ->tugName }} / {{ $c ->bargeName }}</td>
                                                            <td class="bg-white"style="text-transform: uppercase;"><strong>{{ $c ->lokasi }}</td>
                                                            <td class="bg-white">{{ $c ->note }}</td>
                                                            <td class="bg-white">{{ $c ->quantity }}</td>
                                                        </tr>
                                                    @endif
                                                @empty
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                {{-- modal job reject details --}}
                    @foreach($JobRequestHeads as $jr )
                        <div class="modal fade" id="rejectjob-{{ $jr -> id }}" tabindex="-1" role="dialog" aria-labelledby="editJobTitle" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <div class="d-flex-column">
                                            <h5 class="modal-title" id="detailTitle" style="color: white"><strong>Reject Job Request?</strong></h5>
                                        </div>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form method="POST" action="/logistic/Review-Job-Rejected">
                                        @csrf
                                    <div class="modal-body">
                                        <input type="hidden" name='jobhead_id' value= {{$jr->Headjasa_id}}>
                                        <input type="hidden" name='jobhead_name' value= {{$jr->created_by}}>
                                            <div class="form-group">
                                                <label for="reason">Reason</label>
                                                <textarea class="form-control" name="reasonbox" required id="reason" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" id="submitreject2" class="btn btn-danger">Reject Request</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                <!-- Modal approve -->
                    @foreach($JobRequestHeads as $jr )
                        <div class="modal fade" id="approvecompany-{{$jr-> id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-success">
                                <h5 class="modal-title" id="exampleModalLongTitle">Approve Request</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                </div>
                                <form method="POST" action="/logistic/Review-Job-Approved">
                                    @csrf
                                    <div class="modal-body">
                                        <input type="hidden" name='jobhead_id' value= {{$jr->Headjasa_id}}>
                                        <input type="hidden" name='jobhead_name' value= {{$jr->created_by}}>
                                        {{-- send input to identify --}}
                                        <label for="company" class="mb-3">Perusahaan</label>
                                        <select class="form-control" name="company" id="company" required>
                                            <option value="Kartika Samudera Adijaya">KSA</option>
                                            <option value="Iriana Samudera Adijaya">ISA</option>
                                            <option value="KSA OFFSHORE">KSA OFFSHORE</option>
                                            <option value="KSA MARITIME">KSA MARITIME</option>
                                            <option value="Semoga Kartika Bahagia">SKB</option>
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-outline-success">Approve Request</button>
                                    </div>
                                </form>
                            </div>
                            </div>
                        </div>
                    @endforeach
            </div>
        </main>
    </div>

    <style>
        body{
            /* background-image: url('/images/logistic-background.png'); */
            background-repeat: no-repeat;
            background-size: cover;
        }
        .wrapper{
            padding: 15px;
            margin: 15px;
            border-radius: 10px;
            background-color: antiquewhite;
            height: 900px;
            /* height: 100%; */
        }
        th{
            color: white;
        }
        td, th{
            word-wrap: break-word;
            min-width: 120px;
            max-width: 160px;
            text-align: center;
        }
        .alert{
            text-align: center;
        }
        .modal-backdrop {
            height: 100%;
            width: 100%;
        }
    </style>

    <script type="text/javascript">
        function refreshDiv(){
            $('#content').load(location.href + ' #content')
        }
        setInterval(refreshDiv, 60000);

        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 3000); 
    </script>

    @endsection
@else
    @include('../layouts/notAuthorized')
@endif