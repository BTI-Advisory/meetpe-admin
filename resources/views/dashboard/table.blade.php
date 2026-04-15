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
                    <a href="{{ route('export.experiences', ['status' => $status]) }}" class="btn btn-success">
                        Exporter les expériences
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
                                                E-mail - Nom</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Titre</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                description</th>                                        
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                durée d</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                créer à</th>

                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Status</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                price group prive</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                prix_par_voyageur</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Nombre des voyageurs </th>
                                            
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                adresse</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                ville</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Code Postal</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Pays</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                categorie</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                langues</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                type_voyageur</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                et_avec_ça</th>
                                            
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                support_group_prive</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                discount_kids_between_2_and_12</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                dernier_minute_reservation</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Max NB voyageur par groupe</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                photoprincipal</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Autres Photos</th>                                                                                         
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                raison</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Action</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Commentaire</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($experiences as $key => $experience)
                                            <tr>
                                                <td style="text-align: center">
                                                    <p class="text-xs font-weight-bold mb-0">{{ $key + 1 }}</p>
                                                </td>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div>
                                                            {{--  @php
                                                                // Array of image filenames
                                                                $imageFilenames = [
                                                                    'team-1.jpg',
                                                                    'team-2.jpg',
                                                                    'team-3.jpg',
                                                                    'team-4.jpg',
                                                                ];
                                                            @endphp
                                                            @php $randomImage = $imageFilenames[array_rand($imageFilenames)]; @endphp
                                                            <img src="{{ asset('img/' . $randomImage) }}"
                                                                class="avatar avatar-sm me-3" alt="user1"> --}}
                                                            <img src="{{ $experience['guide_picture'] }}"
                                                                class="avatar avatar-sm me-3" alt="user1">
                                                        </div>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ $experience['name_guide'] }}</h6>
                                                            <p class="text-xs text-secondary mb-0">
                                                                {{$experience['email_guide'] }}<br>
                                                                {{$experience['phone_guide'] }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ $experience['title'] }}</p>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span
                                                        class="text-secondary text-xs font-weight-bold">{{ $experience['description'] }}</span>
                                                </td>
                                                <!-- <td class="align-middle text-center text-sm">
                                                    <span

                                                    {{--  @if ($experience['audio_file != null)
                                                        <audio src="{{ $experience['audio_file }}" controls="true"
                                                            class="audio-1"></audio>
                                                    @else
                                                        <span class="badge badge-sm bg-gradient-warning">Pas trouvé</span>
                                                    @endif --}}
                                                </td> -->
                                                <td class="align-middle text-center text-sm">
                                                    <span
                                                        class="text-secondary text-xs font-weight-bold">{{ $experience['duree'] }}</span>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span
                                                        class="text-secondary text-xs font-weight-bold">{{ $experience['createdAt'] }}</span>
                                                </td>
                                                {{--    <td class="align-middle text-center text-sm">
                                                    <span
                                                        class="text-secondary text-xs font-weight-bold">{{ $experience['status'] }}</span>
                                                </td>
 --}}
                                                </td>
                                                <td class="align-middle text-center">
                                                    @php
                                                        $statusClass = '';
                                                        switch ($experience['status']) {
                                                            case 'en ligne':
                                                                $statusClass = 'bg-gradient-success';
                                                                break;
                                                            case 'en cours de vérification':
                                                                $statusClass = 'bg-gradient-success';
                                                                break;
                                                            case 'refuser':
                                                                $statusClass = 'bg-gradient-secondary';
                                                                break;
                                                            case 'à compléter':
                                                                $statusClass = 'bg-gradient-warning';
                                                                break;
                                                            case 'autre_document':
                                                                $statusClass = 'bg-gradient-warning';
                                                                break;
                                                            case 'supprimée':
                                                                $statusClass = 'bg-gradient-warning';
                                                                break;
                                                        }
                                                    @endphp

                                                    <span
                                                        class="badge badge-sm {{ $statusClass }}">{{ $experience['status']}}</span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class=" text-fuchsia-600 ">{{ $experience['prix_par_group'] }}
                                                    </p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    @if ($experience['prix_par_voyageur'] == null)
                                                        <span class=" text-fuchsia-600 text-danger">pas trouvé
                                                        </span>
                                                    @else
                                                        <p class=" text-fuchsia-600 ">{{ $experience['prix_par_voyageur'] }}
                                                        </p>
                                                    @endif

                                                </td>
                                                <td class="align-middle text-center">
                                                    @if ($experience['nombre_voyageur'] == null)
                                                        <span class=" text-fuchsia-600 text-danger">pas trouvé
                                                        </span>
                                                    @else
                                                        <p class=" text-fuchsia-600 ">
                                                            {{ $experience['nombre_voyageur'] }}
                                                        </p>
                                                    @endif

                                                </td>
                                               
                                                <td class="align-middle text-center">
                                                    @if ($experience['adresse'] == null)
                                                        <span class=" text-fuchsia-600 text-danger">pas trouvé
                                                        </span>
                                                    @else
                                                        <p class=" text-fuchsia-600 ">{{ $experience['adresse'] }}
                                                        </p>
                                                    @endif

                                                </td>
                                                <td class="align-middle text-center">
                                                    @if ($experience['ville'] == null)
                                                        <span class=" text-fuchsia-600 text-danger">pas trouvé
                                                        </span>
                                                    @else
                                                        <p class=" text-fuchsia-600 ">{{ $experience['ville'] }}
                                                        </p>
                                                    @endif
                                                </td>
                                                <td class="align-middle text-center">
                                                    @if ($experience['code_postal'] == null)
                                                        <span class=" text-fuchsia-600 text-danger">pas trouvé
                                                        </span>
                                                    @else
                                                        <p class=" text-fuchsia-600 ">{{ $experience['code_postal'] }}
                                                        </p>
                                                    @endif
                                                </td>
                                                <td class="align-middle text-center">
                                                    @if ($experience['pays'] == null)
                                                        <span class=" text-fuchsia-600 text-danger">pas trouvé
                                                        </span>
                                                    @else
                                                        <p class=" text-fuchsia-600 ">{{ $experience['pays'] }}
                                                        </p>
                                                    @endif
                                                </td>
                                                <td class="align-middle text-center">
                                                    
                                                    @if (!is_null($experience['categories']))
                                                   
                                                        @forelse ($experience['categories'] as $c)
                                                            <span class=" text-fuchsia-600 ">- {{ $c->choix }} <br>
                                                            </span>
                                                        @empty
                                                            <span class=" text-fuchsia-600 text-danger">pas trouvéeee
                                                            </span>
                                                        @endforelse
                                                    @else
                                                        <span class=" text-fuchsia-600 text-danger">pas trouvée
                                                        </span>
                                                    @endif

                                                </td>
                                                <td class="align-middle text-center">
                                                     @if (!is_null($experience['languages']))
                                                        @forelse ($experience['languages'] as $l)
                                                            <span class=" text-fuchsia-600 ">- {{ $l->choix }} <br>
                                                            </span>
                                                        @empty
                                                            <span class=" text-fuchsia-600 text-danger">pas trouvé
                                                            </span>
                                                        @endforelse
                                                    @else
                                                        <span class=" text-fuchsia-600 text-danger">pas trouvé
                                                        </span>
                                                    @endif 

                                                </td>
                                                <td class="align-middle text-center">
                                                    @if ($experience['type_voyageur'] == null)
                                                        <span class="text-fuchsia-600 text-danger">pas trouvé</span>
                                                    @else
                                                        @forelse ($experience['type_voyageur'] as $c)
                                                            <span class="text-fuchsia-600"> - {{ $c->choix }}
                                                                <br></span>
                                                        @empty
                                                            <span class="text-fuchsia-600 text-danger">pas trouvé</span>
                                                        @endforelse
                                                    @endif

                                                </td>
                                                <td class="align-middle text-center">
                                                    @if (!is_null($experience['options']))
                                                        @forelse ($experience['options'] as $c)
                                                            <span class=" text-fuchsia-600 ">- {{ $c->choix }} <br>
                                                            </span>
                                                        @empty
                                                            <span class=" text-fuchsia-600 text-danger">pas trouvé
                                                            </span>
                                                        @endforelse
                                                    @else
                                                        <span class=" text-fuchsia-600 text-danger">pas trouvé
                                                        </span>
                                                    @endif

                                                </td>
                                               
                                                <td class="align-middle text-center">

                                                    @if ($experience['support_group_prive'])
                                                        <span class=" text-fuchsia-600 ">support
                                                        </span>
                                                    @else
                                                        <span class=" text-fuchsia-600 ">non support
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="align-middle text-center">
                                                    @if ($experience['discount_kids'] == null)
                                                        pas trouvé
                                                    @else
                                                        <span
                                                            class=" text-fuchsia-600 ">{{ $experience['discount_kids'] }}
                                                        </span>
                                                    @endif

                                                </td>
                                                
                                                <td class="align-middle text-center">
                                                @if (!is_null($experience['dernier_minute_reservation']))
                                                        @forelse ($experience['dernier_minute_reservation'] as $c)
                                                            <span class=" text-fuchsia-600 ">- {{ $c->choix }} <br>
                                                            </span>
                                                        @empty
                                                            <span class=" text-fuchsia-600 text-danger">pas trouvé
                                                            </span>
                                                        @endforelse
                                                    @else
                                                        <span class=" text-fuchsia-600 text-danger">pas trouvé
                                                        </span>
                                                    @endif                                                                   
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span
                                                        class=" text-fuchsia-600 ">{{ $experience['Max_nb_voyageur'] }}
                                                    </span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    @if (optional($experience['photoprincipal'])->photo_url)
                                                        <img src="{{ optional($experience['photoprincipal'])->photo_url }}"
                                                            style="width: 100px; height: 100px; object-fit: cover;"
                                                            alt="">
                                                    @else
                                                        <span class="text-fuchsia-600 text-danger">pas trouvé</span>
                                                    @endif
                                                </td>
                                                <td class="align-middle text-center">
                                                    @if (isset($experience['photos']))
                                                        @foreach ($experience['photos'] as $photo)
                                                            <img src="{{ $photo->photo_url }}"
                                                            style="width: 100px; height: 100px; object-fit: cover;"
                                                            alt="">
                                                        @endforeach            
                                                    @else
                                                        <span class="text-fuchsia-600 text-danger">pas trouvé</span>
                                                    @endif
                                                </td>    
                                                <td class="align-middle text-center">
                                                    @if (isset($experience['raison']))
                                                        <span>
                                                            @if (strstr($experience['raison'], '_'))
                                                                <ul>
                                                                    @foreach (explode('_', $experience['raison']) as $r)
                                                                        <li>
                                                                            {{ $r }}
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                {{ $experience['raison'] }}
                                                            @endif
                                                        </span>
                                                    @else
                                                        <span class="text-fuchsia-600 text-danger">raison non signalé</span>
                                                    @endif
                                                </td>

                                                <td class="align-middle text-center">
                                                    <div>
                                                        <form id="statusForm{{ $experience['id'] }}" method="POST"
                                                            action="{{ url('guide/update-status/' . $experience['id']) }}">
                                                            @csrf
                                                            <select id="statusSelect{{ $experience['id'] }}"
                                                                name="new_status" class="form-select"
                                                                style="width: auto">
                                                                <option>Sélectionnez le statut</option>
                                                                <option value="accepter"
                                                                    {{ $experience['status'] == 'accepter' ? 'selected' : '' }}>
                                                                    Accepter</option>
                                                                <option value="refuser"
                                                                    {{ $experience['status'] == 'refuser' ? 'selected' : '' }}>
                                                                    Refuser</option>
                                                                <option value="autre_document"
                                                                    {{ $experience['status'] == 'autre_document' ? 'selected' : '' }}>
                                                                    Autre document</option>
                                                            </select>
                                                        </form>
                                                        <script>
                                                            document.getElementById('statusSelect{{ $experience['id'] }}').addEventListener('change', function() {
                                                                document.getElementById('statusForm{{ $experience['id'] }}').submit();
                                                            });
                                                        </script>
                                                </td>
                                                <td class="align-middle text-center mt-1">
                                                    <div style="margin-top: 10%">
                                                        <button id="openModalBtn-{{ $experience['id'] }}"
                                                            class="openModalBtn btn btn-waring text-white"
                                                            style="background-color:#fd7e14">commenter</button>
                                                        <!-- The Modal -->
                                                        <div id="myModal-{{ $experience['id'] }}" class="modal">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <span class="close"
                                                                        style="background-color: #fd7e14">&times;</span>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form method="POST"
                                                                        action="/guide/add-comment/{{ $experience['id'] }}">
                                                                        <input type="hidden" name="_token"
                                                                            value="{{ csrf_token() }}">
                                                                        <div class="mb-3">
                                                                            <label for="description"
                                                                                class="form-label">Ajouter un commentaire
                                                                            </label>
                                                                            <textarea style="border-color: #fd7e14" id="description" placeholder="ajouter un commentaire" class="form-control"
                                                                                name="raison">{{ old('raison') }}</textarea>
                                                                        </div>
                                                                        <button type="submit" class="btn text-white"
                                                                            style="background-color:#fd7e14">Envoyer</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
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
                                <div class="pagination mt-4">{{ $experiences->links('pagination::bootstrap-4') }}</div>
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
