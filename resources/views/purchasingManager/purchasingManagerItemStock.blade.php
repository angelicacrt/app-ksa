@if(Auth::user()->hasRole('purchasingManager'))
    @extends('../layouts.base')

    @section('title', 'Item Stocks')

    @section('container')
        @include('purchasingManager.sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-5">
            <h1 class="mb-3" style="text-align: center">Stock Availability</h1>

            <br>
            @if(session('status'))
                <div class="alert alert-success" style="width: 40%; margin-left: 30%">
                    {{ session('status') }}
                </div>
            @endif

            @error('itemName')
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    Nama Barang Invalid
                </div>
            @enderror
            
            @error('itemAge')
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    Umur Barang Invalid
                </div>
            @enderror

            @error('itemStock')
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    Stok Barang Invalid
                </div>
            @enderror

            @error('serialNo')
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    Serial Number Invalid
                </div>
            @enderror

            @error('unit')
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    Satuan Unit Invalid
                </div>
            @enderror
            
            @error('codeMasterItem')
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    Code Master Item Invalid
                </div>
            @enderror

            @error('itemState')
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    Status Barang Invalid
                </div>
            @enderror

            @error('cabang')
                <div class="alert alert-danger" style="width: 40%; margin-left: 30%">
                    Cabang Invalid
                </div>
            @enderror

            <!-- Button trigger modal #1 -->
            <button type="button" class="btn btn-primary mb-3 ml-3" data-toggle="modal" data-target="#addItem">
                Add Item +
            </button>
            
            <div class="d-flex justify-content-between">
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
                        <option value="/purchasing-manager/item-stocks/All" {{ $default_branch == 'All' ? 'selected' : '' }}>Semua Cabang</option>
                        <option value="/purchasing-manager/item-stocks/Jakarta" {{ $default_branch == 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
                        <option value="/purchasing-manager/item-stocks/Banjarmasin" {{ $default_branch == 'Banjarmasin' ? 'selected' : '' }}>Banjarmasin</option>
                        <option value="/purchasing-manager/item-stocks/Samarinda" {{ $default_branch == 'Samarinda' ? 'selected' : '' }}>Samarinda</option>
                        <option value="/purchasing-manager/item-stocks/Bunati" {{ $default_branch == 'Bunati' ? 'selected' : '' }}>Bunati</option>
                        <option value="/purchasing-manager/item-stocks/Babelan" {{ $default_branch == 'Babelan' ? 'selected' : '' }}>Babelan</option>
                        <option value="/purchasing-manager/item-stocks/Berau" {{ $default_branch == 'Berau' ? 'selected' : '' }}>Berau</option>
                        <option value="/purchasing-manager/item-stocks/Kendari" {{ $default_branch == 'Kendari' ? 'selected' : '' }}>Kendari</option>
                        <option value="/purchasing-manager/item-stocks/Morosi" {{ $default_branch == 'Morosi' ? 'selected' : '' }}>Morosi</option>
                    </select>
                </div>
                <div class="mr-4">
                    <button type="button" onclick="refresh()">
                        <span data-feather="refresh-ccw"></span>
                    </button>
                </div>
            </div>
            
            <div class="spinner-border spinner-border-lg text-danger" role="status" id="wait">
                <span class="sr-only">Loading...</span>
            </div>

            <div id="content" style="overflow-x:auto;">
                @include('purchasingManager.purchasingManagerItemStockComponent')
            </div>

            <div class="d-flex justify-content-end">
                {{ $items->links() }}
            </div>

            <!-- Modal #1 -->
            <div class="modal fade" id="addItem" tabindex="-1" role="dialog" aria-labelledby="addItem"
                aria-hidden="true" data-backdrop="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-danger">
                            <h5 class="modal-title" id="addItemTitle" style="color: white">Add New Item</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="/purchasing-manager/item-stocks">
                                @csrf
                                <div class="form-group">
                                    <label for="itemName">Nama Barang</label>
                                    <input type="text" class="form-control" id="itemName" name="itemName"
                                        placeholder="Input Nama Barang" required>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="itemAge">Umur Barang</label>
                                            <input type="number" min="1" class="form-control" id="itemAge" name="itemAge"
                                                placeholder="Input Umur Barang Dalam Angka" required>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="umur">Jenis Durasi</label>
                                            <select class="form-control" id="umur" name="umur" required>
                                                <option value="Jam">Jam</option>
                                                <option value="Hari">Hari</option>
                                                <option value="Bulan">Bulan</option>
                                                <option value="Tahun">Tahun</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="itemStock">Stok Barang</label>
                                            <input type="number" min="1" class="form-control" id="itemStock" name="itemStock"
                                                placeholder="Input Stok Barang" required>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="unit">Unit</label>
                                            <select class="form-control" name="unit" id="unit" required>
                                                <option value="Bks">Bks</option>
                                                    <option value="Btg">Btg</option>
                                                    <option value="Btl">Btl</option>
                                                    <option value="Cm">Cm</option>
                                                    <option value="Crt">Crt</option>
                                                    <option value="Cyl">Cyl</option>
                                                    <option value="Doz">Doz</option>
                                                    <option value="Drm">Drm</option>
                                                    <option value="Duz">Duz</option>
                                                    <option value="Gln">Gln</option>
                                                    <option value="Jrg">Jrg</option>
                                                    <option value="Kbk">Kbk</option>
                                                    <option value="Kg">Kg</option>
                                                    <option value="Klg">Klg</option>
                                                    <option value="Ktk">Ktk</option>
                                                    <option value="Lbr">Lbr</option>
                                                    <option value="Lgt">Lgt</option>
                                                    <option value="Ls">Ls</option>
                                                    <option value="Ltr">Ltr</option>
                                                    <option value="Mtr">Mtr</option>
                                                    <option value="Pak">Pak</option>
                                                    <option value="Pal">Pal</option>
                                                    <option value="Pax">Pax</option>
                                                    <option value="Pc">Pc</option>
                                                    <option value="Pcs">Pcs</option>
                                                    <option value="Plt">Plt</option>
                                                    <option value="Psg">Psg</option>
                                                    <option value="Ptg">Ptg</option>
                                                    <option value="Ret">Ret</option>
                                                    <option value="Rol">Rol</option>
                                                    <option value="Sak">Sak</option>
                                                    <option value="SET">SET</option>
                                                    <option value="Tbg">Tbg</option>
                                                    <option value="Trk">Trk</option>
                                                    <option value="Unt">Unt</option>
                                                    <option value="Zak">Zak</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="minStock">Minimum Stok</label>
                                    <input type="number" class="form-control" id="minStock" name="minStock"
                                        placeholder="Input Minimum Stock" required>
                                </div>
                                <div class="form-group">
                                    <label for="golongan">Golongan</label>
                                    <select class="form-control" id="golongan" name="golongan" required>
                                        <option value="None">None</option>
                                        <option value="Floating">Floating</option>
                                        <option value="Dock">Dock</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="serialNo">Serial Number / Part Number (XX-XXXX-)</label>
                                    <input type="text" class="form-control" id="serialNo" name="serialNo"
                                        placeholder="Input Serial Number (XX-XXXX-)" required>
                                </div>
                                <div class="form-group">
                                    <label for="codeMasterItem">Code Master Item (optional)</label>
                                    <input type="text" class="form-control" id="codeMasterItem" name="codeMasterItem"
                                        placeholder="Input Code Master Item">
                                </div>
                                <div class="form-group">
                                    <label for="cabang">Cabang</label>
                                    <select class="form-control" id="cabang" name="cabang" required>
                                        <option selected disabled="">Choose...</option>
                                        <option value="Jakarta" id="Jakarta">Jakarta</option>
                                        <option value="Banjarmasin" id="Banjarmasin">Banjarmasin</option>
                                        <option value="Samarinda" id="Samarinda">Samarinda</option>
                                        <option value="Bunati" id ="Bunati">Bunati</option>
                                        <option value="Babelan"id ="Babelan">Babelan</option>
                                        <option value="Berau" id ="Berau">Berau</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="description">Deskripsi (optional)</label>
                                    <textarea class="form-control" name="description" id="description" rows="3"
                                        placeholder="Input Deskripsi Barang"></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Add Item</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <style>
            th, td{
                word-wrap: break-word;
                min-width: 120px;
                max-width: 120px;
                text-align: center;
                vertical-align: middle;
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
            setTimeout(function() {
                $('.alert').fadeOut('fast');
            }, 3000);
        </script>

        <script type="text/javascript">
            let spinner = document.getElementById("wait");
            spinner.style.visibility = 'hidden';

            function refresh(){
                event.preventDefault();

                let url = "{{ route('purchasingManager.refreshPurchasingManagerItemStock') }}";
                let searchData = document.getElementById('search').value;
                let default_branch = '{{ $default_branch }}';
                let _token = $('input[name=_token]').val();

                $.ajax({
                    url: url,
                    method: "POST",
                    data: {
                        _token,
                        searchData,
                        default_branch
                    },
                    beforeSend: function(){
                        $('#content').hide();
                        spinner.style.visibility = 'visible';
                    },
                    success: function(data){
                        $('#content').html(data);
                        $('#content').show();
                        spinner.style.visibility = 'hidden';
                    }
                })
            }
        </script>

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