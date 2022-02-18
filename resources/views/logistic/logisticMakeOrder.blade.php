@if(Auth::user()->hasRole('logistic'))
    @extends('../layouts.base')

    @section('title', 'Logistic Make Order')

    @section('container')
    <div class="row">
        @include('logistic.sidebar')
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 mt-3 wrapper">
                <h1 class="mt-3" style="text-align: center">Create Order</h1>
                <br>
                @if (session('status'))
                    <div class="alert alert-success" style="width: 40%; margin-left: 30%">
                        {{session('status')}}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                        {{session('error')}}
                    </div>
                @endif

                @if (session('errorCart'))
                    <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                        {{session('errorCart')}}
                    </div>
                @endif
                
                @error('item_id')
                    <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                        Nama Barang
                    </div>
                @enderror

                @error('quantity')
                    <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                        Quantity Invalid
                    </div>
                @enderror

                @error('tugName')
                    <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                        Nama Tug Invalid
                    </div>
                @enderror

                @error('bargeName')
                    <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                        Nama Barge Invalid
                    </div>
                @enderror

                @error('department')
                    <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                        Nama Department Invalid
                    </div>
                @enderror
                
                @error('golongan')
                    <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                        Golongan Invalid
                    </div>
                @enderror

                @error('orderType')
                    <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                        Tipe Order Invalid
                    </div>
                @enderror
                
                <div class="row">
                    {{-- <div> --}}
                    <div class="col">
                        <form method="POST" action="/logistic/{{ Auth::user()->id }}/add-cart">
                            @csrf
                            <div class="form-group p-2">
                                <label for="item_id" class="mt-3 mb-3">Item</label>
                                <br>
                                <select class="form-control" name="item_id" id="item_id" style="height:50px;">
                                    @foreach($items as $i)
                                        <option value="{{ $i -> id }}">{{ $i -> itemName }} ({{ $i -> cabang }}) {{ $i -> unit }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group p-2">
                                <label for="quantity" class="mb-3">Quantity</label>
                                <input name="quantity" type="number" min="1" class="form-control" id="quantity" placeholder="Input quantity dalam angka..."
                                    style="height: 50px" required>
                            </div>
                            <div class="form-group p-2">
                                <label for="department" class="mb-3">Department</label>
                                <br>
                                <select class="form-control" name="department" id="department" style="height:50px;">
                                    <option value="None">None</option>
                                    <option value="Deck">Deck</option>
                                    <option value="Mesin">Mesin</option>
                                </select>
                            </div>
                            <div class="form-group p-2">
                                <label for="note">Note (optional)</label>
                                <br>
                                <textarea class="form-control" name="note" Note="3"
                                    placeholder="Input Deskripsi Barang" style="height: 100px"></textarea>
                            </div>

                            <br>
                            <div class="d-flex ml-3 justify-content-center">
                                {{-- Add Item To Cart --}}
                                <button type="submit" class="btn btn-success mr-3" style="">Add To Cart</button>
                                
                                {{-- Modal --}}
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#submit-order">Submit Order</button>

                            </div>
                        </form>
                    </div>
                    <div class="col mt-5 mr-3 table-wrapper-scroll-y my-custom-scrollbar tableFixHead">
                        <table class="table">
                            <thead class="thead bg-danger">
                                <tr>
                                    <th scope="col">Nomor</th>
                                    <th scope="col">Nama Barang</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Department</th>
                                    <th scope="col">Golongan</th>
                                    <th scope="col">Note</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($carts as $key => $c)
                                    <tr>
                                        <td class="bg-white">{{ $key + 1 }}</td>
                                        <td class="bg-white">{{ $c -> item -> itemName }}</td>
                                        <td class="bg-white">{{ $c -> quantity }} {{ $c -> item -> unit }}</td>
                                        <td class="bg-white">{{ $c -> department }}</td>
                                        <td class="bg-white">{{ $c -> item -> golongan }}</td>
                                        <td class="bg-white">{{ $c -> note }}</td>
                                        {{-- Delete Item --}}
                                        <form method="POST" action="/logistic/{{ $c -> id }}/delete">
                                            @csrf
                                            @method('delete')
                                            <td class="bg-white"><button class="btn btn-warning btn-sm">Delete Item</button></td>
                                        </form>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <div class="modal fade" id="submit-order" tabindex="-1" role="dialog" aria-labelledby="submit-orderTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title ml-3" id="submitTitle" style="color: white">Input PR Requirements</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="/logistic/{{ Auth::user()->id }}/submit-order">
                    @csrf
                    <div class="modal-body"> 
                        <div class="form-group p-2">
                            <label for="tugs">Pilih Tug:</label>
                            <select class="form-control" name="tugName" id="tugName">
                                @foreach($tugs as $t)
                                    <option value="{{ $t -> tugName }}">{{ $t -> tugName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group p-2">
                            <label for="bargeName">Pilih Barge (optional):</label>
                            <select class="form-control" name="bargeName" id="bargeName">
                                <option value="">None</option>
                                @foreach($barges as $b)
                                    <option value="{{ $b -> bargeName }}">{{ $b -> bargeName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group p-2">
                            <label for="company" class="mb-3">Perusahaan</label>
                            <select class="form-control" name="company" id="company">
                                <option value="KSA">KSA</option>
                                <option value="ISA">ISA</option>
                                <option value="KSAO">KSA OFFSHORE</option>
                                <option value="KSAM">KSA MARITIME</option>
                            </select>
                        </div>
                        <div class="form-group p-2">
                            <label for="orderType">Tipe Order</label>
                            <select class="form-control" name="orderType" id="orderType">
                                <option value="Real Time">Real Time</option>
                                <option value="Susulan">Susulan</option>
                            </select>
                        </div>
                        <div class="form-group p-2">
                            <label for="descriptions" class="mb-3">Deskripsi (optional)</label>
                            <textarea class="form-control" name="descriptions" id="descriptions" placeholder="Input Deskripsi..." rows="5"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary mr-3">Submit</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>

    <script>
        setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 3000); 
    </script>

    <style>
        body{
            /* background-image: url('/images/logistic-background.png'); */
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
        }
    </style>

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