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

                    <table id="content" class="table" style="margin-top: 1%">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">No.</th>
                                <th scope="col">Nama File</th>
                                <th scope="col">Upload Time</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ( $Headclaim as $claims )
                            <tr>
                                <td class="table-info">{{$loop->index+1}}</td>
                                <td class="table-info">{{$claims->nama_file}}</td>
                                <td class="table-info">{{$claims->created_at}}</td>
                                <td class="table-info">
                                    <div class="form-row">
                                        <div class="col-md-auto">
                                            <form method="POST" action="/insurance/historyFormclaimdownload">
                                                @csrf
                                                    <input type="hidden" name ="file_id" value="{{$claims->id}}"/>
                                                    <input type="hidden" name ="file_name" value="{{$claims->nama_file}}"/>
                                                    <button class="btn btn-outline-success" id="downloadexcel">Download</button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
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
                    <script>
                        setTimeout(function(){
                        $("div.alert").remove();
                        }, 5000 ); // 5 secs
                    </script>
                    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
                    </form>
                </div>
            </div>
        </div>   
        </div>
    </div>
    </main>
</div>
<script type="text/javascript">
    function refreshDiv(){
        $('#content').load(location.href + ' #content')
    }
    setInterval(refreshDiv, 60000);
</script>
@endsection