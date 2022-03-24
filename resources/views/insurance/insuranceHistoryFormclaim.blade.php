@extends('../layouts.base')

@section('title', 'insurance-history-FCI')

@section('container')
<div class="row">
    @include('insurance.insuranceSidebar')
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-10">
        <div class="col" style="margin-top: 15px">
            <div class="jumbotron jumbotron-fluid" >
                <div class="container">
                  
                  <div class="text-md-center">
                    <h4 class="display-4">History Form Claim</h4>
                </div>

                @if ($success = Session::get('success'))
                    <div class="center">
                        <div class="alert alert-success alert-block" id="message">
                            <strong>{{ $success }}</strong>
                        </div>
                    </div>
                @endif

                @if ($failed = Session::get('failed'))
                    <div class="center">
                        <div class="alert alert-danger alert-block" id="message">
                            <strong>{{ $failed }}</strong>
                        </div>
                    </div>
                @endif

                    <table id="content" class="table" style="margin-top: 1%">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center" scope="col">No.</th>
                                <th style="text-align: center" scope="col">Nama File</th>
                                <th style="text-align: center" scope="col">Upload Time</th>
                                <th style="text-align: center" scope="col">Status</th>
                                <th style="text-align: center"scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ( $Headclaim as $claims )
                            <tr>
                                @if ($claims->status == 'Approved')
                                    <td class="table-success">{{$loop->index+1}}</td>
                                    <td class="table-success">{{$claims->nama_file}}</td>
                                    <td class="table-success">{{$claims->created_at}}</td>
                                    <td class="table-info"><strong>{{$claims->status}}</strong></td>
                                    <td class="table-success">
                                        <div class="form-row">
                                            <div class="col-md-auto">
                                                <form method="POST" action="/insurance/historyFormclaimdownload">
                                                    @csrf
                                                        <input type="hidden" name ="file_id" value="{{$claims->id}}"/>
                                                        <input type="hidden" name ="file_name" value="{{$claims->nama_file}}"/>
                                                        <button class="btn btn-outline-dark" id="downloadexcel">Download</button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                @else
                                    <td class="table-warning">{{$loop->index+1}}</td>
                                    <td class="table-warning">{{$claims->nama_file}}</td>
                                    <td class="table-warning">{{$claims->created_at}}</td>
                                    <td class="table-info"><strong>{{$claims->status}}</strong></td>
                                    <td class="table-warning" >
                                        <div class="form-row">
                                            <div class="col-md-auto">
                                                <form method="POST" action="/insurance/historyFormclaimExport">
                                                    @csrf
                                                        <input type="hidden" name ="file_id" value="{{$claims->id}}"/>
                                                        <input type="hidden" name ="file_name" value="{{$claims->nama_file}}"/>
                                                        <button class="btn btn-outline-dark" id="downloadexcel">Download</button>
                                                </form>
                                            </div>
                                            <div class="col-md-auto">
                                                <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#approved_file_upload-{{$claims->id}}">Approve</button>
                                                <!-- Modal approve upload-->
                                                <div class="modal fade" id="approved_file_upload-{{$claims->id}}" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-dark">
                                                        <h5 class="modal-title" style="color: white" id="staticBackdropLabel">Upload Approved Form Claim</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form method="POST" enctype="multipart/form-data" action="/insurance/Approved_Formclaim_download">
                                                            @csrf
                                                            <div class="modal-body">
                                                                Ensure that the File is >= 3MB and it is a PDF/Excel file.
                                                                <br>
                                                                <div class="input-group mb-3">
                                                                    <input type="hidden" name ="file_id" value="{{$claims->id}}"/>
                                                                    <input type="hidden" name ="file_name" value="{{$claims->nama_file}}"/>
                                                                    <input type="file" placeholder="Upload The Approved FormClaim" class="form-control" name=FCI_file >
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">Upload</button>
                                                        </form>
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                            @empty
                                <tr>
                                    <td>
                                        No Form Claim Created Yet. 
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>   
</main>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script>
    setTimeout(function(){
    $("div.alert").remove();
    }, 5000 ); // 5 secs
</script>
<script type="text/javascript">
    function refreshDiv(){
        $('#content').load(location.href + ' #content')
    }
    setInterval(refreshDiv, 120000);
</script>
<style>
    td {
        text-align: center;
        font-size: 16px
    }
    .modal-backdrop {
          height: 100%;
          width: 100%;
      }
</style>
@endsection