@if(Auth::user()->hasRole('adminPurchasing'))

    @extends('../layouts.base')

    @section('title', 'Checklist AP')

    @section('container')
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script> 

        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <div class="row">
            @include('adminPurchasing.sidebar')
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                @include('../layouts/time')
                <h1 class="text-center">Upload List AP</h1>
                
                @if(session('status'))
                    <div class="alert alert-success" style="width: 40%; margin-left: 30%">
                        {{ session('status') }}
                    </div>
                @endif

                @if(session('fail'))
                    <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                        {{ session('fail') }}
                    </div>
                @endif

                @if(count($errors) > 0)
                    @foreach($errors->all() as $message)
                        <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                            {{ $message }}
                        </div>
                    @endforeach
                @endif

                <div class="d-flex">
                    <div class="p-2 mr-auto">
                        <h5>Cabang</h5>
                        <select name="cabang" class="form-select" onchange="window.location = this.value;">
                            <option selected disabled>Pilih Cabang</option>
                            <option value="/admin-purchasing/form-ap/Jakarta" 
                                @php
                                    if($default_branch == 'Jakarta'){
                                        echo('selected');
                                    }
                                @endphp
                            >Jakarta</option>
                            <option value="/admin-purchasing/form-ap/Banjarmasin"
                                @php
                                    if($default_branch == 'Banjarmasin'){
                                        echo('selected');
                                    }
                                @endphp
                            >Banjarmasin</option>
                            <option value="/admin-purchasing/form-ap/Samarinda"
                                @php
                                    if($default_branch == 'Samarinda'){
                                        echo('selected');
                                    }
                                @endphp
                            >Samarinda</option>
                            <option value="/admin-purchasing/form-ap/Bunati"
                                @php
                                    if($default_branch == 'Bunati'){
                                        echo('selected');
                                    }
                                @endphp
                            >Bunati</option>
                            <option value="/admin-purchasing/form-ap/Babelan"
                                @php
                                    if($default_branch == 'Babelan'){
                                        echo('selected');
                                    }
                                @endphp
                            >Babelan</option>
                            <option value="/admin-purchasing/form-ap/Berau"
                                @php
                                    if($default_branch == 'Berau'){
                                        echo('selected');
                                    }
                                @endphp
                            >Berau</option>
                        </select>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between my-3">
                    <div class="spinner-border spinner-border-lg text-danger ml-3" role="status" id="wait">
                        <span class="sr-only">Loading...</span>
                    </div>

                    <button class="mr-3" type="button" onclick="refresh()">
                        <span data-feather="refresh-ccw"></span>
                    </button>
                </div>

                <div class="content" id="content" style="overflow-x:auto;">
                    @csrf
                    @include('adminPurchasing.adminPurchasingFormApComponent')
                </div>

                <div class="d-flex justify-content-end">
                    {{ $apList->links() }}
                </div>
            </main>
        </div>

        <style>
            th{
                color: white;
            }
            th, td{
                word-wrap: break-word;
                min-width: 100px;
                max-width: 100px;
                text-align: center;
            }
            .table-properties{
                word-wrap: break-word;
                min-width: 120px;
                max-width: 120px;
                text-align: center;
            }
            /* .myTable tr td:last-child{
                width: 300px;
            } */
            .table-modal{
                height: 400px;
                overflow-y: auto;
            }
            .table-header{
                position: sticky;
                top: 0;
                z-index: 10;
            }
            .icon{
                color: black;
                height: 24px;
                width: 24px
            }
            .center{
                margin-left: 25%;
                width: 50%;
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
                // $('div.alert').remove();
            }, 3000);
        </script>

        <script type="text/javascript">
            let spinner = document.getElementById("wait");
            spinner.style.visibility = 'hidden';

            function refresh(){
                event.preventDefault();

                let url = "{{ route('adminPurchasing.adminPurchasingRefreshFormAp') }}";
                let _token = $('input[name=_token]').val();
                let default_branch = '{{ $default_branch }}';

                $.ajax({
                    url: url,
                    method: "POST",
                    data: {
                        _token,
                        default_branch
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