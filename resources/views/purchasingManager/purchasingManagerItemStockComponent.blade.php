<table class="table mb-5">
    <thead class="thead bg-danger">
        <tr>
            <th scope="col" style="color: white">Item Barang</th>
            <th scope="col" style="color: white">Umur Barang</th>
            <th scope="col" style="color: white">Quantity</th>
            <th scope="col" style="color: white">Minimum Stok</th>
            <th scope="col" style="color: white">Golongan</th>
            <th scope="col" style="color: white">Serial Number</th>
            <th scope="col" style="color: white">Code Master Item</th>
            <th scope="col" style="color: white">Cabang</th>
            <th scope="col" style="color: white">Status Barang</th>
            <th scope="col" style="color: white">Deskripsi</th>
            <th scope="col" style="color: white">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $i)
        <tr>
            <td><strong>{{ $i -> itemName }}</strong></td>
            <td>{{ $i -> itemAge }}</td>
            <td><strong>{{ $i -> itemStock }} {{ $i -> unit }}</strong></td>
            @if($i -> minStock > $i -> itemStock)
                <td style="color: red"><strong>{{ $i -> minStock }} {{ $i -> unit }}</strong></td>
            @else
                <td style="color: green"><strong>{{ $i -> minStock }} {{ $i -> unit }}</strong></td>
            @endif
            <td>{{ $i -> golongan }}</td>
            <td>{{ $i -> serialNo }}</td>
            <td><strong>{{ $i -> codeMasterItem }}</strong></td>
            <td>{{ $i -> cabang }}</td>

            @if($i -> itemState == 'Available')
                <td><span style="color: green; font-weight: bold">{{ $i -> itemState }}</span></td>
            @else
                <td><span style="color: red; font-weight: bold">{{ $i -> itemState }}</span></td>
            @endif
            
            <td>{{ $i -> description }}</td>
            <td>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-primary mr-2" data-toggle="modal" id="detail" data-target="#editItem-{{ $i->id }}">
                        Edit
                    </button>
                    <button class="btn btn-danger" data-toggle="modal" id="delete" data-target="#deleteItem-{{ $i->id }}">
                        Delete
                    </button>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@foreach($items as $i)
    <div class="modal fade" id="editItem-{{ $i->id }}" tabindex="-1" role="dialog" aria-labelledby="editItemTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="editItemTitle" style="color: white">Edit Item: {{ $i -> itemName }} ({{ $i -> cabang }})</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="/purchasing-manager/item-stocks/{{ $i -> id }}/edit-item">
                        @csrf
                        <div class="form-group">
                            <label for="itemName">Nama Barang</label>
                            <input type="text" class="form-control" id="itemName" name="itemName"
                                placeholder="Input Nama Barang" value="{{ $i -> itemName }}" required>
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
                                    <label for="umur">Bulan/Tahun</label>
                                    <select class="form-control" id="umur" name="umur" required>
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
                                        placeholder="Input Stok Barang" value="{{ $i -> itemStock }}" required>
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
                                placeholder="Input Minimum Stock" value="{{ $i -> minStock }}" required>
                        </div>
                        <div class="form-group">
                            <label for="golongan">Golongan</label>
                            <select class="form-control" id="golongan" name="golongan" required>
                                <option value="None" @if ($i -> golongan == 'None') selected="selected" @endif>None</option>
                                <option value="Floating" @if ($i -> golongan == 'Floating') selected="selected" @endif>Floating</option>
                                <option value="Dock" @if ($i -> golongan == 'Dock') selected="selected" @endif>Dock</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="serialNo">Serial Number / Part Number (XX-XXXX-)</label>
                            <input type="text" class="form-control" id="serialNo" name="serialNo"
                                placeholder="Input Serial Number (XX-XXXX-)" value="{{ $i -> serialNo }}" required>
                        </div>
                        <div class="form-group">
                            <label for="codeMasterItem">Code Master Item</label>
                            <input type="text" class="form-control" id="codeMasterItem" name="codeMasterItem"
                                placeholder="Input Code Master Item" value="{{ $i -> codeMasterItem }}">
                        </div>
                        <div class="form-group">
                            <label for="itemState">Status Barang</label>
                            <select class="form-control" id="itemState" name="itemState" required>
                                <option value="Available" @if ($i -> itemState == 'Available') selected="selected" @endif>Available</option>
                                <option value="Hold" @if ($i -> itemState == 'Hold') selected="selected" @endif>Hold</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="description">Deskripsi (optional)</label>
                            <textarea class="form-control" name="description" id="description" rows="3"
                                placeholder="Input Deskripsi Barang">{{ $i -> description }}</textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Edit Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteItem-{{ $i->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteItemTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="editItemTitle" style="color: white">Delete Item: {{ $i -> itemName }} ({{ $i -> cabang }})</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 style="text-align: center">Are You Sure To Delete This Item ?</h5>
                    <div class="d-flex justify-content-center align-items-center mt-2">
                        <span data-feather="alert-circle" style="width: 10vw; height: 10vh;stroke: red;
                        stroke-width: 2;"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <form method="POST" action="/purchasing-manager/item-stocks/{{ $i -> id }}/delete-item" >
                        @csrf
                        @method('delete')
                        <button class="btn btn-danger">Delete Item</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach