{{-- @if(Auth::user()->hasRole('purchasing')) --}}
    @extends('../layouts.base')

    @section('title', 'Purchasing Dashboard')

    @section('container')
    <div class="row">
        @include('purchasing.sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4" style="padding-bottom: 150px">
            <div class="d-flex">
                @include('../layouts/time')

                <div class="p-2 ml-auto mt-5">
                    <h5>Cabang</h5>
                    <select name="default_branch" class="form-select" onchange="window.location = this.value;">
                        <option selected disabled>Pilih Cabang</option>
                        <option value="/purchasing/Job_Request_List/Jakarta" 
                            @php
                                if($default_branch == 'Jakarta'){
                                    echo('selected');
                                }
                            @endphp
                        >Jakarta</option>
                        <option value="/purchasing/Job_Request_List/Banjarmasin"
                            @php
                                if($default_branch == 'Banjarmasin'){
                                    echo('selected');
                                }
                            @endphp
                        >Banjarmasin</option>
                        <option value="/purchasing/Job_Request_List/Samarinda"
                            @php
                                if($default_branch == 'Samarinda'){
                                    echo('selected');
                                }
                            @endphp
                        >Samarinda</option>
                        <option value="/purchasing/Job_Request_List/Bunati"
                            @php
                                if($default_branch == 'Bunati'){
                                    echo('selected');
                                }
                            @endphp
                        >Bunati</option>
                        <option value="/purchasing/Job_Request_List/Babelan"
                            @php
                                if($default_branch == 'Babelan'){
                                    echo('selected');
                                }
                            @endphp
                        >Babelan</option>
                        <option value="/purchasing/Job_Request_List/Berau"
                            @php
                                if($default_branch == 'Berau'){
                                    echo('selected');
                                }
                            @endphp
                        >Berau</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <h2 class="mt-3 mb-4" style="text-align: center;">Job Order List Cabang {{ $default_branch }}</h2>
                    
                    @if(session('success'))
                        <div class="alert alert-success" style="width: 40%; margin-left: 30%">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('failed'))
                        <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                            {{ session('failed') }}
                        </div>
                    @endif

                    @error('reasonbox')
                        <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                            Alasan Wajib Diisi
                        </div>
                    @enderror

                    <div class="d-flex mb-3">
                        <form class="mr-auto w-50" action="">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search by Order ID or Status..." value="{{ request('search') }}" name="search" id="search">
                                <button class="btn btn-primary" type="submit">Search</button>
                            </div>
                        </form>
                        <div>
                            <a style="color: white" class="btn btn-success mr-3">Completed ({{  $completed }})</a>
                            <a style="color: white" class="btn btn-danger mr-3">In Progress ({{ $in_progress }})</a>
                        </div>
                    </div>

                    <div id="content" style="overflow-x:auto;">
                        <table class="table">
                            <thead class="thead bg-danger">
                            <tr>
                                <th scope="col">Order/Job ID</th>
                                <th scope="col">Status</th>
                                <th scope="col">Description</th>
                                <th scope="col">Detail</th>
                            </tr>
                            </thead>
                            <tbody>                      
                            {{-- job dashboard Details --}}
                                @foreach ($JobRequestHeads as $jr )
                                    <tr>
                                        <td><strong>{{ $jr -> Headjasa_id}}</strong></td>
                                        @if(strpos($jr -> status, 'Job Request Approved By') !== false)
                                            <td style="color: green; font-weight: bold">{{ $jr -> status}}</td>
                                        @elseif(strpos($jr -> status, 'Job Request Rejected By') !== false)
                                            <td style="color: red; font-weight: bold">{{ $jr -> status}}</td>
                                        @elseif(strpos($jr -> status, 'Job Request Completed') !== false)
                                            <td style="color: orange; font-weight: bold">{{ $jr -> status}}</td>
                                        @endif
                                        
                                        @if(strpos($jr -> status, 'Rejected') !== false)
                                            <td style="word-wrap: break-word;min-width: 250px;max-width: 250px;">{{ $jr -> reason}}</td>
                                        @elseif (($jr -> status == 'Job Request Approved By Purchasing') !== false)
                                            <td style="word-wrap: break-word;min-width: 250px;max-width: 250px;">This Job Request has been Approved , Awaiting to be Finalized By purchasing</td>
                                        @elseif(strpos($jr -> status, 'Completed') !== false)
                                        <td style="word-wrap: break-word;min-width: 250px;max-width: 250px;">This Job Request has been finalized by purchasing</td>
                                        @else
                                            <td style="word-wrap: break-word;min-width: 250px;max-width: 250px;">This Job Request is Awaiting for Review</td>
                                        @endif

                                        @if($jr -> status == 'Job Request Approved By Logistics')
                                            <td>
                                            <!-- Button trigger modal -->
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalCenter-{{ $jr -> id }}">
                                                    Details
                                                </button>
                                            
                                            <!-- Modal -->
                                                <div class="modal fade " id="ModalCenter-{{ $jr -> id }}" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">Job Request Details</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <table class="center">
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
                                                            <div class="modal-footer">
                                                                <a class="btn btn-outline-success" href="/purchasing/Review-Job/{{$jr -> id }}">Approve Request</a>
                                                                
                                                                <button type="button" class="btn btn-outline-danger" data-toggle="modal" data-target="#rejectjob-{{ $jr -> id }}">Reject Request</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </td>
                                        @elseif ($jr -> status == 'Job Request Approved By Purchasing')
                                            <td>
                                                <button type="button" class="btn btn-outline-info" data-toggle="modal" data-target="#detailjob-{{ $jr -> id }}">Detail</button>
                                            </td>
                                        @elseif ($jr -> status == 'Job Request Completed')
                                            <td>
                                                <button type="button" class="btn btn-outline-info" data-toggle="modal" data-target="#detailjob-{{ $jr -> id }}">Detail</button>
                                            </td>
                                        @else
                                            <td>
                                                <button type="button" class="btn btn-outline-info" data-toggle="modal" id="detail" data-target="#editJob-{{ $jr -> id }}">
                                                    Detail
                                                </button>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach 
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    {{ $JobRequestHeads->links() }}
                </div>

            </div>

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
                            <form method="POST" action="/purchasing/Job_Request_Reject/{{$jr -> id}}">
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
            
            {{--modal for Job order  --}}
            @foreach($JobRequestHeads as $jr)
                <div class="modal fade" id="detailjob-{{ $jr->id }}" tabindex="-1" role="dialog" aria-labelledby="detailTitle"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-danger">
                                <div class="d-flex justify-content-around">
                                    <h5><span style="color: white">Order : {{ $jr -> JobRequestHeads }}</span></h5>
                                    <h5 class="ml-5"><span style="color: white">Processed By : {{ $jr -> check_by }}</span></h5>
                                    <h5 class="ml-5"><span style="color: white">Tipe Order : JOB Request</span></h5>
                                </div>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="d-flex justify-content-around mb-3">
                                    <h5>Nomor JR : {{ $jr -> noJr }}</h5>
                                    <h5>Nomor JO : {{ $jr -> JO_id }}</h5>
                                </div>
                                <table class="table">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th scope="col">Quantity</th>
                                            <th scope="col">Note</th>
                                            <th scope="col">Status Order</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($jobDetails as $od)
                                            @if($od -> jasa_id == $jr -> id)
                                                <tr>
                                                    <td>{{ $od -> note }}</td>
                                                    <td>{{ $od -> quantity }}</td>
                                                    <td>
                                                        @if(strpos($jr -> status , 'Approved') !== false)
                                                            <span style="color: green; font-weight: bold;">{{ $jr -> status }}</span>
                                                        @elseif(strpos($jr -> status , 'Completed') !== false)
                                                            <span style="color: green; font-weight: bold;">{{ $jr -> status }}</span>
                                                        @else
                                                            <span style="color: red; font-weight: bold;">{{ $jr -> status }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div> 
                            <div class="modal-footer">
                                @if(strpos($jr -> status , 'Completed') !== false)
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#downloadJO-{{ $jr -> id }}">
                                        Download JO
                                    </button>
                                    
                                @else
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#downloadJO-{{ $jr -> id }}">
                                        Download JO
                                    </button>
                                    <form method="POST" action="/purchasing/Job_Request_final/{{$jr -> id }}">
                                        @csrf
                                        <input type="hidden" name='jobhead_id' value= {{$jr->Headjasa_id}}>
                                        <input type="hidden" name='jobhead_name' value= {{$jr->created_by}}>
                                        <button type="submit" class="btn btn-warning">
                                            Finalize job
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Modal download-->
            @foreach ($JobRequestHeads as $jr)
                <div class="modal fade" id="downloadJO-{{ $jr->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Download JR : {{$jr -> Headjasa_id }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        </div>
                        <div class="modal-body">
                            <a href="/purchasing/{{ $jr -> id }}/download-Jo" style="color: white" class="btn btn-dark" target="_blank">Download JO As Excel</a>
                            <a href="/purchasing/{{ $jr -> id }}/download-JO_pdf" style="color: white" class="btn btn-dark" target="_blank">Download JO As PDF</a>
                        </div>
                    </div>
                    </div>
                </div>
            @endforeach
            
            {{-- modal for rejected job order --}}
            @foreach($JobRequestHeads as $jr)
                <div class="modal fade" id="editJob-{{ $jr -> id }}" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Job Request Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <table class="center">
                                <thead>
                                    <tr>
                                        <th class="bg-black" scope="col">Nama Tugboat / barge</th>
                                        <th class="bg-black" scope="col">Lokasi Perbaikan</th>
                                        <th class="bg-black" scope="col">description</th>
                                        <th class="bg-black" scope="col">quantity</th>
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
        th, td{
            word-wrap: break-word;
            min-width: 160px;
            max-width: 160px;
            text-align: center;
        }
        .center {
            margin-left: auto;
            margin-right: auto;
        }
        .fa-star{
            font-size: 20px;
        }
        .fa-star.checked{
            color: #ffe400;
        }
        .rating-css {
            color: #ffe400;
            font-size: 20px;
            font-family: sans-serif;
            font-weight: 800;
            text-align: center;
            text-transform: uppercase;
        }
        /* .rating-css input {
            display: none;
        } */
        .rating-css input + label {
            font-size: 20px;
            text-shadow: 1px 1px 0 #8f8420;
            cursor: pointer;
        }
        .rating-css input:checked + label ~ label {
            color: #b4afaf;
        }
        .rating-css label:active {
            transform: scale(0.8);
            transition: 0.3s ease;
        }
        .scrolling-wrapper{
            overflow-y: auto;
            max-height: 800px;
        }
        .card-block{
            background-color: #fff;
            background-position: center;
            background-size: cover;
            border-radius: 24px;
            transition: all 0.2s ease-in-out !important;
            &:hover{
                transform: translateY(-5px);
                box-shadow: none;
                opacity: 0.9;
            }
        }
        .alert{
            text-align: center;
        }
        .modal-backdrop {
            height: 100%;
            width: 100%;
        }
        @media (min-width: 300px) and (max-width: 768){
            .smaller-screen-size{
                width: 150px;
                word-break: break-all;
                font-size: 12px;
            }
            .fa-star{
                font-size: 14px;
            }
        }
    </style>

    <script type="text/javascript">
        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 3000);

        function myFunction() {
            var input, filter, cards, cardContainer, title, i;
            
            input = document.getElementById("myFilter");
            filter = input.value.toUpperCase();
            cardContainer = document.getElementById("mySupplier");
            cards = cardContainer.getElementsByClassName("card");
            
            for (i = 0; i < cards.length; i++) {
                title = cards[i].querySelector(".supplier-name");
                code = cards[i].querySelector(".supplier-code");
                pic = cards[i].querySelector(".supplier-pic");
                if (title.innerText.toUpperCase().indexOf(filter) > -1 || code.innerText.toUpperCase().indexOf(filter) > -1 || pic.innerText.toUpperCase().indexOf(filter) > -1) {
                    cards[i].style.display = "";
                } else {
                    cards[i].style.display = "none";
                }
            }
        }
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/fontawesome.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/all.min.css" />

    @endsection
{{-- @else
    @include('../layouts/notAuthorized')
@endif --}}