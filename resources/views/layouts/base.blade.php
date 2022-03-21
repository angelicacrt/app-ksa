<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    {{-- <meta name="viewport" content="width=device-width, initial-scale=0.1"> --}}
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- <meta name="viewport" content="width=1024"> --}}
    <title>@yield('title')</title>

    <!-- Bootstrap core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- Custom styles for this template -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">

    <link href="/css/dashboard.css" rel="stylesheet">
</head>

<body onload="startTime();">
    @php
        $cabang_arr = [
            'Jakarta' => 'JKT',
            'Banjarmasin' => 'BNJ',
            'Samarinda' => 'SMD',
            'Bunati' => 'BNT',
            'Babelan' => 'BBL',
            'Berau' => 'BER' ,
            'Bunati' => 'BUN' ,
            'Kendari' => 'KDR' ,
            'Morosi' => 'MRS' ,
            'Batu Licin' => 'BTL' ,
        ];
    @endphp
    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="/dashboard" style="font-weight: bold; font-size: 14px;">PT. KSA - {{ Auth::user()->roles->first()->display_name}} - {{ $cabang_arr[Auth::user()->cabang] }}</a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        @if(Session::has('message'))
            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
        @endif
        @if(Auth::user()->hasRole('logistic') || Auth::user()->hasRole('supervisorLogistic') || Auth::user()->hasRole('supervisorLogisticMaster'))
            <div class="navbar-nav ml-auto mr-3">
                <div class="nav-item text-nowrap">
                    <button class="text-white bell-button" type="button" data-toggle="modal" data-target="#itemBelowStockModal"><span data-feather='bell'></span></button>
                </div>
            </div>
        @endif
        {{-- navbar logut and help --}}
        {{-- <div class="navbar-nav">
        </div>
        <div class="nav-item text-nowrap">
        </div> --}}
        <div class="btn-group dropleft">
            <button class="btn btn-dark dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Menu
            </button>
            <div class="dropdown-menu bg-dark" aria-labelledby="dropdownMenu2">
                <a  class="nav-link px-20 border-0" style="color:white" href="https://drive.google.com/drive/folders/1echqZO5WURy25x3CD0Aj19Kt6Hnrq8lj?usp=sharing" target="_blank" >
                    <i class="bi bi-box-arrow-right"></i>
                    <span data-feather="help-circle"></span>
                    Help   
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="color:white" class="nav-link bg-dark px-20 border-0"><i
                            class="bi bi-box-arrow-right"></i>
                            <span data-feather="log-out"></span>
                            Logout</button>
                </form>
                {{-- <button class="dropdown-item" type="button">Something else here</button> --}}
            </div>
        </div>
    </header>

    <div class="container-fluid" id="testing">
        <script>
            document.body.style.zoom = "88%";
            // document.body.style.zoom = screen.logicalXDPI;
        </script>
        @yield('container')
    </div>

    @if(Auth::user()->hasRole('logistic') || Auth::user()->hasRole('supervisorLogistic') || Auth::user()->hasRole('supervisorLogisticMaster'))
        <div class="modal fade" id="itemBelowStockModal" tabindex="-1" role="dialog" aria-labelledby="reject-orderTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title" style="color: white" id="rejectTitle">Notification on Stocks</h5>
                        {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button> --}}
                    </div>
                    <div class="refreshItemBelowStock">
                        @forelse($items_below_stock as $i)
                            <div class="card bg-light my-2 mx-2">
                                <div class="card-body">
                                    <div class="d-flex justify-content-around">
                                        <span class="feather-icon" data-feather="file"></span>
                                        <div class="mr-5">
                                            <h5 class="card-title font-weight-bold text-center">{{ $i -> itemName }}</h5>
                                            <p class="card-text mt-3"><span class="text-danger font-weight-bold">Warning !</span> stock is below {{ $i -> stock_defficiency }} ! please check immediately ! <span class="font-weight-bold">({{ $i->created_at->format('d-m-Y') }})</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <h5>No Items Found</h5>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif

</body>

<style>
    body {
        margin: 0;
        padding: 0;
        /* font-family: 'Inter', sans-serif; */
    }
    .bell-button{
        background: none;
        border: none;
        height: 90%;
        width: 90%;
    }
    .card-text{
        font-size: 18px;
    }
    .feather-icon{
        width: 10%;
        height: 10%;
    }
    @media (min-width: 300px) and (max-width: 767px){
        body {
            height:90vh;
        }
        .med-query{
            color: white;
            width: 150px;
            word-break: break-all;
            font-size: 16px;
        }
    }
    @media (min-width: 768px) and (max-width: 1024px){
        body {
            height:120vh;
        }
    }
    @media (min-width: 1025px) and (max-width: 1280px) {
        body {
            height:130vh;
        }
    }
</style>

<script>
    // function refreshDiv(){
    //     $('#refreshItemBelowStock').load(location.href + ' #refreshItemBelowStock')
    // }
    // setInterval(refreshDiv, 60000);

    setInterval(() => {
        for(i = 0 ; i <= id - 1 ; i++){
            $('.refreshItemBelowStock' + i).empty()
            $('.refreshItemBelowStock' + i).load(location.href + ' .refreshItemBelowStock' + i)
        }
    }, 10000)

</script>

<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js"
    integrity="sha384-uO3SXW5IuS1ZpFPKugNNWqTZRRglnUJK6UAZ/gxOX80nxEkN9NcGZTftn6RzhGWE" crossorigin="anonymous">
</script>
<script src="/js/dashboard.js"></script>
{{-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
    integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
</script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
    integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
</script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
</script>

</html>
