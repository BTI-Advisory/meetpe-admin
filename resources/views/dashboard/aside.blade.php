<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 "
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0">
            <img src="{{ asset('img/logo-ct-dark.png') }}" class="navbar-brand-img h-100" alt="main_logo">
            <span class="ms-1 font-weight-bold " style="color: #FF4C00!important">MeetPe</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  " style="height: 100%" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link {{ Route::is('getGuide') ? 'active' : '' }}" href="{{ route('getGuide') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1 ">Guides</span>
                </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link {{ Route::is('getGuideExperiences') ? 'active' : '' }}" href="{{ route('getGuideExperiences') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-circle-08 text-warning text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1 ">En cours de vérification</span>
                </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link {{ Route::is('getNonCompleted') ? 'active' : '' }}" href="{{ route('getNonCompleted') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-check-bold text-warning text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1 "> À compléter</span>
                </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link {{ Route::is('getOnlineExperiences') ? 'active' : '' }}" href="{{ route('getOnlineExperiences') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-world-2 text-warning text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1 ">En ligne</span>
                </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link {{ Route::is('getRejectedGuidExperiences') ? 'active' : '' }}" href="{{ route('getRejectedGuidExperiences') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-fat-remove text-warning text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1 "> Refuser</span>
                </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link {{ Route::is('another_document') ? 'active' : '' }}" href="{{ route('another_document') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-book-bookmark text-warning text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1 "> Autre Document</span>
                </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link {{ Route::is('getArchivedGuidExperiences') ? 'active' : '' }}" href="{{ route('getArchivedGuidExperiences') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-books text-warning text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1 "> archive</span>
                </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link {{ Route::is('getHorsLigneGuidExperiences') ? 'active' : '' }}" href="{{ route('getHorsLigneGuidExperiences') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-curved-next text-warning text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1 "> hors ligne</span>
                </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link {{ Route::is('getDeletedExperiences') ? 'active' : '' }}" href="{{ route('getDeletedExperiences') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-ambulance text-warning text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1 "> Supprimée</span>
                </a>
            </li>

            <li class="nav-item ">
                <a class="nav-link {{ Route::is('getAllreservations') ? 'active' : '' }}" href="{{ route('getAllreservations') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-bullet-list-67 text-warning text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1 "> Les réservations</span>
                </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link {{ Route::is('getAllVoyageurs') ? 'active' : '' }}" href="{{ route('getAllVoyageurs') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-bullet-list-67 text-warning text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1 "> Les Voyageurs</span>
                </a>
            </li>

            {{--         <li class="nav-item">
          <a class="nav-link " href="../pages/profile.html">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Profile</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link " href="../pages/sign-in.html">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="ni ni-single-copy-04 text-warning text-sm opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Sign In</span>
          </a>
        </li> --}}
        </ul>
    </div>
</aside>
