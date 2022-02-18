@if(empty($operationalData))
    <div class="d-flex justify-content-around">
        <div style="width: 60%;">
            <div class="d-flex justify-content-around">
                <div>
                    <h3 class="text-secondary font-weight-bold">Status</h3>
                    <h5 class="text-center">-</h5>
                </div>
                <div>
                    <h3 class="text-secondary font-weight-bold">Condition</h3>
                    <h5 class="text-center">-</h5>
                </div>
                <div>
                    <h3 class="text-secondary font-weight-bold">Jumlah Cargo</h3>
                    <h3 class="text-center">-</h3>
                </div>
                <div>
                    <h3 class="text-secondary font-weight-bold">Total Time</h3>
                    <h5 class="text-center">-</h5>
                </div>
            </div>
            <div class="card text-white bg-secondary mt-5 overflow-auto" style="width: 100%; height: 52vh; border-radius: 10px;">
                <div class="card-body">
                    <h3 class="text-white font-weight-bold">No Data Found.</h3>
                </div>
            </div>
        </div>
        <div style="width: 30%;">
            <h3 class="text-danger font-weight-bold mb-4">Deskripsi</h3>
            <textarea name="" class="w-100 h-75" style="border-radius: 10px" disabled>-</textarea>
        </div>
    </div>
