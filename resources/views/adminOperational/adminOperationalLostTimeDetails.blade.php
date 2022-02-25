@if(Auth::user()->hasRole('adminOperational'))
    @extends('../layouts.base')

    @section('title', 'Lost Time Details')

    @section('container')
    <div class="row">
        @include('adminOperational.sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <h1 class="mt-5 mb-3 text-center">Lost Time Details</h1>

            <div class="d-flex justify-content-end mr-3">
                <a href="/dashboard" class="btn btn-primary">Back To Dashboard</a>
            </div>

            <div class="mt-3" id="content">
                <table class="table" id="myTable">
                    <thead class="thead bg-danger">
                        <tr>
                            <th scope="col">Nama Kapal</th>
                            <th scope="col">Status</th>
                            <th scope="col">Condition</th>
                            <th scope="col">Total Times</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($operationalData as $od)
                            <tr>
                                <td class="bg-white">{{ $od -> tugName }}/{{ $od -> bargeName }}</td>
                                <td class="bg-white">{{ $od -> taskType }}</td>
                                <td class="bg-white">{{ $od -> condition }}</td>
                                <td class="bg-white">{{ !empty($od -> totalLostDays) ? $od -> totalLostDays : 'n/a' }}</td>
                            </tr>
                        @empty
                            <h2>No Data Found.</h2>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-start">
                {{ $operationalData -> links() }}
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

    <script type="text/javascript">
        function refreshDiv(){
            $('#content').load(location.href + ' #content')
        }
        setInterval(refreshDiv, 60000);
    </script>

    @endsection
@else
    @include('../layouts/notAuthorized')
@endif