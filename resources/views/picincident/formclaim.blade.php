@extends('../layouts.base')

@section('title', 'Create-Form')

@section('container')
<x-guest-layout>
    <div class="row">
        @include('picincident.sidebarincident')
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="row">
                <div class="col" style="margin-top: 5%">
                    <div class="jumbotron jumbotron-fluid" >
                        <div class="container">

                            <div class="text-md-center">
                                <h4 class="display-4">Create Form Claim Insurance</h4>
                            </div>
                        
                            <form method="POST" action="/picincident/formclaim/submitform">
                                @csrf
                                {{-- bagian atas --}}
                                <div class="form-row">
                                    <div class="col-lg-3" style="margin-right:2%">
                                        <x-label for="name" :value="__('Name : ')" style="margin-top: 2%; margin-left: 1%" />
                                        <x-input id="name" class="block mt-1 w-full" type="text" name="name" placeholder="Enter Name" :value="old('name')"  autofocus />
                                        
                                        <x-label for="FormClaim" :value="__('No. FormClaim : ')" style="margin-top: 2%; margin-left: 1%"  />
                                        <x-input id="FormClaim" class="block mt-1 w-full" type="text" name="FormClaim" placeholder="Enter No. FormClaim" :value="old('FormClaim')" required autofocus />

                                        <x-label for="Incident" :value="__('Incident : ')" style="margin-top: 2%; margin-left: 1%" />
                                        <x-input id="Incident" class="block mt-1 w-full" type="text" name="Incident" placeholder="Enter Incident" :value="old('Incident')" required autofocus />
                                    </div>
                                    <div class="col-lg-3" style="margin-right:2%">
                                        <x-label for="dateclaim" :value="__('Tgl.Form Claim : ')" style="margin-top: 2%; margin-left: 1%" />
                                        <x-input id="dateclaim" class="block mt-1 w-full" type="date" name="dateclaim"  autofocus />
                                        
                                        <x-label for="dateincident" :value="__('Tgl Incident : ')" style="margin-top: 2%; margin-left: 1%" />
                                        <x-input id="dateincident" class="block mt-1 w-full" type="date" name="dateincident"  autofocus />
                                        
                                        <x-label for="Surveyor" :value="__('Surveyor : ')" style="margin-top: 2%; margin-left: 1%" />
                                        <x-input id="Surveyor" class="block mt-1 w-full" type="text" name="Surveyor" placeholder="Enter Surveyor's Name" :value="old('Surveyor')"  autofocus />
                                        
                                    </div>
                                    <div class="col-lg-3">
                                        <label style="margin-top: 2%; margin-left: 1%">Tug</label>
                                        <input list="Nama_kapals" name="TugBoat" id="nama_kapal" placeholder="Nama tug" value="{{old('TugBoat')}}" class="col-lg-full custom-select custom-select-md" autofocus>
                                        <datalist id="Nama_kapals">
                                            @foreach ($tug as $t)
                                                <option value="{{ $t -> tugName }}">{{ $t -> tugName }}</option>
                                            @endforeach
                                        </datalist>

                                        <label style="margin-top: 2%; margin-left: 1%">Barge</label>
                                        <input list="nama_tug_barges" class="col-lg-full custom-select custom-select-md" value="{{old('barge')}}"  placeholder="Nama Barge" name="barge" id="Nama_Barge" autofocus>
                                        <datalist id="nama_tug_barges">
                                            @foreach ($barge as $b)
                                                <option value="{{ $b -> bargeName }}">{{ $b -> bargeName }}</option>
                                            @endforeach
                                        </datalist>
                                        
                                        {{-- <x-label for="barge" :value="__('barge : ')" style="margin-top: 2%; margin-left: 1%" />
                                        <x-input id="barge" class="block mt-1 w-full" type="text" name="barge" placeholder="Enter barge" :value="old('barge')"  autofocus />
    
                                        <x-label for="TugBoat" :value="__('TugBoat : ')" style="margin-top: 2%; margin-left: 1%" />
                                        <x-input id="TugBoat" class="block mt-1 w-full" type="text" name="TugBoat" placeholder="Enter TugBoat Name" :value="old('TugBoat')"  autofocus /> --}}

                                        <x-label for="TSI_TugBoat" :value="__('TSI TugBoat : ')" style="margin-top: 2%; margin-left: 1%"  />
                                          <div class="input-group mb-1">
                                              <select class="btn btn-outline-secondary" name="mata_uang_TSI">
                                                  <option selected value="USD" id="">USD</option>
                                                  <option value="IDR" id="">IDR</option>
                                              </select>
                                              <input id="TSI_TugBoat" type="text" class="form-control" name="TSI_TugBoat" placeholder="Enter TSI TugBoat" value="{{ old('TSI_TugBoat') }}"   autofocus>
                                            </div>
                                        <x-label for="TSI_barge" :value="__('TSI Barge : ')" style="margin-top: 2%; margin-left: 1%"  />
                                          <div class="input-group mb-1">
                                              <select class="btn btn-outline-secondary" name="mata_uang_TSI_barge">
                                                  <option selected value="USD" id="">USD</option>
                                                  <option value="IDR" id="">IDR</option>
                                              </select>
                                              <input id="TSI_barge" type="text" class="form-control" name="TSI_barge" placeholder="Enter TSI barge" value="{{ old('TSI_barge') }}"   autofocus/>
                                            </div>
                                            {{-- <x-label for="TSI_barge" :value="__('TSI barge : ')" style="margin-top: 2%; margin-left: 1%"  /> --}}
                                    </div>
                                </div>

                                <br>
                                <br>
                                {{-- bagian bawah --}}
                                <div class="form-row">
                                    <div class="col-lg-3">
                                        <x-label for="jenisincident" :value="__('Jenis Incident : ')" style="margin-top: 2%; margin-left: 1%" />
                                        <select name="jenisincident" id="jenisincident" class="block mt-1 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" required autofocus>
                                            <option selected value="TP" >TP</option>
                                            <option value="HM" >HM</option>
                                        </select>
                                        
                                        <x-label for="Item_name" :value="__('Item : ')" style="margin-top: 2%; margin-left: 1%" />
                                        <x-input id="Item_name" class="block mt-1 w-full" type="text" name="Item_name" placeholder="Enter Item Name" :value="old('Item_name')" required autofocus />
                                    </div>
                                    
                                    <div class="col-lg-3" style="margin-right:2%">
                                        <x-label for="Deductible" :value="__('Deductible : ')" style="margin-top: 2%; margin-left: 1%" />
                                        <input id="Deductible" class="form-control" type="text" name="Deductible" placeholder="Enter Deductible" value="{{old('Deductible')}}" autofocus />
    
                                        <x-label for="Amount" :value="__('Amount : ')" style="margin-top: 2%; margin-left: 1%" />
                                        {{-- <x-input id="Amount" class="block mt-1 w-full" type="text" name="Amount" placeholder="Enter Amount" :value="old('Amount')" required autofocus /> --}}
                                        <div class="input-group mb-1">
                                            <select class="btn btn-outline-secondary" name="mata_uang_amount">
                                                <option selected value="USD" id="">USD</option>
                                                <option value="IDR" id="">IDR</option>
                                                <option value="SGD" id="">SGD</option>
                                                <option value="EURO" id="">EURO</option>
                                            </select>
                                            <input id="Amount" type="text" class="form-control" name="Amount" placeholder="Enter Amount" value="{{ old('Amount') }}"  required autofocus>
                                        </div>
                                    </div>
                                </div>
                                
                                <x-label for="Description" :value="__('Description : ')" style="margin-top: 2%; margin-left: 1%;" required autofocus/>
                                <textarea class="form-control" name="reasonbox" id="Description"  autofocus rows="3"></textarea>

                                <div class="text-md-right">
                                    <button class="btn btn-dark" type="submit" id="addcart" style= "margin-top: 1%; margin-bottom: 1%;width: 20%;">Add To List</button>
                                </div>
                            </form>

                            <form method="POST" action="/picincident/create-history">
                                @csrf
                                {{-- @php
                                    $date = date('Y-m-d');
                                    $spc = rand(1,100);
                                    $name = 'file FCI-'. $spc . ' - ' . $date;
                                @endphp
                                    <input type="hidden" name ="nama_file" value ="{{$name}}" /> --}}
                                    <div class="text-md-right">
                                    <button class="btn btn-outline-danger" type="submit" id="createform" name="createform"  style="margin-left: 80%; width: 20%;">Create Form</button>
                                </div>
                            </form>
                            
                            @if ($success = Session::get('success'))
                                <div class="center">
                                    <div class="alert alert-success alert-block" id="message">
                                        <strong>{{ $success }}</strong>
                                    </div>
                                </div>
                            @endif

                            @if ($ERR = Session::get('ERR'))
                                <div class="center">
                                    <div class="alert alert-danger alert-block" id="message">
                                        <strong>{{ $ERR }}</strong>
                                    </div>
                                </div>
                            @endif
{{-- show form table --}}
                        <table class="table" style="margin-top: 2%">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">No.</th>
                                    <th scope="col">Jenis Incident</th>
                                    <th scope="col">Item</th>
                                    <th scope="col">Deductible</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            @forelse($tempcarts as $temp)
                            <tbody>
                                    <tr>
                                        <td class="table-info">{{$loop->index+1}}</td>
                                        <td class="table-info">{{$temp->jenis_incident}}</td>
                                        <td class="table-info">{{$temp->item}}</td>
                                        <td class="table-info">{{$temp->deductible}}</td>
                                        <td class="table-info">{{Str::limit($temp->description , 20)}}</td>
                                        <td class="table-info">{{$temp->mata_uang_amount}}.{{$temp->amount}}</td>
                                        <td class="table-info">
                                            <form action="/picincident/formclaim/destroy/{{$temp->id}}" method="POST">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" id="Deletepost" onClick="return confirm('Are you sure?')" class="btn btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                        <tr> <td class="table-warning" > <strong style="font-size:16px"> Request not yet added </strong> </td>  </tr>
                                    @endforelse
                            </tbody>
                        </table>
                        <script>
                            setTimeout(function(){
                                $("div.alert").remove();
                            }, 3000 ); // 3 secs
                        </script>
                        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
                        <script>
                            // to separate the number every 3 digits
                            function updateTextView(_obj){
                                var num = getNumber(_obj.val());
                                if(num==0){
                                    _obj.val('');
                                }else{
                                    _obj.val(num.toLocaleString());
                                }
                            }
                            function getNumber(_str){
                                var arr = _str.split('');
                                var out = new Array();
                                for(var cnt=0;cnt<arr.length;cnt++){
                                    if(isNaN(arr[cnt])==false){
                                    out.push(arr[cnt]);
                                    }
                                }
                                return Number(out.join(''));
                            }

                            // call only for certain inputs
                            var amount = document.getElementById('Amount');
                            amount.addEventListener('keyup', function(){
                                updateTextView($(amount));
                            });

                            var barge = document.getElementById('TSI_barge');
                            barge.addEventListener('keyup', function(){
                                updateTextView($(barge));
                            });

                            var Deductible = document.getElementById('Deductible');
                            Deductible.addEventListener('keyup', function(){
                                updateTextView($(Deductible));
                            });

                            var tug = document.getElementById('TSI_TugBoat');
                            tug.addEventListener('keyup', function(){
                                updateTextView($(tug));
                            });
                        </script>
                    </div>
                </div>  
            </div>
        </div>
    </main>
</div>
</x-guest-layout>
@endsection