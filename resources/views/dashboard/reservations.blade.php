@extends('dashboard.index')
@section('main')
    <main class="main-content position-relative border-radius-lg ">
        @include('dashboard.navbar')
        <style>
            audio::-webkit-media-controls-panel {
                background-color: #ff8d41;
            }
        </style>

        <div class="container-fluid py-4">
        <div class="row">
                <div class="col-12">
                    <a href="{{ route('export.reservations') }}" class="btn btn-success">
                        Exporter les reservations
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                           
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Créneau</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Experience</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Voyageur</th>                                        
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Guide</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Status</th>

                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Nombre des voyageurs</th>                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($reservations as $key => $reservation)
                                            <tr>
                                                
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ $reservation['date_time'] }}</p>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span
                                                        class="text-secondary text-xs font-weight-bold">{{ $reservation['experience']->title }}</span>
                                                </td>
                                            
                                                <td class="align-middle text-center text-sm">
                                                    <span
                                                        class="text-secondary text-xs font-weight-bold">{{ $reservation['voyageur']->name }}</span>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span
                                                        class="text-secondary text-xs font-weight-bold">{{ $reservation['experience']->user->name }}</span>
                                                </td>
                                               <td class="align-middle text-center text-sm">
                                               @php
                                                        $statusClass = '';
                                                        switch ($reservation['status']) {
                                                            case 'Acceptée':
                                                                $statusClass = 'bg-gradient-success';
                                                                break;
                                                            case 'En attente':
                                                                $statusClass = 'bg-gradient-info';
                                                                break;
                                                            case 'Annulée':
                                                                $statusClass = 'bg-gradient-secondary';
                                                                break;
                                                            case 'Refusée':
                                                                $statusClass = 'bg-gradient-warning';
                                                                break;
                                                            case 'Abondonnée':
                                                                $statusClass = 'bg-gradient-warning';
                                                                break;
                                                            case 'Archivée':
                                                                $statusClass = 'bg-gradient-primary';
                                                                break;
                                                        }
                                                    @endphp
                                                    <span class="badge badge-sm {{ $statusClass }}">{{ $reservation['status'] }}</span>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span
                                                        class="text-secondary text-xs font-weight-bold">
                                                        <?php
                                                            if($reservation['is_group']) echo"Réservation pour un groupe privée de ".$reservation['nombre_des_voyageurs']."  personnes"; 
                                                            else echo $reservation['nombre_des_voyageurs']."/".$reservation['experience']->nombre_des_voyageur;
                                                        ?>
                                                        </span>
                                                </td>


                                            </tr>
                                        @endforeach

                                    </tbody>

                                </table>
                                <style>
                                    .pagination {
                                        margin-left: 40% !important
                                    }

                                    .page-link {
                                        background-color: #fb6240c6 !important;
                                        color: white !important;
                                    }

                                    .active .page-link {
                                        background-color: #fb6240 !important;
                                        color: white !important;
                                    }
                                </style>
                                <div class="pagination mt-4">{{ $reservations->links('pagination::bootstrap-4') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="footer pt-3  ">
                <div class="container-fluid">
                    <div class="row align-items-center justify-content-lg-between">
                        <div class="col-lg-6 mb-lg-0 mb-4">
                        </div>
                    </div>
            </footer>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Use querySelectorAll to select all modals and buttons
                var modals = document.querySelectorAll(".modal");
                var btns = document.querySelectorAll(".openModalBtn");
                var spans = document.querySelectorAll(".close");
                var closeModalBtns = document.querySelectorAll(".closeModalBtn");

                btns.forEach(function(btn, index) {
                    btn.onclick = function() {
                        modals[index].style.display = "block";
                    }
                });

                spans.forEach(function(span, index) {
                    span.onclick = function() {
                        modals[index].style.display = "none";
                    }
                });

                closeModalBtns.forEach(function(btn, index) {
                    btn.onclick = function() {
                        modals[index].style.display = "none";
                    }
                });

                window.onclick = function(event) {
                    modals.forEach(function(modal) {
                        if (event.target == modal) {
                            modal.style.display = "none";
                        }
                    });
                }
            });
        </script>
    </main>
@endsection
