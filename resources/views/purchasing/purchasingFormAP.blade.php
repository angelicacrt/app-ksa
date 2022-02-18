@if(Auth::user()->hasRole('purchasing'))
    @extends('../layouts.base')

    @section('title', 'Purchasing Form AP')

    @section('container')
    <div class="row">
        @include('purchasing.sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4" style="padding-bottom: 150px">
            <h2 class="mt-5 mb-3" style="text-align: center">Form AP List</h2>

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

            @error('description')
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    Dekripsi Wajib Diisi
                </div>
            @enderror

            <div id="content">
                <div class="table-wrapper-scroll-y my-custom-scrollbar tableFixHead mt-5" style="overflow-x:auto;">
                    <table class="table table-bordered sortable">
                        <thead class="thead bg-danger">
                        <tr>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Status</th>
                            <th scope="col">Nama File</th>
                            <th scope="col">Keterangan</th>
                            <th scope="col">Action</th>
                            <th scope="col">Approval</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $doc)
                            <tr>
                                <td>{{ $doc -> submissionTime }}</td>
                                @if(strpos($doc -> status, 'Denied') !== false)
                                    <td><span style="color: red;font-weight: bold;">{{ $doc -> status }}</span></td>
                                @elseif(strpos($doc -> status, 'Approved') !== false)
                                    <td><span style="color: green;font-weight: bold;">{{ $doc -> status }}</span></td>
                                @else
                                    <td>{{ $doc -> status }}</td>
                                @endif
                                <td>{{ $doc -> filename }}</td>
                                <td>{{ $doc -> description }}</td>
                                <td><a href="/purchasing/form-ap/{{ $doc -> id }}/download" target="_blank"><span class="icon" data-feather="download"></span></a></td>
                                @if(strpos($doc -> status, 'Denied') !== false || strpos($doc -> status, 'Approved') !== false)
                                    <td></td>
                                @else
                                    <td>
                                        <a href="/purchasing/form-ap/{{ $doc -> id }}/approve" class="btn btn-success">Accept</a>
                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#reject-form-{{ $doc -> id }}">Reject</button>
                                    </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    {{-- Modal 1 --}}
    @foreach($documents as $doc)
        <div class="modal fade" id="reject-form-{{ $doc -> id }}" tabindex="-1" role="dialog" aria-labelledby="reject-formTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="rejectTitle" style="color: white">Reject</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="/purchasing/form-ap/{{ $doc -> id }}/reject">
                    @csrf
                    <div class="modal-body"> 
                        <label for="description">Alasan</label>
                        <textarea class="form-control" name="description" id="description" rows="3" placeholder="Input Alasan Reject Form"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    @endforeach

    <script>
        function refreshDiv(){
            $('#content').load(location.href + ' #content')
        }

        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 3000);
    </script>
        
    <style>
        .tableFixHead          { overflow: auto; height: 250px; }
        .tableFixHead thead th { position: sticky; top: 0; z-index: 1; }

        .my-custom-scrollbar {
            position: relative;
            height: 700px;
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
            min-width: 100px;
            max-width: 100px;
            text-align: center;
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
    </style>
    <script src="https://www.kryogenix.org/code/browser/sorttable/sorttable.js"></script>

    @endsection
@else
    @include('../layouts/notAuthorized')
@endif