@else
    <div class="d-flex justify-content-around">
        <div style="width: 60%;">

            @if($operationalData -> taskType != 'Non Operational')
                <div class="d-flex justify-content-between">
                    <div class="d-flex flex-column">
                        <h3 class="text-secondary font-weight-bold">Status</h3>
                        <h5 class="text-center">{{ $operationalData -> taskType }}</h5>
                    </div>
                    <div class="d-flex flex-column">
                        <h3 class="text-secondary font-weight-bold">Condition</h3>
                        <h5 class="text-center">{{ $operationalData -> condition }}</h5>
                    </div>
                    <div class="d-flex flex-column mr-4 ml-2">
                        <h3 class="text-secondary font-weight-bold">Jumlah Kargo Akhir</h3>
                        <h5 class="text-center">{{ $operationalData -> cargoAmountEnd }}</h5>
                    </div>
                    @if($operationalData -> taskType == 'Operational Shipment')
                        <div class="d-flex flex-column mr-5">
                            <h3 class="text-secondary font-weight-bold">Total Time</h3>
                            <h5 class="text-center">{{ !empty($operationalData -> totalTime) ? $operationalData -> totalTime : 'n/a' }}</h5>
                        </div>
                    @endif
                </div>
            @else
                <div class="d-flex justify-content-around">
                    <div class="d-flex flex-column">
                        <h3 class="text-secondary font-weight-bold">Status</h3>
                        <h5 class="text-center">{{ $operationalData -> taskType }}</h5>
                    </div>
                    <div class="d-flex flex-column">
                        <h3 class="text-secondary font-weight-bold">Condition</h3>
                        <h5 class="text-center">{{ $operationalData -> condition }}</h5>
                    </div>
                </div>
            @endif
            
            <div class="card text-white bg-secondary mt-5 overflow-auto" style="width: 100%; height: 52vh; border-radius: 10px;">
                <div class="card-body">
                    <div class="d-flex flex-wrap mt-2">
                        @if($operationalData -> taskType == 'Operational Shipment')
                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                <div class="card-body">
                                    <h5 class="card-title text-center text-danger font-weight-bold">Time Arrival</h5>
                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                    <div class="form-group mt-3">
                                        <input disabled disabled class="form-control" type="datetime-local" name="arrivalTime" id="arrivalTime" value="{{ $operationalData -> arrivalTime != NULL ? date('Y-m-d\TH:i:s', strtotime($operationalData -> arrivalTime)) : 'n/a' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                <div class="card-body">
                                    <h5 class="card-title text-center text-danger font-weight-bold">Start Aside (L)</h5>
                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                    <div class="form-group mt-3">
                                        <input disabled class="form-control" type="datetime-local" name="startAsideL" id="startAsideL" value="{{ $operationalData -> startAsideL != NULL ? date('Y-m-d\TH:i:s', strtotime($operationalData -> startAsideL)) : 'n/a' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                <div class="card-body">
                                    <h5 class="card-title text-center text-danger font-weight-bold">Aside (L)</h5>
                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                    <div class="form-group mt-3">
                                        <input disabled class="form-control" type="datetime-local" name="asideL" id="asideL" value="{{ $operationalData -> asideL != NULL ? date('Y-m-d\TH:i:s', strtotime($operationalData -> asideL)) : 'n/a' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                <div class="card-body">
                                    <h5 class="card-title text-center text-danger font-weight-bold">Commence Load (L)</h5>
                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                    <div class="form-group mt-3">
                                        <input disabled class="form-control" type="datetime-local" name="commenceLoadL" id="commenceLoadL" value="{{ $operationalData -> commenceLoadL != NULL ? date('Y-m-d\TH:i:s', strtotime($operationalData -> commenceLoadL)) : 'n/a' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                <div class="card-body">
                                    <h5 class="card-title text-center text-danger font-weight-bold">Completed Loading (L)</h5>
                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                    <div class="form-group mt-3">
                                        <input disabled class="form-control" type="datetime-local" name="completedLoadingL" id="completedLoadingL" value="{{ $operationalData -> completedLoadingL != NULL ? date('Y-m-d\TH:i:s', strtotime($operationalData -> completedLoadingL)) : 'n/a' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                <div class="card-body">
                                    <h5 class="card-title text-center text-danger font-weight-bold">Cast Off (L)</h5>
                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                    <div class="form-group mt-3">
                                        <input disabled class="form-control" type="datetime-local" name="cOffL" id="cOffL" value="{{ $operationalData -> cOffL != NULL ? date('Y-m-d\TH:i:s', strtotime($operationalData -> cOffL)) : 'n/a' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                <div class="card-body">
                                    <h5 class="card-title text-center text-danger font-weight-bold">Doc Overhand</h5>
                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                    <div class="form-group mt-3">
                                        <input disabled class="form-control" type="datetime-local" name="DOH" id="DOH" value="{{ $operationalData -> DOH != NULL ? date('Y-m-d\TH:i:s', strtotime($operationalData -> DOH)) : 'n/a' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                <div class="card-body">
                                    <h5 class="card-title text-center text-danger font-weight-bold">Doc On Boat</h5>
                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                    <div class="form-group mt-3">
                                        <input disabled class="form-control" type="datetime-local" name="DOB" id="DOB" value="{{ $operationalData -> DOB != NULL ? date('Y-m-d\TH:i:s', strtotime($operationalData -> DOB)) : 'n/a' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                <div class="card-body">
                                    <h5 class="card-title text-center text-danger font-weight-bold">Departure to (POD)</h5>
                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                    <div class="form-group mt-3">
                                        <input disabled class="form-control" type="datetime-local" name="departurePOD" id="departurePOD" value="{{ $operationalData -> departurePOD != NULL ? date('Y-m-d\TH:i:s', strtotime($operationalData -> departurePOD)) : 'n/a' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                <div class="card-body">
                                    <h5 class="card-title text-center text-danger font-weight-bold">Arrival (POD)</h5>
                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                    <div class="form-group mt-3">
                                        <input disabled class="form-control" type="datetime-local" name="arrivalPOD" id="arrivalPOD" value="{{ $operationalData -> arrivalPOD != NULL ? date('Y-m-d\TH:i:s', strtotime($operationalData -> arrivalPOD)) : 'n/a' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                <div class="card-body">
                                    <h5 class="card-title text-center text-danger font-weight-bold">Start Aside (POD)</h5>
                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                    <div class="form-group mt-3">
                                        <input disabled class="form-control" type="datetime-local" name="startAsidePOD" id="startAsidePOD" value="{{ $operationalData -> startAsidePOD != NULL ? date('Y-m-d\TH:i:s', strtotime($operationalData -> startAsidePOD)) : 'n/a' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                <div class="card-body">
                                    <h5 class="card-title text-center text-danger font-weight-bold">Aside (POD)</h5>
                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                    <div class="form-group mt-3">
                                        <input disabled class="form-control" type="datetime-local" name="asidePOD" id="asidePOD" value="{{ $operationalData -> asidePOD != NULL ? date('Y-m-d\TH:i:s', strtotime($operationalData -> asidePOD)) : 'n/a' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                <div class="card-body">
                                    <h5 class="card-title text-center text-danger font-weight-bold">Commence Load (POD)</h5>
                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                    <div class="form-group mt-3">
                                        <input disabled class="form-control" type="datetime-local" name="commenceLoadPOD" id="commenceLoadPOD" value="{{ $operationalData -> commenceLoadPOD != NULL ? date('Y-m-d\TH:i:s', strtotime($operationalData -> commenceLoadPOD)) : 'n/a' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                <div class="card-body">
                                    <h5 class="card-title text-center text-danger font-weight-bold">Completed Loading (POD)</h5>
                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                    <div class="form-group mt-3">
                                        <input disabled class="form-control" type="datetime-local" name="completedLoadingPOD" id="completedLoadingPOD" value="{{ $operationalData -> completedLoadingPOD != NULL ? date('Y-m-d\TH:i:s', strtotime($operationalData -> completedLoadingPOD)) : 'n/a' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                <div class="card-body">
                                    <h5 class="card-title text-center text-danger font-weight-bold">Cast Off (POD)</h5>
                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                    <div class="form-group mt-3">
                                        <input disabled class="form-control" type="datetime-local" name="cOffPOD" id="cOffPOD" value="{{ $operationalData -> cOffPOD != NULL ? date('Y-m-d\TH:i:s', strtotime($operationalData -> cOffPOD)) : 'n/a' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                <div class="card-body">
                                    <h5 class="card-title text-center text-danger font-weight-bold">Doc On Boat (POD)</h5>
                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                    <div class="form-group mt-3">
                                        <input disabled class="form-control" type="datetime-local" name="DOBPOD" id="DOBPOD" value="{{ $operationalData -> DOBPOD != NULL ? date('Y-m-d\TH:i:s', strtotime($operationalData -> DOBPOD)) : 'n/a' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="card border-dark mx-3 mb-3" style="width: 17rem;">
                                <div class="card-body">
                                    <h5 class="card-title text-center text-danger font-weight-bold">Departure Time</h5>
                                    <h6 class="card-subtitle mb-2 text-muted text-center text-center font-weight-bold">Start Date & Time</h6>
                                    <div class="form-group mt-3">
                                        <input disabled class="form-control" type="datetime-local" name="departureTime" id="departureTime" value="{{ $operationalData -> departureTime != NULL ? date('Y-m-d\TH:i:s', strtotime($operationalData -> departureTime)) : 'n/a' }}">
                                    </div>
                                </div>
                            </div>
                        @elseif($operationalData -> taskType == 'Operational Transhipment')
                            <div class="card mx-3 my-3 text-white bg-dark card-styling" style="width: 17rem; height: 15vh;">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Sailing to Jetty</h5>
                                    <h6 class="card-subtitle mb-2 mt-2 text-center">
                                        {{ $operationalData -> sailingToJetty != NULL ? $operationalData -> sailingToJetty . ' Hours': 'n/a' }}
                                    </h6>
                                </div>
                            </div>
                            <div class="card mx-3 my-3 text-white bg-dark card-styling" style="width: 17rem; height: 15vh;">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Ldg Time</h5>
                                    <h6 class="card-subtitle mb-2 mt-2 text-center"><span class="df-clock mr-2" data-feather="clock"></span>{{ $operationalData -> ldgTime != NULL ? $operationalData -> ldgTime . ' Hours' : 'n/a' }}</h6>
                                </div>
                            </div>
                            <div class="card mx-3 my-3 text-white bg-dark card-styling" style="width: 17rem; height: 15vh;">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Ldg Rate</h5>
                                    <h6 class="card-subtitle mb-2 mt-2 text-center"><span class="df-clock mr-2" data-feather="clock"></span>{{ $operationalData -> ldgRate != 0 ? number_format($operationalData -> ldgRate, 2, '.', ',') : 'n/a' }}</h6>
                                </div>
                            </div>
                            <div class="card mx-3 my-3 text-white bg-dark card-styling" style="width: 17rem; height: 15vh;">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Unberthing</h5>
                                    <h6 class="card-subtitle mb-2 mt-2 text-center"><span class="df-clock mr-2" data-feather="clock"></span>{{ $operationalData -> unberthing != NULL ? $operationalData -> unberthing . ' Hours' : 'n/a' }}</h6>
                                </div>
                            </div>
                            <div class="card mx-3 my-3 text-white bg-dark card-styling" style="width: 17rem; height: 15vh;">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Sailing to MV</h5>
                                    <h6 class="card-subtitle mb-2 mt-2 text-center"><span class="df-clock mr-2" data-feather="clock"></span>{{ $operationalData -> sailingToMV != NULL ? $operationalData -> sailingToMV . ' Hours' : 'n/a' }}</h6>
                                </div>
                            </div>
                            <div class="card mx-3 my-3 text-white bg-dark card-styling" style="width: 17rem; height: 15vh;">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Maneuver</h5>
                                    <h6 class="card-subtitle mb-2 mt-2 text-center"><span class="df-clock mr-2" data-feather="clock"></span>{{ $operationalData -> maneuver != NULL ? $operationalData -> maneuver . ' Hours' : 'n/a' }}</h6>
                                </div>
                            </div>
                            <div class="card mx-3 my-3 text-white bg-dark card-styling" style="width: 17rem; height: 15vh;">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Disch Time</h5>
                                    <h6 class="card-subtitle mb-2 mt-2 text-center"><span class="df-clock mr-2" data-feather="clock"></span>{{ $operationalData -> dischTime != NULL ? $operationalData -> dischTime . ' Hours' : 'n/a' }}</h6>
                                </div>
                            </div>
                            <div class="card mx-3 my-3 text-white bg-dark card-styling" style="width: 17rem; height: 15vh;">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Disch Rate</h5>
                                    <h6 class="card-subtitle mb-2 mt-2 text-center"><span class="df-clock mr-2" data-feather="clock"></span>{{ $operationalData -> dischRate != NULL ? $operationalData -> dischRate . ' Hours' : 'n/a' }}</h6>
                                </div>
                            </div>
                            <div class="card mx-3 my-3 text-white bg-dark card-styling" style="width: 17rem; height: 15vh;">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Cycle Time</h5>
                                    <h6 class="card-subtitle mb-2 mt-2 text-center"><span class="df-clock mr-2" data-feather="clock"></span>{{ $operationalData -> cycleTime != NULL ? $operationalData -> cycleTime . ' Hours' : 'n/a' }}</h6>
                                </div>
                            </div>
                        @elseif($operationalData -> taskType == 'Return Cargo')
                            <div class="card mx-3 my-3 text-white bg-dark card-styling" style="width: 17rem; height: 15vh;">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Sailing To MV</h5>
                                    <h6 class="card-subtitle mb-2 mt-2 text-center"><span class="df-clock mr-2" data-feather="clock"></span>{{ $operationalData -> sailingToMVCargo != NULL ? $operationalData -> sailingToMVCargo . ' Hours' : 'n/a' }}</h6>
                                </div>
                            </div>
                            <div class="card mx-3 my-3 text-white bg-dark card-styling" style="width: 17rem; height: 15vh;">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Maneuver</h5>
                                    <h6 class="card-subtitle mb-2 mt-2 text-center"><span class="df-clock mr-2" data-feather="clock"></span>{{ $operationalData -> maneuverCargo != NULL ? $operationalData -> maneuverCargo . ' Hours' : 'n/a' }}</h6>
                                </div>
                            </div>
                            <div class="card mx-3 my-3 text-white bg-dark card-styling" style="width: 17rem; height: 15vh;">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Disch Time</h5>
                                    <h6 class="card-subtitle mb-2 mt-2 text-center"><span class="df-clock mr-2" data-feather="clock"></span>{{ $operationalData -> dischTimeCargo != NULL ? $operationalData -> dischTimeCargo . ' Hours' : 'n/a' }}</h6>
                                </div>
                            </div>
                            <div class="card mx-3 my-3 text-white bg-dark card-styling" style="width: 17rem; height: 15vh;">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Disch Rate</h5>
                                    <h6 class="card-subtitle mb-2 mt-2 text-center"><span class="df-clock mr-2" data-feather="clock"></span>{{ $operationalData -> dischRateCargo != NULL ? $operationalData -> dischRateCargo . ' Hours' : 'n/a' }}</h6>
                                </div>
                            </div>
                            <div class="card mx-3 my-3 text-white bg-dark card-styling" style="width: 17rem; height: 15vh;">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Cycle Time</h5>
                                    <h6 class="card-subtitle mb-2 mt-2 text-center"><span class="df-clock mr-2" data-feather="clock"></span>{{ $operationalData -> cycleTimeCargo != NULL ? $operationalData -> cycleTimeCargo . ' Hours' : 'n/a' }}</h6>
                                </div>
                            </div>
                        @elseif($operationalData -> taskType == 'Non Operational')
                            <div class="card mx-3 my-3 text-white bg-dark card-styling" style="width: 17rem; height: 15vh;">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Total Lost Time</h5>
                                    <h6 class="card-subtitle mb-2 mt-2 text-center"><span class="df-clock mr-2" data-feather="clock"></span>{{ $operationalData -> totalLostDays != NULL ? $operationalData -> totalLostDays . ' Hours' : 'n/a' }}</h6>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div style="width: 30%;">
            <h5 class="text-danger font-weight-bold mb-4">Deskripsi</h5>
            <textarea name="" id="" class="w-100 h-75" style="border-radius: 10px" disabled>{{ $operationalData -> description }}</textarea>
        </div>
    </div>
@endif