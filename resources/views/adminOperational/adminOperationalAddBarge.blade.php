@if(Auth::user()->hasRole('adminOperational'))
    @extends('../layouts.base')

    @section('title', 'Add Barge')

    @section('container')
    <div class="row">
        @include('adminOperational.sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <h1 class="mt-5 mb-3 text-center">Barge List</h1>

            @if(session('status'))
                <div class="alert alert-success" style="width: 40%; margin-left: 30%">
                    {{ session('status') }}
                </div>
            @endif

            {{-- @error('bargeName')
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    Nama Barge Invalid
                </div>
            @enderror --}}

            @foreach ($errors->all() as $error)
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    {{ $error }}
                </div>
            @endforeach

            <div class="d-flex justify-content-around mb-3">
                <div class="input-group w-25">
                    @csrf
                    <input type="text" class="form-control" placeholder="Search Barge by name..." name="search" id="search" value="{{ old('search') }}">
                    <button class="btn btn-primary searchBtn" type="button" onclick="search()">Search</button>
                </div>
                <div class="mr-5">
                    <button class="btn btn-info" type="button" data-toggle="modal" data-target="#addBarge">Add</button>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <div class="spinner-border spinner-border-lg text-danger" role="status" id="wait">
                    <span class="sr-only">Loading...</span>
                </div>
                <div class="w-75" id="table_data">
                    @include('adminOperational.adminOperationalAddBargeTable')
                </div>
                {{ $barges -> withQueryString() -> links() }}
            </div>
        </main>

        <!-- Modal #1 -->
        <div class="modal fade" id="addBarge" tabindex="-1" role="dialog" aria-labelledby="addBarge"
            aria-hidden="true" data-backdrop="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title" id="addBargeTitle" style="color: white">Add New Barge</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="/admin-operational/add-newbarge">
                            @csrf
                            <div class="form-group">
                                <label for="bargeName">Nama Barge</label>
                                <input type="text" class="form-control" id="bargeName" name="bargeName"
                                    placeholder="Input Nama Barge" required>
                            </div>
                            <div class="form-group">
                                <label for="gt">GT</label>
                                <input type="text" class="form-control" id="gt" name="gt"
                                    placeholder="Input GT" required>
                            </div>
                            <div class="form-group">
                                <label for="nt">NT</label>
                                <input type="text" class="form-control" id="nt" name="nt"
                                    placeholder="Input NT" required>
                            </div>
                            <div class="form-group">
                                <label for="flag">Flag</label>
                                <input type="text" class="form-control" id="flag" name="flag"
                                    placeholder="Input Flag" required>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Add Item</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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
            align-items: center;
        }
        .alert{
            text-align: center;
        }
        .modal-backdrop {
            height: 100%;
            width: 100%;
        }
    </style>

    <script>
        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 3000); 
    </script>

    <script type="text/javascript">
        $(document).ready(function(){
            $(document).on('click', '.page-link', function(event){
                event.preventDefault();
                console.log('clicked');
                let page = $(this).attr('href').split('page=')[1];
                
                let _token = $('input[name=_token]').val();

                $.ajax({
                    url: "{{ route('adminOperational.paginationBarge') }}",
                    method: "POST",
                    data: {
                        _token,
                        page
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
                });
            });
        });
    </script>

    <script type="text/javascript">
        let spinner = document.getElementById("wait");
        spinner.style.visibility = 'hidden';

        function search(){
            event.preventDefault();
            let searchData = document.getElementById('search').value;

            let _token = $('input[name=_token]').val();

            $.ajax({
                url: "{{ route('adminOperational.searchBarge') }}",
                method: "POST",
                data: {
                    _token,
                    searchData
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

        // function refreshDiv(){
        //     $('#content').load(location.href + ' #content')
        // }
        // setInterval(refreshDiv, 60000);
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