@if(Auth::user()->hasRole('adminOperational'))
    @extends('../layouts.base')

    @section('title', 'Monitoring')

    @section('container')
    <div class="row">
        @include('adminOperational.sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <h1 class="mt-5 mb-3 text-center">Monitoring Shipment</h1>

            <div class="mb-3" style="margin-top: 10vh">
                <form action="" method="POST">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-2 mx-3">
                            <label class="font-weight-bold" for="">Search Tug</label>
                            <select class="custom-select" id="tugName">
                                <option disabled>Choose</option>
                                @forelse($tugs as $t)
                                    <option value="{{ $t -> tugName }}">{{ $t -> tugName }}</option>
                                @empty
                                    <option value="" disabled>No Tugs Available</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="form-group col-md-2 mx-3">
                            <label class="font-weight-bold" for="">Search Barge</label>
                            <select class="custom-select" id="bargeName">
                                <option disabled>Choose</option>
                                @forelse($barges as $b)
                                    <option value="{{ $b -> bargeName }}">{{ $b -> bargeName }}</option>
                                @empty
                                    <option value="" disabled>No Barges Available</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="form-group col-md-2 mx-3">
                            <label class="font-weight-bold" for="">From</label>
                            <select class="custom-select" id="from">
                                <option disabled>Choose</option>
                                @foreach($from as $f)
                                    <option value="{{ $f -> from }}">{{ $f -> from }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2 mx-3">
                            <label class="font-weight-bold" for="">To</label>
                            <select class="custom-select" id="to">
                                <option disabled>Choose</option>
                                @foreach($to as $t)
                                    <option value="{{ $t -> to }}">{{ $t -> to }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2 mx-3">
                            <label class="font-weight-bold" for="">Type of Task</label>
                            <select class="custom-select" id="taskType">
                                <option disabled>Choose</option>
                                <option value="Operational Shipment">Operational Shipment</option>
                                <option value="Operational Transhipment">Operational Transhipment</option>
                                <option value="Return Cargo">Return Cargo</option>
                                <option value="Non Operational">Non Operational</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-5">
                        <button type="button" class="btn btn-primary" onclick="search()">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="d-flex justify-content-center">
                <div class="spinner-border spinner-border-lg text-danger mt-2" role="status" id="wait">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <div class="jumbotron" id="table_data">
                @include('adminOperational.adminOperationalJumbotronMonitoring')
            </div>

        </main>
    </div>

    <script type="text/javascript">
        let spinner = document.getElementById("wait");
        spinner.style.visibility = 'hidden';

        function search(){
            event.preventDefault();
            let tugName = document.getElementById('tugName').value;
            let bargeName = document.getElementById('bargeName').value;
            let from = document.getElementById('from').value;
            let to = document.getElementById('to').value;
            let taskType = document.getElementById('taskType').value;
            
            let _token = $('input[name=_token]').val();

            $.ajax({
                url: "{{ route('adminOperational.searchMonitoring') }}",
                method: "POST",
                data: {
                    _token,
                    tugName,
                    bargeName,
                    from,
                    to,
                    taskType
                },
                beforeSend: function(){
                    $('#table_data').hide();
                    spinner.style.visibility = 'visible';
                },
                success: function(data){
                    $('#table_data').html(data);
                    $('#table_data').show();
                    spinner.style.visibility = 'hidden';
                }
            })
        }
    </script>

    <style>
        th{
            color: white;
        }
        td, th{
            word-wrap: break-word;
            min-width: 120px;
            max-width: 120px;
            text-align: center;
        }
        .alert{
            text-align: center;
        }
        .modal-backdrop {
            height: 100%;
            width: 100%;
        }
        .card-styling{
            width: 17rem;
            height: 12vh;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            border: 5px solid #555555;
        }
        .df-clock{
            width: 10%;
            height: 10%;
        }
    </style>

    {{-- <script type="text/javascript">
        function refreshDiv(){
            $('#content').load(location.href + ' #content')
        }
        setInterval(refreshDiv, 60000);

        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 3000); 
    </script> --}}

    @endsection
@else
    @include('../layouts/notAuthorized')
@endif