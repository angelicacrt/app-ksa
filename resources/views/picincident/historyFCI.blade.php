@extends('../layouts.base')

@section('title', 'PicIncident-history-FCI')

@section('container')
<x-guest-layout>
<div class="row">
    @include('picincident.sidebarincident')
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
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

                    <table class="table" style="margin-top: 1%">
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
                                            <form method="POST" action="/picincident/formclaimDownload">
                                                @csrf
                                                    <input type="hidden" name ="file_id" value="{{$claims->id}}"/>
                                                    <input type="hidden" name ="file_name" value="{{$claims->nama_file}}"/>
                                                    <button class="btn btn-outline-success" id="downloadexcel"><span class="text-center" data-feather="download" style="color: black"></span></button>
                                            </form>
                                        </div>
                                        <div class="col-md-auto">
                                            <form method="POST" action="/picincident/history/destroy/{{$claims->id}}">
                                                @csrf
                                                @method('delete')
                                                <button class="btn btn-outline-danger" type="submit" onClick="return confirm('Are you sure?')" style="font-size: 16px" id="deleteexcel">Delete</button>
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
</x-guest-layout>
@endsection