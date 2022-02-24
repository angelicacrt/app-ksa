@if(Auth::user()->hasRole('crew'))

    @extends('../layouts.base')

    @section('title', 'Create Task')

    @section('container')
        <div class="row">
            @include('crew.sidebar')
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="page-bg flex-wrap flex-md-nowrap align-items-center pt-4 pb-5 mb-3 mt-3">
                    
                    @if(count($errors) > 0)
                        @foreach($errors->all() as $message)
                            <div class="alert alert-danger text-center" style="width: 40%; margin-left: 30%">
                                {{ $message }}
                            </div>
                        @endforeach
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger text-center" style="width: 40%; margin-left: 30%">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('status'))
                        <div class="alert alert-success text-center" style="width: 40%; margin-left: 30%">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    @foreach($ongoingTask as $ot)
                        <form action="/crew/ongoing-task" method="post">
                            @csrf
                            <input type="hidden" name="taskId" value="{{ $ot -> id }}">
                            <div class="d-flex justify-content-around">
                                <div style="width: 60%">
                                    <div class="d-flex">
                                        <h5 class="mt-4">Task : <span class="text-danger">{{ $ot -> taskType }}</span></h5>

                                        <div class="form-group row ml-3">
                                            <label class="text-danger font-weight-bold" for="from" class="col-sm-2 col-form-label text-danger font-weight-bold">From</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="from" name="from" placeholder="Input Source" value="{{ $ot -> from }}" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="text-danger font-weight-bold" for="to" class="col-sm-2 col-form-label text-danger font-weight-bold">To</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="to" name="to" placeholder="Input Destination" value="{{ $ot -> to }}" required>
                                            </div>
                                        </div>

                                        <h5 class="mt-4">Nama Kapal : <span class="text-danger">{{ $ot -> tugName }}/{{ $ot -> bargeName }}</span></h5>
                                    </div>

                                    <div class="d-flex flex-wrap mt-4" style="overflow-y: auto; max-height: 800px">
                                        @if($ot -> taskType == 'Operational Shipment')
                                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center text-danger font-weight-bold">Time Arrival</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="datetime-local" name="arrivalTime" id="arrivalTime" value="{{ $ot -> arrivalTime != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> arrivalTime)) : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center text-danger font-weight-bold">Start Aside (L)</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="datetime-local" name="startAsideL" id="startAsideL" value="{{ $ot -> startAsideL != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> startAsideL)) : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center text-danger font-weight-bold">Aside (L)</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="datetime-local" name="asideL" id="asideL" value="{{ $ot -> asideL != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> asideL)) : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center text-danger font-weight-bold">Commence Load (L)</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="datetime-local" name="commenceLoadL" id="commenceLoadL" value="{{ $ot -> commenceLoadL != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> commenceLoadL)) : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center text-danger font-weight-bold">Completed Loading (L)</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="datetime-local" name="completedLoadingL" id="completedLoadingL" value="{{ $ot -> completedLoadingL != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> completedLoadingL)) : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center text-danger font-weight-bold">Cast Off (L)</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="datetime-local" name="cOffL" id="cOffL" value="{{ $ot -> cOffL != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> cOffL)) : '' }}">
                                                    </div>
                                                </div>
                                            </div>

                                            @if($ot -> user -> cabang == 'Samarinda')
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Departure To Jetty</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="departureJetty" id="departureJetty" value="{{ $ot -> departureJetty != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> departureJetty)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Pengolongan Naik</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="pengolonganNaik" id="pengolonganNaik" value="{{ $ot -> pengolonganNaik != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> pengolonganNaik)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Pengolongan Turun</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="pengolonganTurun" id="pengolonganTurun" value="{{ $ot -> pengolonganTurun != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> pengolonganTurun)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Mooring Area</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="mooringArea" id="mooringArea" value="{{ $ot -> mooringArea != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> mooringArea)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($ot -> user -> cabang !== 'Kendari' && $ot -> user -> cabang !== 'Babelan')
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Doc Overhand {{ $ot -> user -> cabang == 'Samarinda' ? '(Optional)' : '' }}</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="DOH" id="DOH" value="{{ $ot -> DOH != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> DOH)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Doc On Boat</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="DOB" id="DOB" value="{{ $ot -> DOB != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> DOB)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Departure to (POD)</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="departurePOD" id="departurePOD" value="{{ $ot -> departurePOD != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> departurePOD)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Arrival (POD)</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="arrivalPODGeneral" id="arrivalPODGeneral" value="{{ $ot -> arrivalPODGeneral != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> arrivalPODGeneral)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Start Aside (POD)</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="startAsidePOD" id="startAsidePOD" value="{{ $ot -> startAsidePOD != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> startAsidePOD)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Aside (POD)</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="asidePOD" id="asidePOD" value="{{ $ot -> asidePOD != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> asidePOD)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Commence Load (POD)</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="commenceLoadPOD" id="commenceLoadPOD" value="{{ $ot -> commenceLoadPOD != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> commenceLoadPOD)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Completed Loading (POD)</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="completedLoadingPOD" id="completedLoadingPOD" value="{{ $ot -> completedLoadingPOD != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> completedLoadingPOD)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Cast Off (POD)</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="cOffPOD" id="cOffPOD" value="{{ $ot -> cOffPOD != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> cOffPOD)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Doc On Boat (POD)</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="DOBPOD" id="DOBPOD" value="{{ $ot -> DOBPOD != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> DOBPOD)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        @if($ot -> user -> cabang == 'Samarinda')
                                                            <h5 class="card-title text-center text-danger font-weight-bold">Departure Pangkalan</h5>
                                                        @else
                                                            <h5 class="card-title text-center text-danger font-weight-bold">Departure To</h5>
                                                        @endif
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="departureTime" id="departureTime" value="{{ $ot -> departureTime != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> departureTime)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                        @endif
                                        @if($ot -> taskType == 'Operational Transhipment')
                                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                <div class="card-body">
                                                    @if($ot -> user -> cabang == 'Samarinda')
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Arrival Pangkalan</h5>
                                                    @else
                                                        <h5 class="card-title text-center text-danger font-weight-bold">From Arrival Vessel</h5>
                                                    @endif
                                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="datetime-local" name="faVessel" id="faVessel" value="{{ $ot -> faVessel != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> faVessel)) : '' }}">
                                                    </div>
                                                </div>
                                            </div>

                                            @if($ot -> user -> cabang == 'Samarinda')
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Departure To Jetty</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="departureJetty" id="departureJetty" value="{{ $ot -> departureJetty != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> departureJetty)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Pengolongan Naik</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="pengolonganNaik" id="pengolonganNaik" value="{{ $ot -> pengolonganNaik != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> pengolonganNaik)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center text-danger font-weight-bold">Arrival POL</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="datetime-local" name="arrivalPOL" id="arrivalPOL" value="{{ $ot -> arrivalPOL != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> arrivalPOL)) : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center text-danger font-weight-bold">Start Aside (L)</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="datetime-local" name="startAsideL" id="startAsideL" value="{{ $ot -> startAsideL != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> startAsideL)) : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center text-danger font-weight-bold">Aside (L)</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="datetime-local" name="asideL" id="asideL" value="{{ $ot -> asideL != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> asideL)) : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center text-danger font-weight-bold">Commence Load (L)</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="datetime-local" name="commenceLoadL" id="commenceLoadL" value="{{ $ot -> commenceLoadL != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> commenceLoadL)) : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center text-danger font-weight-bold">Completed Loading (L)</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="datetime-local" name="completedLoadingL" id="completedLoadingL" value="{{ $ot -> completedLoadingL != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> completedLoadingL)) : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center text-danger font-weight-bold">Cast Off (L)</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="datetime-local" name="cOffL" id="cOffL" value="{{ $ot -> cOffL != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> cOffL)) : '' }}">
                                                    </div>
                                                </div>
                                            </div>

                                            @if($ot -> user -> cabang == 'Samarinda')
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Pengolongan Turun</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="pengolonganTurun" id="pengolonganTurun" value="{{ $ot -> pengolonganTurun != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> pengolonganTurun)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Mooring Area</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="mooringArea" id="mooringArea" value="{{ $ot -> mooringArea != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> mooringArea)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if($ot -> user -> cabang !== 'Kendari' && $ot -> user -> cabang !== 'Babelan')
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Doc Overhand {{ $ot -> user -> cabang == 'Samarinda' ? '(Optional)' : '' }}</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="DOH" id="DOH" value="{{ $ot -> DOH != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> DOH)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Doc On Boat</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="DOB" id="DOB" value="{{ $ot -> DOB != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> DOB)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Departure (POD)</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="departurePOD" id="departurePOD" value="{{ $ot -> departurePOD != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> departurePOD)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Arrival (POD)</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="arrivalPODGeneral" id="arrivalPODGeneral" value="{{ $ot -> arrivalPODGeneral != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> arrivalPODGeneral)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Start Aside (MV)</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="startAsideMVTranshipment" id="startAsideMVTranshipment" value="{{ $ot -> startAsideMVTranshipment != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> startAsideMVTranshipment)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Aside (MV)</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="asideMVTranshipment" id="asideMVTranshipment" value="{{ $ot -> asideMVTranshipment != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> asideMVTranshipment)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Commence Discharge (MV)</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="commMVTranshipment" id="commMVTranshipment" value="{{ $ot -> commMVTranshipment != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> commMVTranshipment)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Complete Discharge (MV)</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="compMVTranshipment" id="compMVTranshipment" value="{{ $ot -> compMVTranshipment != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> compMVTranshipment)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        <h5 class="card-title text-center text-danger font-weight-bold">Cast Off (MV)</h5>
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="cOffMVTranshipment" id="cOffMVTranshipment" value="{{ $ot -> cOffMVTranshipment != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> cOffMVTranshipment)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                    <div class="card-body">
                                                        @if($ot -> user -> cabang == 'Samarinda')
                                                            <h5 class="card-title text-center text-danger font-weight-bold">Departure Pangkalan</h5>
                                                        @else
                                                            <h5 class="card-title text-center text-danger font-weight-bold">Departure To</h5>
                                                        @endif
                                                        <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                        <div class="form-group mt-3">
                                                            <input class="form-control" type="datetime-local" name="departureTimeTranshipment" id="departureTimeTranshipment" value="{{ $ot -> departureTimeTranshipment != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> departureTimeTranshipment)) : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                        @if($ot -> taskType == 'Return Cargo')
                                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center text-danger font-weight-bold">Arrival (POD)</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="datetime-local" name="arrivalPODCargo" id="arrivalPODCargo" value="{{ $ot -> arrivalPODCargo != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> arrivalPODCargo)) : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center text-danger font-weight-bold">Start Aside (MV)</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="datetime-local" name="startAsideMVCargo" id="startAsideMVCargo" value="{{ $ot -> startAsideMVCargo != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> startAsideMVCargo)) : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center text-danger font-weight-bold">Aside (MV)</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="datetime-local" name="asideMVCargo" id="asideMVCargo" value="{{ $ot -> asideMVCargo != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> asideMVCargo)) : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center text-danger font-weight-bold">Commence Discharge (MV)</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="datetime-local" name="commMVCargo" id="commMVCargo" value="{{ $ot -> commMVCargo != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> commMVCargo)) : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center text-danger font-weight-bold">Complete Discharge (MV)</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="datetime-local" name="compMVCargo" id="compMVCargo" value="{{ $ot -> compMVCargo != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> compMVCargo)) : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center text-danger font-weight-bold">Cast Off (MV)</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="datetime-local" name="cOffMVCargo" id="cOffMVCargo" value="{{ $ot -> cOffMVCargo != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> cOffMVCargo)) : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center text-danger font-weight-bold">Departure Time</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="datetime-local" name="departureTime" id="departureTime" value="{{ $ot -> departureTime != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> departureTime)) : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if($ot -> taskType == 'Non Operational')
                                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center text-danger font-weight-bold">Time Arrival</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="datetime-local" name="arrivalTime" id="arrivalTime" value="{{ $ot -> arrivalTime != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> arrivalTime)) : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center text-danger font-weight-bold">Start Docking</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="datetime-local" name="startDocking" id="startDocking" value="{{ $ot -> startDocking != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> startDocking)) : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center text-danger font-weight-bold">Finish Docking</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="datetime-local" name="finishDocking" id="finishDocking" value="{{ $ot -> finishDocking != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> finishDocking)) : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center text-danger font-weight-bold">Departure To POL</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="datetime-local" name="departurePOL" id="departurePOL" value="{{ $ot -> departurePOL != NULL ? date('Y-m-d\TH:i:s', strtotime($ot -> departurePOL)) : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- <div class="d-flex justify-content-center mt-5 mb-5">
                                        <button class="btn btn-info" type="submit">Update</button>
                                    </div> --}}

                                </div>
                                <div style="width: 30%; margin-top: 7vh">
                                    <div class="d-flex justify-content-center">
                                        <div class="form-group col-md-12">
                                            <label class="text-danger font-weight-bold" for="condition">Condition : </label>
                                            {{-- <input type="text" class="form-control" name="condition" id="" placeholder="Input Condition ..."> --}}
                                            <select class="form-select" name="condition" id="condition" required>
                                                <option disabled>Choose</option>
                                                @if($ot -> taskType == 'Non Operational')
                                                    <option value="Docking" {{ $ot -> condition == 'Docking' ? 'selected' : '' }}>Docking</option>
                                                    <option value="Standby Docking" {{ $ot -> condition == 'Standby Docking' ? 'selected' : '' }}>Standby Docking</option>
                                                @else
                                                    <option value="On Sailing" {{ $ot -> condition == 'On Sailing' ? 'selected' : '' }}>On Sailing</option>
                                                    <option value="Loading Activity" {{ $ot -> condition == 'Loading Activity' ? 'selected' : '' }}>Loading Activity</option>
                                                    <option value="Discharge Activity" {{ $ot -> condition == 'Discharge Activity' ? 'selected' : '' }}>Discharge Activity</option>
                                                    <option value="Standby" {{ $ot -> condition == 'Standby' ? 'selected' : '' }}>Standby</option>
                                                    <option value="Repair" {{ $ot -> condition == 'Repair' ? 'selected' : '' }}>Repair</option>
                                                    <option value="Grounded Barge" {{ $ot -> condition == 'Grounded Barge' ? 'selected' : '' }}>Grounded Barge</option>
                                                    <option value="Waiting Schedule" {{ $ot -> condition == 'Waiting Schedule' ? 'selected' : '' }}>Waiting Schedule</option>
                                                @endif
                                            </select>
                                        </div>

                                    </div>
                                    @if($ot -> taskType != 'Non Operational')
                                        <div class="d-flex justify-content-around mt-3">
                                            <div class="form-group col-md-6">
                                                <label class="text-danger font-weight-bold" for="estimatedTime">Estimasi (dalam hari) : </label>
                                                <input type="text" class="form-control" name="estimatedTime" id="" placeholder="Input Estimasi Dalam Hari ..." value="{{ $ot -> estimatedTime }}">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="text-danger font-weight-bold" for="cargoAmountEnd">Jumlah Kargo : </label>
                                                @if($ot -> taskType != 'Return Cargo')
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" name="cargoAmountEnd" min="1" step="0.001" id="" placeholder="Input Jumlah Kargo Dalam Ton..." value="{{ $ot -> cargoAmountEnd }}">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">Ton</div>
                                                        </div>
                                                    </div>
                                                @elseif($ot -> taskType == 'Return Cargo')
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" name="cargoAmountEndCargo" min="1" step="0.001" id="" placeholder="Input Jumlah Kargo Dalam Ton..." value="{{ $ot -> cargoAmountEndCargo }}">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">Ton</div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="form-group col-md-6">
                                            <label class="text-danger font-weight-bold" for="estimatedTime">Estimasi (dalam hari) : </label>
                                            <input type="text" class="form-control" name="estimatedTime" id="" placeholder="Input Estimasi Dalam Hari ..." value="{{ $ot -> estimatedTime }}">
                                        </div>
                                    @endif

                                    @if($ot -> taskType == 'Operational Shipment')
                                        <div class="d-flex justify-content-center">
                                            <div class="form-group col-md-12">
                                                <label class="text-danger font-weight-bold" for="customer">Customer : </label>
                                                <input type="text" class="form-control" name="customer" id="" placeholder="Input Customer ..." value="{{ $ot -> customer }}">
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="d-flex justify-content-center mt-3">
                                        <div class="form-group col-md-12">
                                            <label class="text-danger font-weight-bold" for="description">Deskripsi : </label>
                                            <br>
                                            <textarea name="description" style="width: 100%" rows="10" placeholder="Input Deskripsi ...">{{ $ot -> description }}</textarea>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button class="btn btn-info" type="submit" style="width: 90%">Update</button>
                                    </div>
                                    <div class="d-flex justify-content-center mt-3">
                                        <button class="btn btn-primary" type="button" data-toggle="modal" id="finalize" data-target="#finalize-{{ $ot -> id }}" style="width: 90%">Finalize</button>
                                    </div>
                                    <div class="d-flex justify-content-around mt-3">
                                        <button class="btn btn-danger" type="button" data-toggle="modal" id="cancel" data-target="#cancel-{{ $ot -> id }}" style="width: 40%">Cancel Task</button>
                                        @if($ot -> taskType == 'Operational Transhipment')
                                            <button class="btn btn-warning text-white" type="button" data-toggle="modal" id="return cargo" data-target="#return-cargo-{{ $ot -> id }}" style="width: 40%">Continue Return Cargo</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>

                        {{-- Modal Cancel Task --}}
                        <div class="modal fade" id="cancel-{{ $ot -> id }}" tabindex="-1" role="dialog" aria-labelledby="updateTitle"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h5 class="modal-title" id="updateTitle" style="color: white">Cancel Task</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body"> 
                                        <div class="d-flex flex-column justify-content-center align-items-center">
                                            <span class="text-danger" data-feather="alert-circle" style="height: 15%; width: 15%;"></span>
                                            <h5 class="font-weight-bold mt-3">Are You Sure To Cancel This Task ?</h5>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <form action="/crew/ongoing-task" method="POST">
                                            @csrf
                                            @method('delete')
                                            <input type="hidden" name="taskId" value="{{ $ot -> id }}">
                                            <input type="hidden" name="tugName" value="{{ $ot -> tugName }}">
                                            <input type="hidden" name="bargeName" value="{{ $ot -> bargeName }}">

                                            <button type="button" class="btn btn-danger mr-3" data-dismiss="modal" aria-label="Close">No</button>
                                            <button type="submit" class="btn btn-primary" href="">Yes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($ot -> taskType == 'Operational Transhipment')
                            {{-- Modal Return Cargo --}}
                            <div class="modal fade" id="return-cargo-{{ $ot -> id }}" tabindex="-1" role="dialog" aria-labelledby="updateTitle"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <h5 class="modal-title" id="updateTitle" style="color: white">Continue To Return Cargo</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body"> 
                                            <div class="d-flex flex-column justify-content-center align-items-center">
                                                <span class="text-info" data-feather="arrow-right" style="height: 15%; width: 15%;"></span>
                                                <h5 class="font-weight-bold mt-3">Are You Sure Want To Continue To Return Cargo ?</h5>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <form action="/crew/ongoing-task/return-cargo" method="POST">
                                                @csrf
                                                @method('patch')
                                                <input type="hidden" name="taskId" value="{{ $ot -> id }}">
                                                <input type="hidden" name="taskType" value="{{ $ot -> taskType }}">
                                                <input type="hidden" name="cabang" value="{{ $ot -> user -> cabang }}">

                                                <button type="button" class="btn btn-danger mr-3" data-dismiss="modal" aria-label="Close">No</button>
                                                <button type="submit" class="btn btn-primary" href="">Yes</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
    
                        {{-- Modal Finalize Task --}}
                        <div class="modal fade" id="finalize-{{ $ot -> id }}" tabindex="-1" role="dialog" aria-labelledby="finalizeTitle"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h5 class="modal-title" id="finalizeTitle" style="color: white">Finalize Task</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body"> 
                                        <div class="d-flex flex-column justify-content-center align-items-center">
                                            <span class="text-success" data-feather="check-circle" style="height: 15%; width: 15%;"></span>
                                            <h5 class="font-weight-bold mt-3">Are You Sure To Finalize This Task ?</h5>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <form action="/crew/ongoing-task" method="POST">
                                            @csrf
                                            @method('patch')
                                            <input type="hidden" name="tugName" value="{{ $ot -> tugName }}">
                                            <input type="hidden" name="bargeName" value="{{ $ot -> bargeName }}">

                                            <input type="hidden" name="taskId" value="{{ $ot -> id }}">
                                            <button type="button" class="btn btn-danger mr-3" data-dismiss="modal" aria-label="Close">No</button>
                                            <button type="submit" class="btn btn-primary" href="">Yes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach


                </div>
            </main>
            
        </div>
        
        <style>
            .page-bg{
                /* background-color: rgba(246,243,245,0.8); */
                background-color: rgb(231,233,235);
                border-radius: 10px;
            }
            .form-control, textarea{
                border: solid 1px #A01D23;
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