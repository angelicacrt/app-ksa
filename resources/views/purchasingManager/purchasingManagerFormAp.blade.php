@if(Auth::user()->hasRole('purchasingManager'))

    @extends('../layouts.base')

    @section('title', 'Checklist AP')

    @section('container')
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script> 

        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <div class="row">
            @include('purchasingManager.sidebar')
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                @include('../layouts/time')

                <h1 class="text-center">Checklist AP</h1>
                <div class="d-flex">
                    <div class="p-2 mr-auto">
                        <h5>Cabang</h5>
                        <select name="cabang" class="form-select" onchange="window.location = this.value;">
                            <option selected disabled>Pilih Cabang</option>
                            <option value="/purchasing-manager/form-ap/Jakarta" 
                                @php
                                    if($default_branch == 'Jakarta'){
                                        echo('selected');
                                    }
                                @endphp
                            >Jakarta</option>
                            <option value="/purchasing-manager/form-ap/Banjarmasin"
                                @php
                                    if($default_branch == 'Banjarmasin'){
                                        echo('selected');
                                    }
                                @endphp
                            >Banjarmasin</option>
                            <option value="/purchasing-manager/form-ap/Samarinda"
                                @php
                                    if($default_branch == 'Samarinda'){
                                        echo('selected');
                                    }
                                @endphp
                            >Samarinda</option>
                            <option value="/purchasing-manager/form-ap/Bunati"
                                @php
                                    if($default_branch == 'Bunati'){
                                        echo('selected');
                                    }
                                @endphp
                            >Bunati</option>
                            <option value="/purchasing-manager/form-ap/Babelan"
                                @php
                                    if($default_branch == 'Babelan'){
                                        echo('selected');
                                    }
                                @endphp
                            >Babelan</option>
                            <option value="/purchasing-manager/form-ap/Berau"
                                @php
                                    if($default_branch == 'Berau'){
                                        echo('selected');
                                    }
                                @endphp
                            >Berau</option>
                            <option value="/purchasing-manager/form-ap/Kendari"
                                @php
                                    if($default_branch == 'Kendari'){
                                        echo('selected');
                                    }
                                @endphp
                            >Kendari</option>
                            <option value="/purchasing-manager/form-ap/Morosi"
                                @php
                                    if($default_branch == 'Morosi'){
                                        echo('selected');
                                    }
                                @endphp
                            >Morosi</option>
                        </select>
                    </div>
                </div>
                
                @error('reason')
                    <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                        Alasan Invalid & Maksimal 180 Karakter
                    </div>
                @enderror

                <div class="d-flex justify-content-between my-3">
                    <div class="spinner-border spinner-border-lg text-danger ml-3" role="status" id="wait">
                        <span class="sr-only">Loading...</span>
                    </div>

                    <button class="mr-3" type="button" onclick="refresh()">
                        <span data-feather="refresh-ccw"></span>
                    </button>
                </div>

                <div id="content" style="overflow-x:auto;">
                    @include('purchasingManager.purchasingManagerFormApComponent')
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
                min-width: 80px;
                max-width: 80px;
                text-align: center;
            }
            .table-properties{
                word-wrap: break-word;
                min-width: 80px;
                max-width: 80px;
                text-align: center;
            }
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
            .alert{
                text-align: center;
            }
            .modal-backdrop {
                height: 100%;
                width: 100%;
            }
            .btn_download{
                background: none;
                border: none;
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

                let url = "{{ route('purchasingManager.purchasingManagerRefreshFormAp') }}";
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

        <script src="https://www.kryogenix.org/code/browser/sorttable/sorttable.js"></script>
    @endsection

@else
    @include('../layouts/notAuthorized')
@endif