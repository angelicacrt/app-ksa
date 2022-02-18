@if(Auth::user()->hasRole('supervisorLogistic') || Auth::user()->hasRole('supervisorLogisticMaster'))
    @extends('../layouts.base')

    @section('title', 'Supervisor Dashboard')

    @section('container')
        <div class="row">
        @include('supervisor.sidebar')

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4" style="padding-bottom: 200px">
            @include('../layouts/time')
            
            <h2 class="mt-3 mb-2" style="text-align: center">Job Request List Cabang {{ Auth::user()->cabang }}</h2>

            <div class="d-flex justify-content-end mr-3">
                {{ $JobRequestHeads->links() }}
            </div>

            <br>

            <div class="d-flex mb-3">
                <form class="mr-auto w-50" action="">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search by Order ID or Status..." name="search" id="search">
                        <button class="btn btn-primary" type="submit" value="{{ request('search') }}">Search</button>
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
                                @elseif(strpos($jr -> status, 'Job Request Completed') !== false)
                                    <td style="color: green; font-weight: bold">{{ $jr -> status}}</td>
                                @elseif($jr -> status == 'Job Request In Progress By Logistics')
                                    <td style="color: blue; font-weight: bold">{{ $jr -> status}}</td>
                                @else
                                    <td>{{ $jr -> status}}</td>
                                @endif
                                
                                @if(strpos($jr -> status, 'Rejected') !== false)
                                    <td style="word-wrap: break-word;min-width: 250px;max-width: 250px; text-transform: uppercase;" ><strong>{{ $jr -> reason}}</td>
                                    <td>
                                        <button type="button" class="btn btn-info" data-toggle="modal" id="detail" data-target="#editJob-{{ $jr -> id }}">
                                            Detail
                                        </button>
                                    </td>
                                @elseif($jr -> status == 'Job Request In Progress By Logistics')
                                    <td>
                                        <button type="button" class="btn btn-info" data-toggle="modal" id="detail" data-target="#editJob-{{ $jr -> id }}">
                                            Detail
                                        </button>
                                    </td>
                                @elseif($jr -> status == 'Job Request Completed')
                                    <td style="word-wrap: break-word;min-width: 250px;max-width: 250px;">This Job Request Has been Completed</td>
                                    <td>
                                        <button type="button" class="btn btn-info" data-toggle="modal" id="detail" data-target="#editJob-{{ $jr -> id }}">
                                            Detail
                                        </button>
                                    </td>
                                @else
                                    <td style="word-wrap: break-word;min-width: 250px;max-width: 250px;">This Job Request is On progress by Purchasing</td>
                                    <td>
                                        <button type="button" class="btn btn-info" data-toggle="modal" id="detail" data-target="#editJob-{{ $jr -> id }}">
                                            Detail
                                        </button>
                                    </td>
                                @endif
                                
                                
                               
                            </tr>
                            @empty
                            @endforelse
                    </tbody>
                </table>

            </div>

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
            </main>
        </div>

        <style>
            th{
                color: white;
            }
            td, th{
                word-wrap: break-word;
                min-width: 150px;
                max-width: 150px;
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