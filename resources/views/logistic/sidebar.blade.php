<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                    <a class="nav-link 
                    @php
                        if(strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false || strpos($_SERVER['REQUEST_URI'], '/logistic/completed-order') !== false || strpos($_SERVER['REQUEST_URI'], '/logistic/in-progress-order') !== false){
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
                        if(basename($_SERVER['REQUEST_URI']) == 'Job_Request_List'){
                            echo('active');
                        }
                    @endphp" href="{{ Route("logistic.Job_Request_List") }}">
                    <span data-feather="align-justify"></span>
                    Job Request Order List
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link 
                    @php
                        if(strpos($_SERVER['REQUEST_URI'], 'stocks') !== false){
                            echo('active');
                        }
                    @endphp" href="{{ Route("logistic.stocks") }}">
                    <span data-feather="layers"></span>
                    Item Stocks
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link 
                    @php
                        if(basename($_SERVER['REQUEST_URI']) == 'make-order'){
                            echo('active');
                        }
                    @endphp" href="{{ Route("logistic.makeOrder") }}">
                    <span data-feather="plus-circle"></span>
                    Make Order Goods
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link 
                    @php
                        if(basename($_SERVER['REQUEST_URI']) == 'request-do'){
                            echo('active');
                        }
                    @endphp" href="{{ Route("logistic.requestDo") }}">
                    <span data-feather="shopping-bag"></span>
                    Request DO
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link 
                    @php
                        if(basename($_SERVER['REQUEST_URI']) == 'history-out' || basename($_SERVER['REQUEST_URI']) == 'history-in'){
                            echo('active');
                        }
                    @endphp" href="{{ Route("logistic.historyOut") }}">
                    <span data-feather="file"></span>
                    Report Goods In/Out
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link 
                    @php
                        if(basename($_SERVER['REQUEST_URI']) == 'report'){
                            echo('active');
                        }
                    @endphp" href="{{ Route("logistic.report") }}">
                    <span data-feather="archive"></span>
                    Report PR/PO
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link 
                    @php
                        if(basename($_SERVER['REQUEST_URI']) == 'report_JR_Page'){
                            echo('active');
                        }
                    @endphp" href="{{ Route("logistic.report_JR_Page") }}">
                    <span data-feather="archive"></span>
                    Report JR
                </a>
            </li>
        </ul>
    </div>
</nav>
