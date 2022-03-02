@if(Auth::user()->hasRole('crew'))

    @extends('../layouts.base')

    @section('title', 'Create Task')

    @section('container')
        <div class="row">
            @include('crew.sidebar')
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 mt-3">
                    <h1 class="mt-3 mb-5" style="text-align: center">Create Task</h1>
                    
                    @if(count($errors) > 0)
                        @foreach($errors->all() as $message)
                            <div class="alert alert-danger text-center" style="width: 40%; margin-left: 30%">
                                {{ $message }}
                            </div>
                        @endforeach
                    @endif

                    @if(session('failed'))
                        <div class="alert alert-danger text-center" style="width: 40%; margin-left: 30%">
                            {{ session('failed') }}
                        </div>
                    @endif

                    @if(session('status'))
                        <div class="alert alert-success text-center" style="width: 40%; margin-left: 30%">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div>
                        @if (Auth::user()->cabang == 'Kendari')
                            <form method="POST" action="/crew/create-task">
                                {{-- remove jumlah kargo awal --}}
                                @csrf
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Tug</label>
                                        <select class="form-control" name="tugName" id="tugName" style=" height:50px;" required>
                                            @forelse($tugs as $t)
                                                <option value="{{ $t -> tugName }}">{{ $t -> tugName }}</option>
                                            @empty
                                                <option value="" disabled>No Tugs Available</option>
                                            @endforelse
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Barge</label>
                                        <select class="form-control" name="bargeName" id="bargeName" style=" height:50px;">
                                                <option value="">None</option>
                                            @foreach($barges as $b)
                                                <option value="{{ $b -> bargeName }}">{{ $b -> bargeName }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Please choose type of tasks : </label>
                                        <select class="form-control" name="taskType" id="taskType" style=" height:50px;" required>
                                            <option value="" disabled>Choose Task...</option>
                                            <option value="Operational Shipment">Operational Shipment</option>
                                            <option value="Operational Transhipment">Operational Transhipment</option>
                                            <option value="Non Operational">Non Operational</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group col-md-4">
                                        <label for="portOfLoading">Port Of Loading</label>
                                        <input name="portOfLoading" type="text" class="form-control" id="portOfLoading" maxlength="50" placeholder="Input Port Of Loading..."
                                            style=" height: 50px" required>
                                    </div>
                                   
                                    <div class="form-group col-md-4">
                                        <label for="portOfDischarge">Port Of Discharge</label>
                                        <input name="portOfDischarge" type="text" class="form-control" id="portOfDischarge" maxlength="50" placeholder="Input Port Of Discharge..."
                                            style=" height: 50px" required>
                                    </div>
                                </div>

                                
                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="mt-5 btn btn-primary">Submit</button>
                                </div>
                            </form>
                        @else
                            {{-- jumlah kargo awal added --}}
                            <form method="POST" action="/crew/create-task">
                                @csrf
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Tug</label>
                                        <select class="form-control" name="tugName" id="tugName" style=" height:50px;" required>
                                            @forelse($tugs as $t)
                                                <option value="{{ $t -> tugName }}">{{ $t -> tugName }}</option>
                                            @empty
                                                <option value="" disabled>No Tugs Available</option>
                                            @endforelse
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Barge</label>
                                        <select class="form-control" name="bargeName" id="bargeName" style=" height:50px;">
                                                <option value="">None</option>
                                            @foreach($barges as $b)
                                                <option value="{{ $b -> bargeName }}">{{ $b -> bargeName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="cargoAmountStart">Jumlah Kargo Awal</label>
                                        <div class="input-group">
                                            <input name="cargoAmountStart" type="number" min="1" class="form-control" id="cargoAmountStart" step="0.001" placeholder="Input Jumlah Kargo Awal Dalam Ton..."
                                            style=" height: 50px" required>
                                            <div class="input-group-prepend">
                                                <div class="input-group-text" style="height: 50px">Ton</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Please choose type of tasks : </label>
                                        <select class="form-control" name="taskType" id="taskType" style=" height:50px;" required>
                                            <option value="" disabled>Choose Task...</option>
                                            <option value="Operational Shipment">Operational Shipment</option>
                                            <option value="Operational Transhipment">Operational Transhipment</option>
                                            <option value="Non Operational">Non Operational</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="portOfLoading">Port Of Loading</label>
                                        <input name="portOfLoading" type="text" class="form-control" id="portOfLoading" maxlength="50" placeholder="Input Port Of Loading..."
                                            style=" height: 50px" required>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="portOfDischarge">Port Of Discharge</label>
                                        <input name="portOfDischarge" type="text" class="form-control" id="portOfDischarge" maxlength="50" placeholder="Input Port Of Discharge..."
                                            style=" height: 50px" required>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="mt-5 btn btn-primary">Submit</button>
                                </div>
                            </form>
                        @endif
                        
                    </div>

                </div>
            </main>
            
        </div>

        <script type="text/javascript">
            setTimeout(function() {
                $('.alert').fadeOut('fast');
            }, 3000); 
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