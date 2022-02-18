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
                    if(strpos($_SERVER['REQUEST_URI'], 'form-ap') !== false){
                        echo('active');
                    }
                @endphp" href="{{ Route('purchasingManager.formApPage') }}">
                    <span data-feather="file"></span>
                    Checklist AP
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link 
                @php
                    if(strpos($_SERVER['REQUEST_URI'], 'checklist-pr') !== false){
                        echo('active');
                    }
                @endphp" href="{{ Route('purchasingManager.checklistPrPage') }}">
                    <span data-feather="folder-plus"></span>
                    Checklist PR
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link 
                @php
                    if(strpos($_SERVER['REQUEST_URI'], 'report-po') !== false){
                        echo('active');
                    }
                @endphp" href="{{ Route('purchasingManager.reportPoPage') }}">
                    <span data-feather="bookmark"></span>
                    Report PO
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link 
                @php
                    if(strpos($_SERVER['REQUEST_URI'], 'report-ap') !== false){
                        echo('active');
                    }
                @endphp" href="/purchasing-manager/report-ap">
                    <span data-feather="bookmark"></span>
                    Report AP
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link 
                @php
                    if(strpos($_SERVER['REQUEST_URI'], '/report-JO') !== false){
                        echo('active');
                    }
                @endphp" href="/purchasing-manager/report-JO">
                    <span data-feather="bookmark"></span>
                    Report JO
                </a>
            </li>
        </ul>
    </div>
</nav>