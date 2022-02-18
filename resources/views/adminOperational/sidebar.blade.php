<nav id="sidebarMenu" class="col-md-3 col-lg-2 col-sm-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link 
                @php
                    //strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false
                    if(strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false){
                        echo('active');
                    }
                @endphp" aria-current="page" href="/dashboard">
                    <span data-feather="home"></span>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link 
                @php
                    if(strpos($_SERVER['REQUEST_URI'], 'daily-reports') !== false){
                        echo('active');
                    }
                @endphp" href="{{ Route('adminOperational.reportTranshipment') }}">
                    <span data-feather="file-text"></span>
                    Daily Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link
                @php
                    if(strpos($_SERVER['REQUEST_URI'], 'monitoring') !== false){
                        echo('active');
                    }
                @endphp
                " href="{{ Route('adminOperational.monitoring') }}">
                    <span data-feather="monitor"></span>
                    Monitoring
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link 
                @php
                    if(strpos($_SERVER['REQUEST_URI'], 'add-tugboat') !== false){
                        echo('active');
                    }
                @endphp" href="{{ Route('adminOperational.addTugboat') }}">
                    <span data-feather="plus-square"></span>
                    Add Tugboat
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link 
                @php
                    if(strpos($_SERVER['REQUEST_URI'], 'add-barge') !== false){
                        echo('active');
                    }
                @endphp" href="{{ Route('adminOperational.addBarge') }}">
                    <span data-feather="plus-square"></span>
                    Add Barge
                </a>
            </li>
        </ul>
    </div>
</nav>