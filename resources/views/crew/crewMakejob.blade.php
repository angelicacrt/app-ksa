@if(Auth::user()->hasRole('crew'))
    @extends('../layouts.base')

    @section('title', 'crew Make Jobs')

    @section('container')
    <div class="row">
        @include('crew.sidebar')
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 mt-3 wrapper">
                <h1 class="mt-3" style="text-align: center">Create Job Request</h1> 
                <br>
                
                @if ($success = Session::get('success'))
                        <div class="alert alert-success alert-block" id="success">
                            <strong>{{ $success }}</strong>
                        </div>
                @endif

                @if ($errorCart = Session::get('errorCart'))
                        <div class="alert alert-success alert-block" id="errorCart">
                            <strong>{{ $errorCart }}</strong>
                        </div>
                @endif

                @if($errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger alert-block" id="success">
                            <strong>
                                {{ $error }}
                            </strong>
                        </div>
                    @endforeach 
                @endif

                <div class="row">
                {{-- input space --}}
                    <div class="col">
                        <form method="POST" action="/crew/{{ Auth::user()->id }}/add-cart-jasa">
                            @csrf
                            @if (count($carts) == 0)
                                <div class="form-group p-2">
                                    <label for="tugName" class="mt-3 mb-3">Nama TugBoat</label>
                                    <input list=tugNames type="text" class="col-sm-full custom-select custom-select-sm" name="tugName" id="tugName" style="height:50px;" placeholder="Ketik nama TugBoat" >
                                    <datalist id="tugNames">
                                        @foreach($tugs as $t)
                                            <option value="{{ $t -> tugName }}">{{ $t -> tugName }}</option>
                                        @endforeach
                                    </datalist>
                                </div>
                                <div class="form-group p-2">
                                    <label for="bargeName" class="mt-3 mb-3">Nama Barge (optional)</label>
                                    <input list = "bargeNames" type="text" class="col-sm-full custom-select custom-select-sm" name="bargeName" id="bargeName" style="height:50px;" placeholder="Ketik nama Barge">
                                    <datalist id="bargeNames">
                                        @foreach($barges as $b)
                                            <option value="{{ $b -> bargeName }}">{{ $b -> bargeName }}</option>
                                        @endforeach
                                    </datalist>
                                </div>
                                <div class="form-group p-2">
                                    <label for="lokasi" class="mb-3">Lokasi</label>
                                    <br>
                                    <input list = "lokasi_list" type="text" class="col-sm-full custom-select custom-select-sm" name="lokasi" id="lokasi" style="height:50px;" placeholder="Ketik Lokasi Job">
                                    <datalist id="lokasi_list">
                                        <option value="None" disabled selected>Pilih lokasi</option>
                                        <option value="Jakarta" id="Jakarta">Jakarta</option>
                                        <option value="Banjarmasin" id="Banjarmasin">Banjarmasin</option>
                                        <option value="Samarinda" id="Samarinda">Samarinda</option>
                                        <option value="Bunati" id="Bunati">Bunati</option>
                                        <option value="Babelan" id="Babelan">Babelan</option>
                                        <option value="Berau" id="Berau">Berau</option>
                                        <option value="Kendari" id="Kendari">Kendari</option>
                                        <option value="Morosi" id="Morosi">Morosi</option>
                                    </datalist>
                                </div>
                            @else
                                    <div class="form-group p-2">
                                        <label for="tugName" class="mt-3 mb-3">Nama TugBoat</label>
                                        <input type="text" class="col-sm-full custom-select custom-select-sm" name="tugName"  readonly="readonly" value="{{ old('tugName') }}" id="tugName" style="height:50px;" placeholder="Ketik nama TugBoat" >
                                    </div>
                                    <div class="form-group p-2">
                                        <label for="bargeName" class="mt-3 mb-3">Nama Barge (optional)</label>
                                        <input type="text" class="col-sm-full custom-select custom-select-sm" name="bargeName" readonly="readonly" value="{{ old('bargeName') }}" id="bargeName" style="height:50px;" placeholder="Ketik nama Barge">
                                    </div>
                                    <div class="form-group p-2">
                                        <label for="lokasi" class="mb-3">Lokasi</label>
                                        <br>
                                        <input type="text" class="col-sm-full custom-select custom-select-sm" name="lokasi" readonly="readonly" value="{{ old('lokasi') }}" id="lokasi" style="height:50px;" placeholder="Ketik Lokasi Job">
                                    </div>
                            @endif

                            <div class="form-group p-2">
                                <label for="quantity" class="mb-3">Quantity</label>
                                <input type="range" class="form-control-range" id="rangebar" min="0" max="100" oninput="updateInput(this.value);">
                                <input class="col-md-full form-control" type="number" name="quantity" id="quantity" value ="" placeholder="Masukan Quantity">
                            </div>
                            <div class="form-group p-2">
                                <label for="note">Job Description</label>
                                <br>
                                <textarea class="form-control" name="note" Note="3"
                                    placeholder="Ketik Deskripsi Job" style="height: 100px" required autofocus></textarea>
                            </div>

                            <br>
                            <div class="d-flex ml-3 justify-content-center">
                                {{-- Add Item To Cart --}}
                                <button type="submit" class="btn btn-success mr-3" style="">Add To Cart</button>
                        </form>
                        {{-- Submit Data--}}
                            <form method="POST" action="/crew/{{Auth::user()->id}}/submit-jasa">
                                @csrf
                                <button class="btn btn-primary">Submit Request</button>
                            </form>
                        </div>
                    </div>
                {{-- table data --}}
                    <div class="col mt-5 mr-3 table-wrapper-scroll-y my-custom-scrollbar tableFixHead">
                        <table class="table">
                            <thead class="thead bg-danger">
                                <tr>
                                    <th scope="col">No.</th>
                                    <th scope="col">Nama Tugboat / barge</th>
                                    <th scope="col">Lokasi Perbaikan</th>
                                    <th scope="col">Job Description</th>
                                    <th scope="col">quantity</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($carts as $c)
                                    <tr>
                                        <td class="bg-white">{{ $loop->index+1 }}</td>
                                        <td class="bg-white" style="text-transform: uppercase;"><strong>{{ $c ->tugName }} / {{ $c ->bargeName }}</td>
                                        <td class="bg-white"style="text-transform: uppercase;"><strong>{{ $c ->lokasi }}</td>
                                        <td class="bg-white" style="word-wrap: break-word;min-width: 250px;max-width: 250px;">{{ $c ->note }}</td>
                                        <td class="bg-white">{{ $c ->quantity }}</td>
                                        {{-- Delete Item --}}
                                        <form method="POST" action="/crew/{{ $c -> id }}/deletejasa">
                                            @csrf
                                            @method('delete')
                                            <td class="bg-white"><button class="btn btn-warning btn-sm">Delete Item</button></td>
                                        </form>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- </div> --}}
                </div>
            </div>
        </main>
    </div>
 {{-- script and style --}}
    <script>
        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 3000); 

        function updateInput(val) {
          document.getElementById('quantity').value=val; 
        }
    </script>

    <style>
        body{
            /* background-image: url('/images/crew-background.png'); */
            background-repeat: no-repeat;
            background-size: cover;
        }
        .wrapper{
            padding: 10px;
            border-radius: 10px;
            background-color: antiquewhite;
            height: 1100px;
            /* height: 100%; */
        }
        label{
            font-weight: bold;
        }
        th{
            color: white;
        }
        td, th{
            word-wrap: break-word;
            min-width: 100px;
            max-width: 120px;
            text-align: center;
        }
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
        .alert{
                text-align: center;
        }
        .modal-backdrop {
            height: 100%;
            width: 100%;
        }
        @media (max-width: 768px) {
        #row-wrapper{
            overflow-x: auto;
        }
    </style>
    @endsection
@else
    @include('../layouts/notAuthorized')
@endif