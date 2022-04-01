@if(Auth::user()->hasRole('adminOperational') or Auth::user()->hasRole('StaffOperasional'))
    @extends('../layouts.base')

    @section('title', 'Admin Operational Dashboard')

    @section('container')
    <div class="row">
        @include('adminOperational.sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            @include('../layouts/time')            

            <h1 class="mt-4 mb-5" style="text-align: center">Dashboard</h1>

            <div class="d-flex justify-content-around smaller-size" id="content">
                <div class="d-flex flex-wrap" style="width: 50%; overflow-y: auto; max-height: 600px">
                    <div class="flip-card">
                        <div class="flip-card-inner">
                          <div class="flip-card-front">
                            <h5 class="text-warning mt-3 display-5">{{ $total_tugs }}</h5>
                            <h5 class="text-warning mt-3">Total Tugboat</h5>
                          </div>
                        </div>
                    </div>
                    <div class="flip-card">
                        <div class="flip-card-inner">
                          <div class="flip-card-front">
                            <h5 class="text-warning mt-3 display-5">{{ $total_barges }}</h5>
                            <h5 class="text-warning mt-3">Total Barges</h5>
                          </div>
                        </div>
                    </div>
                    <div class="flip-card">
                        <div class="flip-card-inner">
                          <div class="flip-card-front">
                            <h5 class="text-white mt-3 display-5">{{ $on_sailing_count }}</h5>
                            <h5 class="text-white mt-3">On Sailing</h5>
                          </div>
                        </div>
                    </div>
                    <div class="flip-card">
                        <div class="flip-card-inner">
                          <div class="flip-card-front">
                            <h5 class="text-white mt-3 display-5">{{ $loading_activity_count }}</h5>
                            <h5 class="text-white mt-3">Loading Activity</h5>
                          </div>
                        </div>
                    </div>
                    <div class="flip-card">
                        <div class="flip-card-inner">
                          <div class="flip-card-front">
                            <h1 class="text-white mt-3 display-5">{{ $discharge_activity_count }}</h1>
                            <h5 class="text-white mt-3">Discharge Activity</h5>
                          </div>
                        </div>
                    </div>
                    <div class="flip-card">
                      <div class="flip-card-inner">
                        <div class="flip-card-front">
                          <h1 class="text-white mt-3 display-5">{{ $standby_count }}</h1>
                          <h5 class="text-white mt-3">Standby</h5>
                        </div>
                      </div>
                    </div>
                    <div class="flip-card">
                        <div class="flip-card-inner">
                          <div class="flip-card-front">
                            <h1 class="text-white mt-3 display-5">{{ $repair_count }}</h1>
                            <h5 class="text-white mt-3">Repair</h5>
                          </div>
                        </div>
                    </div>
                    <div class="flip-card">
                        <div class="flip-card-inner">
                          <div class="flip-card-front">
                            <h1 class="text-white mt-3 display-5">{{ $tug_docking_count }}</h1>
                            <h5 class="text-white mt-3">Tug Docking</h5>
                          </div>
                        </div>
                    </div>
                    <div class="flip-card">
                        <div class="flip-card-inner">
                          <div class="flip-card-front">
                            <h1 class="text-white mt-3 display-5">{{ $barge_docking_count }}</h1>
                            <h5 class="text-white mt-3">Barge Docking</h5>
                          </div>
                        </div>
                    </div>
                    <div class="flip-card">
                      <div class="flip-card-inner">
                        <div class="flip-card-front">
                          <h1 class="text-white mt-3 display-5">{{ $tug_standby_docking_count }}</h1>
                          <h5 class="text-white mt-3">Tug Standby Docking</h5>
                        </div>
                      </div>
                    </div>
                    <div class="flip-card">
                      <div class="flip-card-inner">
                        <div class="flip-card-front">
                          <h1 class="text-white mt-3 display-5">{{ $barge_standby_docking_count }}</h1>
                          <h5 class="text-white mt-3">Barge Standby Docking</h5>
                        </div>
                      </div>
                    </div>
                    <div class="flip-card">
                        <div class="flip-card-inner">
                          <div class="flip-card-front">
                            <h1 class="text-white mt-3 display-5">{{ $grounded_barge_count }}</h1>
                            <h5 class="text-white mt-3">Grounded Barge</h5>
                          </div>
                        </div>
                    </div>
                    <div class="flip-card">
                        <div class="flip-card-inner">
                          <div class="flip-card-front">
                            <h1 class="text-white mt-3 display-5">{{ $waiting_schedule_count }}</h1>
                            <h5 class="text-white mt-3">Waiting Schedule</h5>
                          </div>
                        </div>
                    </div>
                </div>
                <div class="right-section mt-3 ml-3">
                    <div onclick="redirectToMonitoring()" class="jumbotron jumbotron-fluid mx-2">
                        <div class="container mt-2">
                          <p class=" text-wrap font-weight-bold text-center" style="font-size: 2vw">Percentage Ship's Activity : </p>
                          <p class="font-weight-bold text-center text-success" style="font-size: 2vw">
                            {{ $percentage_ship_activity }}%
                          </p>
                          <p class=" text-wrap font-weight-bold text-center mt-3" style="font-size: 2vw">Total Lost Time : </p>
                          <p class=" text-wrap text-center text-secondary font-weight-bold" style="font-size: 2vw">{{ $total_lost_time }} Days</p>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <style>
      h5{
        font-size: 17px;
      }
      th{
          color: white;
      }
      td, th{
          word-wrap: break-word;
          min-width: 120px;
          max-width: 120px;
          text-align: center;
      }
      .alert{
          text-align: center;
      }
      .modal-backdrop {
          height: 100%;
          width: 100%;
      }
      .feather-100{
          margin-top: 10px;
          width: 100px;
          height: 100px;
      }
      .jumbotron{
          background-color: #A4363A;
          color: white;
          border: 8px solid black;
          border-radius: 50%;
          height: 50vh;
          width: 50vh;
      }
      /* The flip card container - set the width and height to whatever you want. We have added the border property to demonstrate that the flip itself goes out of the box on hover (remove perspective if you don't want the 3D effect */
      .flip-card {
          margin: 20px;
          background-color: transparent;
          width: 180px;
          height: 180px;
          /* perspective: 1000px; */
      }

      /* This container is needed to position the front and back side */
      .flip-card-inner {
          border-radius: 50%;
          /* border: 8px solid black; */
          border: 8px solid #A01D23;
          position: relative;
          width: 100%;
          height: 100%;
          text-align: center;
      }

      /* Position the front and back side */
      .flip-card-front{
        position: absolute;
        border-radius: 50%;
        width: 100%;
        height: 100%;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
      }

      .flip-card-front {
        background-color: #A01D23;      
      }

      .text-white mt-3 display-2{
        font-size: 64px;
      }
      @media only screen and (max-width: 960px) {
          .smaller-size {
              flex-direction: column;
              justify-content: center;
              align-items: center;
          }
          .right-section{
              margin: 20px;
              margin-left: -30%;
          }
          .jumbotron{
              margin: 10px;
              height: 40vh;
              width: 40vh;
          }
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

    <script type="text/javascript">
        function redirectToMonitoring(){
          window.location.href = "{{ route('adminOperational.lostTimeDetails')}}";
        }
    </script>

    @endsection
@else
    @include('../layouts/notAuthorized')
@endif