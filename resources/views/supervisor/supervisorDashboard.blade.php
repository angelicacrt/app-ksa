@if(Auth::user()->hasRole('supervisorLogistic') || Auth::user()->hasRole('supervisorLogisticMaster'))
    @extends('../layouts.base')

    @section('title', 'Supervisor Dashboard')

    @section('container')
        <div class="row">
        @include('supervisor.sidebar')

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4" style="padding-bottom: 200px">
            @include('../layouts/time')
            
            <h2 class="mt-3 mb-2" style="text-align: center">Order List Cabang {{ Auth::user()->cabang }}</h2>

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

            @error('reason')
            <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                Alasan Wajib Diisi
            </div>
            @enderror

            <div class="d-flex mb-3">
                <form class="mr-auto w-50" action="">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search by Order ID or Status..." name="search" id="search">
                        <button class="btn btn-primary" type="submit" value="{{ request('search') }}">Search</button>
                    </div>
                </form>
                <div>
                    <button class="mr-3" type="button" onclick="refresh()">
                        <span data-feather="refresh-ccw"></span>
                    </button>
                    <a href="{{ Route('supervisor.completed-order') }}" class="btn btn-success mr-3">Completed ({{  $completed }})</a>
                    <a href="{{ Route('supervisor.in-progress-order') }}" class="btn btn-danger mr-3">In Progress ({{ $in_progress }})</a>
                </div>
            </div>

            <div class="spinner-border spinner-border-lg text-danger" role="status" id="wait">
                <span class="sr-only">Loading...</span>
            </div>

            <div id="content" style="overflow-x:auto;">
                @include('supervisor.supervisorDashboardComponent')
            </div>

            <div class="d-flex justify-content-end mr-3">
                {{ $orderHeads->links() }}
            </div>

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
            setTimeout(function() {
                $('.alert').fadeOut('fast');
            }, 3000); 
        </script>

        <script type="text/javascript">
            let spinner = document.getElementById("wait");
            spinner.style.visibility = 'hidden';

            function refresh(){
                event.preventDefault();

                let url = '';
                let searchData = document.getElementById('search').value;
                let _token = $('input[name=_token]').val();

                if(window.location.pathname == '/dashboard'){
                    url = "{{ route('supervisor.supervisorRefreshDashboard') }}";
                }else if(window.location.pathname == '/supervisor/completed-order'){
                    url = "{{ route('supervisor.supervisorRefreshDashboardCompleted') }}"
                }else{
                    url = "{{ route('supervisor.supervisorRefreshDashboardInProgress') }}"
                }

                $.ajax({
                    url: url,
                    method: "POST",
                    data: {
                        _token,
                        searchData
                    },
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