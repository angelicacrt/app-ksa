@if(Auth::user()->hasRole('supervisorLogistic') || Auth::user()->hasRole('supervisorLogisticMaster'))
    @extends('../layouts.base')

    @section('title', 'Supervisor Approval DO')

    @section('container')
        <div class="row">
            @include('supervisor.sidebar')
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 mt-3">
                    <h1 class="d-flex justify-content-center mb-3">Approval DO Site</h1>
                    <br>
                    
                    @if(session('status'))
                        <div class="alert alert-success" style="width: 40%; margin-left: 30%">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="d-flex mb-3">
                        <div class="ml-auto">
                            <button class="mr-3" type="button" onclick="refresh()">
                                <span data-feather="refresh-ccw"></span>
                            </button>
                        </div>
                    </div>

                    <div class="spinner-border spinner-border-lg text-danger" role="status" id="wait">
                        <span class="sr-only">Loading...</span>
                    </div>

                    <div id="content">
                        @include('supervisor.supervisorApprovalDOContent')
                    </div>

                    <div class="d-flex justify-content-end">
                        {{ $ongoingOrders->links() }}
                    </div>

                </div>
            </main>
        </div>

        <style>
            th{
                color: white;
            }
            td, th{
                word-wrap: break-word;
                min-width: 80px;
                max-width: 80px;
                text-align: center;
            }
            .icon{
                margin-bottom: -10px;
                color: black;
                height: 34px;
                width: 34px;
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
            setTimeout(function() {
                $('.alert').fadeOut('fast');
            }, 3000);
        </script>

        <script type="text/javascript">
            let spinner = document.getElementById("wait");
            spinner.style.visibility = 'hidden';

            function refresh(){
                event.preventDefault();

                let url = "{{ route('supervisor.supervisorRefreshApprovalDO') }}";

                $.ajax({
                    url: url,
                    method: "GET",
                    beforeSend: function(){
                        $('#content').hide();
                        spinner.style.visibility = 'visible';
                    },
                    success: function(data){
                        $('#content').html(data);
                        $('#content').show();
                        spinner.style.visibility = 'hidden';
                    }
                })
            }
        </script>
    @endsection
@else
    @include('../layouts/notAuthorized')
@endif