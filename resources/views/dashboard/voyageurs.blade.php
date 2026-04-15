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
                    <a href="{{ route('export.voyageurs') }}" class="btn btn-success">
                        Exporter les voyageurs
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
                                        <th style="text-align: center"
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            </th>
                                           
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Coordonnées</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Personnalité</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Préfenrences</th>                                        
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Types</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Modes</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Experiences</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Languages</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Adresse</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Dates Arrivée/Depart</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Nombres total des reservations</th>                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($voyageurs as $key => $voyageur)
                                        
                                        
                                            <tr>
                                            <td style="text-align: center">
                                                    <p class="text-xs font-weight-bold mb-0">{{ $key + 1 }}</p>
                                                </td>
                                                
                                                <td class="align-middle text-center text-sm">
                                                    <span
                                                        class="text-secondary text-xs font-weight-bold">{{ $voyageur['name'] }}
                                                        <br><span>{{$voyageur['email']}}<br>{{$voyageur['phone']}}</span>
                                                        <br><b><span>{{$voyageur['age']}} ans</span></b>
                                                    </span>
                                                </td>
                                            
                                            
                                                <td class="align-middle text-center">
                                                    
                                                    @if (!is_null($voyageur['personnalite']))
                                                   
                                                        @forelse ($voyageur['personnalite'] as $c)
                                                            <span class=" text-fuchsia-600 ">- {{ $c->choix }} <br>
                                                            </span>
                                                        @empty
                                                            <span class=" text-fuchsia-600 text-danger">Rien
                                                            </span>
                                                        @endforelse
                                                    @else
                                                        <span class=" text-fuchsia-600 text-danger">non défini
                                                        </span>
                                                    @endif

                                                </td>
                                                <td class="align-middle text-center">
                                                    
                                                    @if (!is_null($voyageur['preference']))
                                                   
                                                        @forelse ($voyageur['preference'] as $c)
                                                            <span class=" text-fuchsia-600 ">- {{ $c->choix }} <br>
                                                            </span>
                                                        @empty
                                                            <span class=" text-fuchsia-600 text-danger">Rien
                                                            </span>
                                                        @endforelse
                                                    @else
                                                        <span class=" text-fuchsia-600 text-danger">non défini
                                                        </span>
                                                    @endif

                                                </td>
                                                <td class="align-middle text-center">
                                                    
                                                    @if (!is_null($voyageur['type']))
                                                   
                                                        @forelse ($voyageur['type'] as $c)
                                                            <span class=" text-fuchsia-600 ">- {{ $c->choix }} <br>
                                                            </span>
                                                        @empty
                                                            <span class=" text-fuchsia-600 text-danger">Rien
                                                            </span>
                                                        @endforelse
                                                    @else
                                                        <span class=" text-fuchsia-600 text-danger">non défini
                                                        </span>
                                                    @endif

                                                </td>
                                                <td class="align-middle text-center">
                                                    
                                                    @if (!is_null($voyageur['mode']))
                                                   
                                                        @forelse ($voyageur['mode'] as $c)
                                                            <span class=" text-fuchsia-600 ">- {{ $c->choix }} <br>
                                                            </span>
                                                            @empty
                                                            <span class=" text-fuchsia-600 text-danger">Rien
                                                            </span>
                                                        @endforelse
                                                    @else
                                                        <span class=" text-fuchsia-600 text-danger">non défini
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="align-middle text-center">
                                                    
                                                    @if (!is_null($voyageur['experiences']))
                                                   
                                                        @forelse ($voyageur['experiences'] as $c)
                                                            <span class=" text-fuchsia-600 ">- {{ $c->choix }} <br>
                                                            </span>
                                                            @empty
                                                            <span class=" text-fuchsia-600 text-danger">Rien
                                                            </span>
                                                        @endforelse
                                                    @else
                                                        <span class=" text-fuchsia-600 text-danger">non défini
                                                        </span>
                                                    @endif

                                                </td>

                                                <td class="align-middle text-center">
                                                    
                                                    @if (!is_null($voyageur['languages']))
                                                   
                                                        @forelse ($voyageur['languages'] as $c)
                                                            <span class=" text-fuchsia-600 ">- {{ $c->choix }} <br>
                                                            </span>
                                                            @empty
                                                            <span class=" text-fuchsia-600 text-danger">Rien
                                                            </span>
                                                        @endforelse
                                                    @else
                                                        <span class=" text-fuchsia-600 text-danger">non défini
                                                        </span>
                                                    @endif

                                                </td>

                                                <td class="align-middle text-center text-sm">
                                                    <span
                                                        class="text-secondary text-xs font-weight-bold">{{ $voyageur['adresse'] }}
                                                    </span>
                                                </td>


                                                <td class="align-middle text-center text-sm">
                                              
                                                    <span class="text-secondary text-xs font-weight-bold">
                                                        {{ ($voyageur['date_arrivee']) }} <br> {{ ($voyageur['date_depart'])}}
                                                    
                                                    </span>
                                                </td>

                                                <td class="align-middle text-center text-sm">
                                              
                                                    <span class="text-secondary text-xs font-weight-bold">
                                                        {{ ($voyageur['nb_reservations']) }}
                                                    </span>
                                                </td>

                                                


                                                <td class="align-middle text-center text-sm">
                                                    <span
                                                        class="text-secondary text-xs font-weight-bold">
                                                       
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
                                <div class="pagination mt-4">{{ $voyageurs->links('pagination::bootstrap-4') }}</div>
                               
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
