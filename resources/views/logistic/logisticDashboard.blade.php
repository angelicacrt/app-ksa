@if(Auth::user()->hasRole('logistic'))
    @extends('../layouts.base')

    @section('title', 'Logistic Dashboard')

    @section('container')
    <div class="row">
        @include('logistic.sidebar')
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4" style="padding-bottom: 30px">
            
            @include('../layouts/time')
            <div class="wrapper">
            <h2 class="mt-3 mb-2" style="text-align: center">Order List Cabang {{ Auth::user()->cabang }}</h2>

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
            <div class="d-flex mb-3">
                <form class="mr-auto w-50" action="">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search by Order ID or Status..." name="search" id="search" value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                </form>
                <div>
                    <button class="mr-3" type="button" onclick="refresh()">
                        <span data-feather="refresh-ccw"></span>
                    </button>
                    <a href="{{ Route('logistic.completed-order') }}" class="btn btn-success mr-3">Completed ({{  $completed }})</a>
                    <a href="{{ Route('logistic.in-progress-order') }}" class="btn btn-danger mr-3">In Progress ({{ $in_progress }})</a>
                </div>
            </div>

            <div class="spinner-border spinner-border-lg text-danger" role="status" id="wait">
                <span class="sr-only">Loading...</span>
            </div>

            <div id="content" style="overflow-x:auto;">
                @csrf
                @include('logistic.logisticDashboardComponent')
            </div>
            
            <div class="d-flex justify-content-end">
                {{ $orderHeads->links() }}
            </div>

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
                url = "{{ route('logistic.logisticRefreshDashboard') }}";
            }else if(window.location.pathname == '/logistic/completed-order'){
                url = "{{ route('logistic.logisticRefreshDashboardCompleted') }}"
            }else{
                url = "{{ route('logistic.logisticRefreshDashboardInProgress') }}"
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

    <script type="text/javascript">
        function trim_text(el) {
            el.value = el.value.
            replace(/(^\s*)|(\s*$)/gi, ""). // removes leading and trailing spaces
            replace(/[ ]{2,}/gi, " "). // replaces multiple spaces with one space
            replace(/\n +/, "\n"); // Removes spaces after newlines
            return;
        }
        $(function(){
            $("textarea").change(function() {
                trim_text(this);
            });
            $("input").change(function() {
                trim_text(this);
            });
        }); 
    </script>

    @endsection
@else
    @include('../layouts/notAuthorized')
@endif