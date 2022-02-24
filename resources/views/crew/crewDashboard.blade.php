@if(Auth::user()->hasRole('crew'))
    @extends('../layouts.base')

    @section('title', 'Crew Dashboard')

    @section('container')
    <div class="row">
        @include('crew.sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            @include('../layouts/time')            

            <h1 class="mt-3 mb-3" style="text-align: center">Order List</h1>

            @if(session('status'))
                <div class="alert alert-success text-center" style="width: 40%; margin-left: 30%">
                    {{ session('status') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger text-center" style="width: 40%; margin-left: 30%">
                    {{ session('error') }}
                </div>
            @endif

            <div class="d-flex">
                <div class="p-2 mr-auto">
                    <h5>Cabang: {{ Auth::user()->cabang }}</h5>
                    <form action="{{ Route('crew.changeBranch') }}" method="POST">
                        @csrf
                        <div class="d-flex">
                            <select class="form-select mr-3" aria-label="Default select example" name="cabang" id="cabang">
                                <option value="Jakarta" id="Jakarta" 
                                    @php if(Auth::user()->cabang == 'Jakarta') {
                                        echo('selected');
                                    } 
                                    @endphp
                                >Jakarta</option>
                                <option value="Banjarmasin" id="Banjarmasin"
                                    @php if(Auth::user()->cabang == 'Banjarmasin') {
                                        echo('selected');
                                    } 
                                    @endphp
                                >Banjarmasin</option>
                                <option value="Samarinda" id="Samarinda"
                                    @php if(Auth::user()->cabang == 'Samarinda') {
                                        echo('selected');
                                    } 
                                    @endphp
                                >Samarinda</option>
                                <option value="Bunati" id="Bunati"
                                    @php if(Auth::user()->cabang == 'Bunati') {
                                        echo('selected');
                                    } 
                                    @endphp
                                >Bunati</option>
                                <option value="Babelan" id="Babelan"
                                    @php if(Auth::user()->cabang == 'Babelan') {
                                        echo('selected');
                                    } 
                                    @endphp
                                >Babelan</option>
                                <option value="Berau" id="Berau"
                                    @php if(Auth::user()->cabang == 'Berau') {
                                        echo('selected');
                                    } 
                                    @endphp
                                >Berau</option>
                                <option value="Kendari" id="Kendari"
                                    @php if(Auth::user()->cabang == 'Kendari') {
                                        echo('selected');
                                    } 
                                    @endphp
                                >Kendari</option>
                                <option value="Morosi" id="Morosi"
                                    @php if(Auth::user()->cabang == 'Morosi') {
                                        echo('selected');
                                    } 
                                    @endphp
                                >Morosi</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                        </div>
                    </form>
                </div>

                <div class="p-2 mt-auto">
                    <button class="mr-3" type="button" onclick="refresh()">
                        <span data-feather="refresh-ccw"></span>
                    </button>
                    <a href="{{ Route('crew.completed-order') }}" class="btn btn-success mr-3">Completed ({{  $completed }})</a>
                    <a href="{{ Route('crew.in-progress-order') }}" class="btn btn-danger mr-3">In Progress ({{ $in_progress }})</a>
                </div>
            </div>

            <div class="spinner-border spinner-border-lg text-danger" role="status" id="wait">
                <span class="sr-only">Loading...</span>
            </div>
            
            <div id="content" style="overflow-x:auto;">
                @include('crew.crewDashboardComponent')
            </div>
            
            <div class="p-2 page">
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
            if(window.location.pathname == '/dashboard'){
                url = "{{ route('crew.crewRefreshDashboard') }}";
            }else if(window.location.pathname == '/crew/completed-order'){
                url = "{{ route('crew.crewRefreshDashboardCompleted') }}"
            }else{
                url = "{{ route('crew.crewRefreshDashboardInProgress') }}"
            }
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