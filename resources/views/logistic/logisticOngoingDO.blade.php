@if(Auth::user()->hasRole('logistic'))
    @extends('../layouts.base')

    @section('title', 'Logistic Request DO')

    @section('container')
        <div class="row">
            @include('logistic.sidebar')
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 mt-3 wrapper">
                    <h1 class="d-flex justify-content-center mb-3">My Request DO</h1>
                    <br>
                    
                    @if(session('success'))
                        <div class="alert alert-success" style="width: 40%; margin-left: 30%">
                            {{ session('success') }}
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
                        @include('logistic.logisticOngoingDOComponent')
                    </div>

                    <div class="d-flex justify-content-end">
                        {{ $ongoingOrders->links() }}
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
                padding: 10px;
                border-radius: 10px;
                background-color: antiquewhite;
                height: 1000px;
                /* height: 100%; */
            }
            th{
                color: white;
            }
            td, th{
                word-wrap: break-word;
                min-width: 120px;
                max-width: 120px;
                text-align: center;
            }
            .icon{
                margin-bottom: -10px;
                color: black;
                height: 30px;
                width: 30px;
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

                let url = "{{ route('logistic.logisticRefreshOngoingDOPage') }}";

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