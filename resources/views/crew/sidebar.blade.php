<nav id="sidebarMenu" class="col-md-3 col-lg-2 col-sm-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link 
                @php
                    if(strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false || strpos($_SERVER['REQUEST_URI'], 'completed-order') !== false || strpos($_SERVER['REQUEST_URI'], 'in-progress-order') !== false){
                        echo('active');
                    }
                @endphp" aria-current="page" href="/dashboard">
                    <span data-feather="home"></span>
                    Dashboard
                </a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link 
                @php
                    if(basename($_SERVER['REQUEST_URI']) == 'Job_Request_List'){
                        echo('active');
                    }
                @endphp" aria-current="page" href="crew/Job_Request_List">
                    <span data-feather="align-justify"></span>
                    Job Request List
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link 
                @php
                    if(strpos($_SERVER['REQUEST_URI'], '/crew/order') !== false){
                        echo('active');
                    }
                @endphp" href="{{ Route('crew.order') }}">
                    <span data-feather="file"></span>
                    Make Order
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" 
                @php
                    if(basename($_SERVER['REQUEST_URI']) == 'makeJobRequest'){
                        echo('active');
                    }
                @endphp
                href="{{ Route('crew.makeJobRequest') }}">
                    <span data-feather="layout"></span>
                    Create Job Request
                </a>
            </li> -->
            <li class="nav-item">
                <a class="nav-link
                    @php
                        if(strpos($_SERVER['REQUEST_URI'], 'create-task') !== false){
                        echo('active');
                    }
                    @endphp
                "   
                href="{{ Route('crew.createTask') }}">
                    <span data-feather="plus-square"></span>
                    Create Task
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link
                @php
                    if(strpos($_SERVER['REQUEST_URI'], 'ongoing-task') !== false){
                    echo('active');
                }
                @endphp
                " href="{{ Route('crew.ongoingTaskPage') }}">
                    <span data-feather="clock"></span>
                    Ongoing Task
                </a>
            </li>
        </ul>
    </div>
</nav>