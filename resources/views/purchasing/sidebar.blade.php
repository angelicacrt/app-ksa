<nav id="sidebarMenu" class="col-md-2 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link 
                @php
                    if(strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false || strpos($_SERVER['REQUEST_URI'], 'completed') !== false || strpos($_SERVER['REQUEST_URI'], 'in-progress') !== false){
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
                    if(strpos($_SERVER['REQUEST_URI'], '/Job_Request_List') !== false){
                        echo('active');
                    } 
                @endphp" href="/purchasing/Job_Request_List">
                    <span data-feather="align-justify"></span>
                    Review Job Request List
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link 
                @php
                    if($_SERVER['REQUEST_URI'] == '/purchasing/report' || strpos($_SERVER['REQUEST_URI'], '/purchasing/report/') !== false){
                        echo('active');
                    }
                @endphp" href="{{ Route('purchasing.report') }}">
                    <span data-feather="archive"></span>
                    Report PO
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link 
                @php
                    if(strpos($_SERVER['REQUEST_URI'], 'report-ap') !== false){
                        echo('active');
                    }
                @endphp" href="{{ Route('purchasing.reportAp') }}">
                    <span data-feather="bookmark"></span>
                    Report AP
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link 
                @php
                    if(strpos($_SERVER['REQUEST_URI'], 'report-JO') !== false){
                        echo('active');
                    }
                @endphp" href="{{ Route('purchasing.reportJO') }}">
                    <span data-feather="bookmark"></span>
                    Report JO
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link 
                @php
                    if(basename($_SERVER['REQUEST_URI']) == 'supplier'){
                        echo('active');
                    }
                @endphp" href="/purchasing/supplier">
                    <span data-feather="user-plus"></span>
                    Add Supplier Contact
                </a>
            </li>
        </ul>
    </div>
</nav>