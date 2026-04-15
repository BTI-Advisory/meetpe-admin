<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
    data-scroll="false">
    <div class="container-fluid py-1 px-3">
        <div class="d-flex align-items-center">
            <h6 class="font-weight-bolder text-white mb-0">Dashboard</h6>
            <nav aria-label="breadcrumb" class="ms-3">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
                    <!-- Breadcrumb items here if needed -->
                </ol>
            </nav>
        </div>
        <div class="collapse navbar-collapse mt-2 mt-lg-0 ms-auto" id="navbar">
            <form class="me-auto mb-2 mb-lg-0">
                <div class="input-group">
                    <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                    <input type="text" class="form-control" placeholder="Search...">
                </div>
            </form>
            <ul class="navbar-nav justify-content-end flex-row align-items-center">
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-link text-white"><i
                                class="ni ni-button-power text-white text-sm opacity-10"></i> Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- End Navbar -->
