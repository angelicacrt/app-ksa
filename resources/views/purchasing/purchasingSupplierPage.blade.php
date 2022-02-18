@extends('../layouts.base')

@section('title', 'Supplier')

@section('container')
<div class="row">
    @include('purchasing.sidebar')
    
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        @include('../layouts/time')

        @if(session('status'))
            <div class="alert alert-success" style="width: 40%; margin-left: 30%">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">{{$error}}</div>
            @endforeach
        @endif

        <div class="d-flex justify-content-around">
            <div style="overflow-x: auto; width: 40%">
                <h2 class="mb-4" style="text-align: center">Contact Suppliers</h2>
                <input type="text" id="myFilter" class="form-control my-3 w-50" onkeyup="myFunction()" placeholder="Search for supplier...">
                <div class="flex-column flex-nowrap scrolling-wrapper" id="mySupplier">
                    @if(count($suppliers) == 0)
                        <h5>No Data Found.</h5>
                    @endif
                    @foreach($suppliers as $s)
                        <div class="card border-danger w-100 mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-around">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <img src="/images/profile.png" class="w-75">
                                    </div>
                                    <div class="mt-3">
                                            <h5 class="supplier-name font-weight-bold">{{ $s -> supplierName }}</h5>
                                            <h5 class="supplier-code font-weight-light">{{ $s -> supplierCode }}</h5>
                                            <h5 class="supplier-pic font-weight-light">{{ $s -> supplierPic }}</h5>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <button class="btn btn-success mt-3 mb-3 w-100" data-toggle="modal" id="detail" data-target="#editItem-{{ $s->id }}">Details/Edit</button>
                                        <button class="btn btn-danger mt-2 mb-2 w-100" data-toggle="modal" id="delete" data-target="#deleteSupplier-{{ $s -> id }}">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="editItem-{{ $s->id }}" tabindex="-1" role="dialog" aria-labelledby="editItemTitle"
                            aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger" style="color: white">
                                        <h5 class="modal-title" id="editItemTitle">Edit/Detail Supplier</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="/purchasing/supplier">
                                            @csrf
                                            @method('put')
                                            <input type="hidden" name="supplier_id" value="{{ $s -> id }}">
                                            <div class="form-row my-2">
                                                <div class="form-group col-md-6">
                                                    <label for="supplierName">Nama Supplier</label>
                                                    <input type="text" class="form-control" name="supplierName" id="supplierName" value="{{ $s -> supplierName }}" required>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="supplierPic">Alamat Supplier</label>
                                                    <input type="text" class="form-control" name="supplierPic" id="supplierPic" value="{{ $s -> supplierPic}}" required>
                                                </div>
                                            </div>
                                            <div class="form-row my-2">
                                                <div class="form-group col-md-6">
                                                    <label for="supplierEmail">Email Supplier</label>
                                                    <input type="email" class="form-control" name="supplierEmail" id="supplierEmail" value="{{ $s -> supplierEmail }}" required>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="supplierAddress">Alamat Supplier</label>
                                                    <input type="text" class="form-control" name="supplierAddress" id="supplierAddress" value="{{ $s -> supplierAddress}}" required>
                                                </div>
                                            </div>
                                            <div class="form-row my-2">
                                                <div class="form-group col-md-6">
                                                    <label for="supplierNoRek">No. Rekening Supplier</label>
                                                    <input type="text" class="form-control" name="supplierNoRek" id="supplierNoRek" value="{{ $s -> supplierNoRek }}" required>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="supplierNPWP">NPWP</label>
                                                    <input type="text" class="form-control" name="supplierNPWP" id="supplierNPWP" value="{{ $s -> supplierNPWP }}" required>
                                                </div>
                                            </div>
                                            <div class="form-row my-2">
                                                <div class="form-group col-md-6">
                                                    <label for="supplierCode">Kode Supplier</label>
                                                    <input type="text" class="form-control" name="supplierCode" id="supplierCode" value="{{ $s -> supplierCode }}" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="supplierNote">Note</label>
                                                <textarea class="form-control" id="supplierNote" name="supplierNote" rows="10">{{ $s -> supplierNote }}</textarea>
                                            </div>
                                            {{-- <h5><u>No. Telp</u></h5>
                                            <div class="form-row my-3">
                                                <div class="col">
                                                    <label for="noTelpBks">Bekasi</label>
                                                    <input type="text" class="form-control" name="noTelpBks" value="{{ $s -> noTelpBks }}">
                                                </div>
                                                <div class="col">
                                                    <label for="noTelpSmd">Samarinda</label>
                                                    <input type="text" class="form-control" name="noTelpSmd" value="{{ $s -> noTelpSmd }}">
                                                </div>
                                                <div class="col">
                                                    <label for="noTelpBer">Berau</label>
                                                    <input type="text" class="form-control" name="noTelpBer" value="{{ $s -> noTelpBer }}">
                                                </div>
                                            </div>
                                            <div class="form-row my-3">
                                                <div class="col">
                                                    <label for="noTelpBnt">Bunati</label>
                                                    <input type="text" class="form-control" name="noTelpBnt" value="{{ $s -> noTelpBnt }}">
                                                </div>
                                                <div class="col">
                                                    <label for="noTelpBnj">Banjarmasin</label>
                                                    <input type="text" class="form-control" name="noTelpBnj" value="{{ $s -> noTelpBnj }}">
                                                </div>
                                                <div class="col">
                                                    <label for="noTelpJkt">Jakarta</label>
                                                    <input type="text" class="form-control" name="noTelpJkt" value="{{ $s -> noTelpJkt }}">
                                                </div>
                                            </div> --}}
                                            <div class="d-flex ml-3 justify-content-center pb-3">
                                                <button type="submit" class="btn btn-primary">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="deleteSupplier-{{ $s->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteSupplierTitle"
                            aria-hidden="true">
                            <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h5 class="modal-title" id="editItemTitle" style="color: white">Delete Supplier: {{ $s -> supplierName }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <br>
                                        <h5 style="text-align: center">Are You Sure To Delete This Supplier ?</h5>
                                        <br>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <form method="POST" action="/purchasing/supplier">
                                            @csrf
                                            @method('delete')
                                            <input type="hidden" name="supplier_id" value="{{ $s -> id }}">
                                            <button class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="w-50">
                <h2 class="mb-4" style="text-align: center">Add Suppliers</h2>
                <div class="text-white font-weight-bold" style="background-color: #AA393D; border: 1px solid white; padding: 10px; border-radius: 10px">
                    <form method="POST" action="/purchasing/supplier">
                        @csrf
                        <div class="form-row my-2">
                            <div class="form-group col-md-6">
                                <label for="supplierName">Nama Supplier</label>
                                <input type="text" class="form-control" name="supplierName" id="supplierName" value="{{ old('supplierName') }}" required placeholder="CV/PT. ">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="supplierPic">PIC Supplier</label>
                                <input type="text" class="form-control" name="supplierPic" id="supplierPic" value="{{ old('supplierPic') }}" required placeholder="Input PIC Supplier">
                            </div>
                        </div>
                        <div class="form-row my-3">
                            <div class="form-group col-md-6">
                                <label for="supplierEmail">Email Supplier</label>
                                <input type="email" class="form-control" name="supplierEmail" id="supplierEmail" value="{{ old('supplierEmail') }}" required placeholder="Input Email Supplier">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="supplierAddress">Alamat Supplier</label>
                                <input type="text" class="form-control" name="supplierAddress" id="supplierAddress" value="{{ old('supplierAddress') }}" required placeholder="Input Alamat Supplier">
                            </div>
                          </div>
                        <div class="form-row my-3">
                            <div class="col-6">
                                <label for="supplierNoRek">No. Rekening</label>
                                <input type="text" name="supplierNoRek" class="form-control" value="{{ old('supplierNoRek') }}" required placeholder="Input Nomor Rekening Supplier">
                            </div>
                            <div class="col">
                                <label for="supplierNPWP">NPWP</label>
                                <input type="text" class="form-control" name="supplierNPWP" value="{{ old('supplierNPWP') }}" required placeholder="Input Nomor NPWP">
                            </div>
                            <div class="col">
                                <label for="supplierCode">Kode Supplier</label>
                                <input type="text" class="form-control" name="supplierCode" value="{{ old('supplierCode') }}" required placeholder="Input Kode Supplier">
                            </div>
                        </div>
                        <div class="d-flex justify-content-center font-weight-bold mt-4">
                            <h5><u>Note</u></h5>
                        </div>
                        {{-- <div class="form-row my-3">
                            <div class="col">
                                <label for="noTelpBks">Bekasi</label>
                                <input type="text" class="form-control" name="noTelpBks" value="{{ old('noTelpBks') }}" placeholder="Input No. Telp Cabang Bekasi">
                            </div>
                            <div class="col">
                                <label for="noTelpSmd">Samarinda</label>
                                <input type="text" class="form-control" name="noTelpSmd" value="{{ old('noTelpSmd') }}" placeholder="Input No. Telp Cabang Samarinda">
                            </div>
                            <div class="col">
                                <label for="noTelpBer">Berau</label>
                                <input type="text" class="form-control" name="noTelpBer" value="{{ old('noTelpBer') }}" placeholder="Input No. Telp Cabang Berau">
                            </div>
                        </div>
                        <div class="form-row my-3">
                            <div class="col">
                                <label for="noTelpBnt">Bunati</label>
                                <input type="text" class="form-control" name="noTelpBnt" value="{{ old('noTelpBnt') }}" placeholder="Input No. Telp Cabang Bunati">
                            </div>
                            <div class="col">
                                <label for="noTelpBnj">Banjarmasin</label>
                                <input type="text" class="form-control" name="noTelpBnj" value="{{ old('noTelpBnj') }}" placeholder="Input No. Telp Cabang Banjarmasin">
                            </div>
                            <div class="col">
                                <label for="noTelpJkt">Jakarta</label>
                                <input type="text" class="form-control" name="noTelpJkt" value="{{ old('noTelpJkt') }}" placeholder="Input No. Telp Cabang Jakarta">
                            </div>
                        </div> --}}
                        <div class="form-group">
                            <label for="supplierNote">Note</label>
                            <textarea class="form-control" id="supplierNote" name="supplierNote" rows="10"></textarea>
                        </div>
                        <div class="d-flex mt-4 justify-content-center pb-3">
                            <button type="submit" class="btn btn-primary">Add Supplier</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    setTimeout(function() {
        $('.alert').fadeOut('fast');
    }, 3000);

    function myFunction() {
        var input, filter, cards, cardContainer, title, i;
        
        input = document.getElementById("myFilter");
        filter = input.value.toUpperCase();
        cardContainer = document.getElementById("mySupplier");
        cards = cardContainer.getElementsByClassName("card");
        
        for (i = 0; i < cards.length; i++) {
            title = cards[i].querySelector(".supplier-name");
            code = cards[i].querySelector(".supplier-code");
            pic = cards[i].querySelector(".supplier-pic");
            if (title.innerText.toUpperCase().indexOf(filter) > -1 || code.innerText.toUpperCase().indexOf(filter) > -1 || pic.innerText.toUpperCase().indexOf(filter) > -1) {
                cards[i].style.display = "";
            } else {
                cards[i].style.display = "none";
            }
        }
    }
</script>

<style>
    .alert{
        text-align: center;
    }
    .modal-backdrop {
        height: 100%;
        width: 100%;
    }
    .scrolling-wrapper{
        overflow-y: auto;
        max-height: 600px;
    }
    .card-block{
        height: 425px;
        background-color: #fff;
        background-position: center;
        background-size: cover;
        transition: all 0.2s ease-in-out !important;
        border-radius: 24px;
        &:hover{
            box-shadow: none;
		opacity: 0.9;
	}
    @media (min-width: 300px) and (max-width: 768){
        .smaller-screen-size{
            width: 150px;
            word-break: break-all;
            font-size: 12px;
        }
    }
</style>

@endsection
