@if(Auth::user()->hasRole('adminPurchasing'))
    @extends('../layouts.base')

    @section('title', 'Report AP')

    @section('container')
    <div class="row">
        @include('adminPurchasing.sidebar')
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            
            <div class="flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 mt-3">
                <h1 class="d-flex justify-content-center mb-4">Reports List AP Cabang {{ $default_branch }} ({{ $str_month }})</h1>
                
                @if(session('status'))
                    <div class="alert alert-success" style="width: 40%; margin-left: 30%">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="d-flex justify-content-between mr-3">
                    <div class="p-2">
                        <select name="cabang" class="form-select" onchange="window.location = this.value;">
                            <option selected disabled>Pilih Cabang</option>
                            <option value="/admin-purchasing/report-ap/Jakarta" 
                                @php
                                    if($default_branch == 'Jakarta'){
                                        echo('selected');
                                    }
                                @endphp
                            >Jakarta</option>
                            <option value="/admin-purchasing/report-ap/Banjarmasin"
                                @php
                                    if($default_branch == 'Banjarmasin'){
                                        echo('selected');
                                    }
                                @endphp
                            >Banjarmasin</option>
                            <option value="/admin-purchasing/report-ap/Samarinda"
                                @php
                                    if($default_branch == 'Samarinda'){
                                        echo('selected');
                                    }
                                @endphp
                            >Samarinda</option>
                            <option value="/admin-purchasing/report-ap/Bunati"
                                @php
                                    if($default_branch == 'Bunati'){
                                        echo('selected');
                                    }
                                @endphp
                            >Bunati</option>
                            <option value="/admin-purchasing/report-ap/Babelan"
                                @php
                                    if($default_branch == 'Babelan'){
                                        echo('selected');
                                    }
                                @endphp
                            >Babelan</option>
                            <option value="/admin-purchasing/report-ap/Berau"
                                @php
                                    if($default_branch == 'Berau'){
                                        echo('selected');
                                    }
                                @endphp
                            >Berau</option>
                        </select>
                    </div>

                    <div class="p-2 mr-auto">
                        <input type="text" id="myInput" class="form-control" placeholder="Search by Invoice or Supplier" name="search" style="width: 15vw">
                    </div>

                    @if(count($apList) > 0)
                        <div class="p-2">
                            <a href="/purchasing-manager/report-ap/{{ $default_branch }}/export" class="btn btn-outline-danger mb-3" target="_blank">Export</a>
                        </div>
                    @endif
                </div>

                <div class="d-flex justify-content-between my-3">
                    <div class="spinner-border spinner-border-lg text-danger ml-3" role="status" id="wait">
                        <span class="sr-only">Loading...</span>
                    </div>

                    <button class="mr-3" type="button" onclick="refresh()">
                        <span data-feather="refresh-ccw"></span>
                    </button>
                </div>

                <div id="content">
                    @include('adminPurchasing.adminPurchasingReportApPageComponent')
                </div>
            </div>
        </main>
    </div>

    <style>
        .tableFixHead          { overflow: auto; height: 250px; }
        .tableFixHead thead th { position: sticky; top: 0; z-index: 1; }

        .my-custom-scrollbar {
            position: relative;
            height: 900px;
            overflow: auto;
        }
        .table-wrapper-scroll-y {
            display: block;
        }
        th{
            color: white;
        }

        td, th{
            word-wrap: break-word;
            min-width: 160px;
            max-width: 160px;
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
        function filterTable(event) {
            var filter = event.target.value.toUpperCase();
            var rows = document.querySelector("#myTable tbody").rows;
            
            for (var i = 0; i < rows.length; i++) {
                var firstCol = rows[i].cells[0].textContent.toUpperCase();
                var secondCol = rows[i].cells[1].textContent.toUpperCase();
                if (firstCol.indexOf(filter) > -1 || secondCol.indexOf(filter) > -1) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }      
            }
        }

        document.querySelector('#myInput').addEventListener('keyup', filterTable, false);
    </script>

    <script type="text/javascript">
        let spinner = document.getElementById("wait");
        spinner.style.visibility = 'hidden';

        function refresh(){
            event.preventDefault();

            let url = "{{ route('adminPurchasing.adminPurchasingRefreshReportAp') }}";
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