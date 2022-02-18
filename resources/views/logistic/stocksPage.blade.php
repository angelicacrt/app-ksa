@if(Auth::user()->hasRole('logistic'))
    @extends('../layouts.base')

    @section('title', 'Logistic Stocks')

    @section('container')
        @include('logistic.sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-5">
            <div class="wrapper">
            <h1 class="mb-3" style="text-align: center">Stock Availability</h1>
                
            <br>
            
            @if(session('itemInvalid'))
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    {{ session('itemInvalid') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    {{ session('error') }}
                </div>
            @endif

            @error('itemName')
            <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                Nama Barang Invalid
            </div>
            @enderror

            @error('cabang')
            <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                Cabang Invalid
            </div>
            @enderror

            @error('cabang')
            <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                Cabang Invalid
            </div>
            @enderror

            @error('quantity')
            <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                Quantity Invalid
            </div>
            @enderror

            @error('unit')
            <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                Satuan Invalid
            </div>
            @enderror

            <div class="row">
                <div class="col-md-6">
                    <form action="">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Search Item by Nama, Cabang, Kode Barang..." name="search" id="search" value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </form>
                </div>
                <div class="col">
                    <select name="cabang" class="form-select w-25" onchange="window.location = this.value;">
                        <option selected disabled>Pilih Cabang</option>
                        <option value="/logistic/stocks/All" {{ $default_branch == 'All' ? 'selected' : '' }}>Semua Cabang</option>
                        <option value="/logistic/stocks/Jakarta" {{ $default_branch == 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
                        <option value="/logistic/stocks/Banjarmasin" {{ $default_branch == 'Banjarmasin' ? 'selected' : '' }}>Banjarmasin</option>
                        <option value="/logistic/stocks/Samarinda" {{ $default_branch == 'Samarinda' ? 'selected' : '' }}>Samarinda</option>
                        <option value="/logistic/stocks/Bunati" {{ $default_branch == 'Bunati' ? 'selected' : '' }}>Bunati</option>
                        <option value="/logistic/stocks/Babelan" {{ $default_branch == 'Babelan' ? 'selected' : '' }}>Babelan</option>
                        <option value="/logistic/stocks/Berau" {{ $default_branch == 'Berau' ? 'selected' : '' }}>Berau</option>
                    </select>
                </div>
            </div>

            <div id="content" style="overflow-x: auto">
                <table class="table mb-5">
                    <thead class="thead bg-danger">
                    <tr>
                        <th scope="col" style="color: white">Item Barang</th>
                        <th scope="col" style="color: white">Umur Barang</th>
                        <th scope="col" style="color: white">Quantity</th>
                        <th scope="col" style="color: white">Minimum Stok</th>
                        <th scope="col" style="color: white">Serial Number</th>
                        <th scope="col" style="color: white">Code Master Item</th>
                        <th scope="col" style="color: white">Cabang</th>
                        <th scope="col" style="color: white">Deskripsi</th>
                        <th scope="col" style="color: white">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $i)
                            <tr>
                                @php
                                    $str = 'itemNam' . 'e'   
                                @endphp
                                <td class="bg-white">{{ $i -> $str }}</td>
                                <td class="bg-white">{{ $i -> itemAge }}</td>
                                <td class="bg-white">{{ $i -> itemStock }} {{ $i -> unit }}</td>
                                @if($i -> minStock > $i -> itemStock)
                                    <td class="bg-white" style="color: red"><strong>{{ $i -> minStock }} {{ $i -> unit }}</strong></td>
                                @else
                                    <td class="bg-white" style="color: green"><strong>{{ $i -> minStock }} {{ $i -> unit }}</strong></td>
                                @endif
                                <td class="bg-white">{{ $i -> serialNo }}</td>
                                <td class="bg-white">{{ $i -> codeMasterItem }}</td>
                                <td class="bg-white">{{ $i -> cabang }}</td>
                                <td class="bg-white">{{ $i -> description }}</td>
                                @if($i -> cabang != Auth::user()->cabang)
                                    <td class="bg-white"><button class="btn btn-warning" data-toggle="modal" data-target="#request-stock-{{ $i -> id }}" style="color: white">Request Delivery</button></td>
                                @else
                                    <td class="bg-white"></td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-end">
                {{ $items->links() }}
            </div>

            </div>

            <!-- Modal #1 -->
            @foreach($items as $i)
                <div class="modal fade" id="request-stock-{{ $i->id }}" tabindex="-1" role="dialog" aria-labelledby="requestStockTitle"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-danger">
                                <h5 class="modal-title" id="editItemTitle" style="color: white">Request Stock</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="/logistic/stocks/{{ $i -> id }}/request">
                                    @csrf
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="itemName">Nama Barang <strong>(Periksa Kembali Nama Barang)</strong></label>
                                                <input type="text" class="form-control" id="itemName" name="itemName"
                                                    placeholder="Input Nama Barang" value="{{ $i -> itemName }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="cabang">Cabang</label>
                                                <input type="text" class="form-control" id="cabang" name="cabang" value="{{ $i -> cabang}}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="quantity">Quantity <strong>(Periksa Kembali Stok Barang)</strong></label>
                                                <input type="number" min="1" class="form-control" id="quantity" name="quantity"
                                                    placeholder="Input Quantity Dalam Angka">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="unit">Satuan</label>
                                                <input type="text" class="form-control" id="unit" name="unit" value="{{ $i -> unit }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Deskripsi (optional)</label>
                                        <textarea class="form-control" name="description" id="description" rows="3"
                                            placeholder="Input Deskripsi Tambahan"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </main>

        <style>
            body{
                /* background-image: url('/images/logistic-background.png'); */
                background-repeat: no-repeat;
                background-size: cover;
            }
            .wrapper{
                padding: 15px;
                margin: 15px;
                border-radius: 10px;
                background-color: antiquewhite;
                height: 900px;
                /* height: 100%; */
            }
            
            th, td{
                word-wrap: break-word;
                min-width: 140px;
                max-width: 140px;
                text-align: center;
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
            
            setTimeout(function() {
            $('.alert').fadeOut('fast');
        }, 3000); 
        </script>

    @endsection
@else
    @include('../layouts/notAuthorized')
@endif