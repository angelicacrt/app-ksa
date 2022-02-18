@if(Auth::user()->hasRole('adminOperational'))
    @extends('../layouts.base')

    @section('title', 'Daily Reports')

    @section('container')
    <div class="row">
        @include('adminOperational.sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <h1 class="mt-5 mb-3 text-center">Daily Report</h1>

            <div style="margin-top: 10vh">
                <form action="" method="POST">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-2 mx-3">
                            <label class="text-danger font-weight-bold" for="">Search Tug</label>
                            <select class="custom-select" id="tugName">
                                <option disabled>Choose</option>
                                @foreach($tugs as $t)
                                    <option value="{{ $t -> tugName }}">{{ $t -> tugName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2 mx-3">
                            <label class="text-danger font-weight-bold" for="">Search Barge</label>
                            <select class="custom-select" id="bargeName">
                                <option disabled>Choose</option>
                                <option value="">None</option>
                                @foreach($barges as $b)
                                    <option value="{{ $b -> bargeName }}">{{ $b -> bargeName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2 mx-3">
                            <label class="text-danger font-weight-bold" for="">Type Of Task</label>
                            <select class="custom-select" id="taskType">
                                <option disabled>Choose</option>
                                <option value="Operational Shipment">Operational Shipment</option>
                                <option value="Operational Transhipment">Operational Transhipment / Return Cargo</option>
                                <option value="Non Operational">Non Operational</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2 mx-3">
                            <label class="text-danger font-weight-bold" for="">Month</label>
                            <select class="custom-select" id="month">
                                <option disabled>Choose</option>
                                <option value="01">{{ 'January' }}</option>
                                <option value="02">{{ 'February' }}</option>
                                <option value="03">{{ 'March' }}</option>
                                <option value="04">{{ 'April' }}</option>
                                <option value="05">{{ 'May' }}</option>
                                <option value="06">{{ 'June' }}</option>
                                <option value="07">{{ 'July' }}</option>
                                <option value="08">{{ 'August' }}</option>
                                <option value="09">{{ 'September' }}</option>
                                <option value="10">{{ 'October' }}</option>
                                <option value="11">{{ 'November' }}</option>
                                <option value="12">{{ 'Desember' }}</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2 mx-3">
                            <label class="text-danger font-weight-bold" for="">Year</label>
                            <select class="custom-select" id="year">
                                <option disabled>Choose</option>
                                <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                <option value="{{ date('Y') - 1 }}">{{ date('Y') - 1 }}</option>
                                <option value="{{ date('Y') - 2 }}">{{ date('Y') - 2 }}</option>
                                <option value="{{ date('Y') - 3 }}">{{ date('Y') - 3 }}</option>
                                <option value="{{ date('Y') - 4 }}">{{ date('Y') - 4 }}</option>
                                <option value="{{ date('Y') - 5 }}">{{ date('Y') - 5 }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-5">
                        <button type="submit" class="btn btn-lg btn-primary" onclick="search()">
                            Submit
                        </button>
                    </div>
                </form>
            </div>

            <div class="d-flex justify-content-center mt-2">
                <div class="spinner-border spinner-border-lg text-danger" role="status" id="wait">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>

            <div class="mt-3" id="table_data">
                @include('adminOperational.adminOperationalReportTranshipmentTable')
            </div>
        </main>
    </div>

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
    </style>

    <script type="text/javascript">
        let spinner = document.getElementById("wait");
        spinner.style.visibility = 'hidden';

        function search(){
            event.preventDefault();
            let tugName = document.getElementById('tugName').value;
            let bargeName = document.getElementById('bargeName').value;
            let taskType = document.getElementById('taskType').value;
            let month = document.getElementById('month').value;
            let year = document.getElementById('year').value;
            
            let _token = $('input[name=_token]').val();

            $.ajax({
                url: "{{ route('adminOperational.searchDailyReports') }}",
                method: "POST",
                data: {
                    _token,
                    tugName,
                    bargeName,
                    taskType,
                    month,
                    year
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
    @endsection
@else
    @include('../layouts/notAuthorized')
@endif