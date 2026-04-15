@extends('dashboard.index')
@section('main')
<style>
    /* Style the Image Used to Trigger the Modal */
#myImg {
  border-radius: 5px;
  cursor: pointer;
  transition: 0.3s;
}

#myImg:hover {opacity: 0.7;}

/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
}

/* Modal Content (Image) */
.modal-content {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
}

/* Caption of Modal Image (Image Text) - Same Width as the Image */
#caption {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
  text-align: center;
  color: #ccc;
  padding: 10px 0;
  height: 150px;
}

/* Add Animation - Zoom in the Modal */
.modal-content, #caption {
  animation-name: zoom;
  animation-duration: 0.6s;
}

@keyframes zoom {
  from {transform:scale(0)}
  to {transform:scale(1)}
}

/* The Close Button */
.close {
  position: absolute;
  top: 15px;
  right: 35px;
  color: #f1f1f1;
  font-size: 40px;
  font-weight: bold;
  transition: 0.3s;
}

.close:hover,
.close:focus {
  color: #bbb;
  text-decoration: none;
  cursor: pointer;
}

/* 100% Image Width on Smaller Screens */
@media only screen and (max-width: 700px){
  .modal-content {
    width: 100%;
  }
}
</style>
    <main class="main-content position-relative border-radius-lg ">
        @include('dashboard.navbar')
        <div class="container-fluid py-4" style="margin-left: 10%">
        
            <div class="row">

                
            
                <div class="col-12">
                    <div class="card mb-4">
                        <style>
                            * {
                                box-sizing: border-box;
                                margin: 0;
                                padding: 0;
                            }

                            body {
                                font-family: sans-serif;
                                line-height: 1.6;
                                color: #333;
                            }

                            .container {
                                max-width: 64rem;
                                margin: 0 auto;
                                padding: 2.5rem 1rem;
                            }

                            .grid {
                                display: grid;
                                gap: 2rem;
                            }

                            .grid-cols-2 {
                                display: grid;
                                grid-template-columns: 1fr 1fr;
                                gap: 2rem;
                            }

                            .grid-cols-3 {
                                display: grid;
                                grid-template-columns: 1fr 1fr 1fr;
                                gap: 1rem;
                            }

                            .title {
                                font-size: 1.5rem;
                                font-weight: bold;
                            }

                            .subtitle {
                                color: #6b7280;
                            }

                            .card {
                                border: 1px solid #e5e7eb;
                                background-color: #fff;
                                color: #374151;
                                border-radius: 0.5rem;
                                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
                            }

                            .card-header {
                                padding: 1.5rem;
                                border-bottom: 1px solid #e5e7eb;
                            }

                            .card-title {
                                font-size: 1.5rem;
                                font-weight: 600;
                            }

                            .card-body {
                                padding: 1.5rem;
                            }

                            .input-group {
                                margin-bottom: 1rem;
                            }

                            .input-group label {
                                display: block;
                                font-size: 0.875rem;
                                font-weight: 500;
                                margin-bottom: 0.5rem;
                            }

                            .input-group input {
                                width: 100%;
                                height: 2.5rem;
                                padding: 0.5rem 0.75rem;
                                border: 1px solid #d1d5db;
                                border-radius: 0.375rem;
                                font-size: 0.875rem;
                                color: #374151;
                                background-color: #f9fafb;
                                outline: none;
                                transition: border-color 0.2s, box-shadow 0.2s;
                            }

                            .input-group input:focus {
                                border-color: #60a5fa;
                                box-shadow: 0 0 0 2px rgba(96, 165, 250, 0.5);
                            }

                            .save-button {
                                display: inline-flex;
                                align-items: center;
                                justify-content: center;
                                padding: 0.5rem 1rem;
                                font-size: 0.875rem;
                                font-weight: 500;
                                border: 1px solid #d1d5db;
                                border-radius: 0.375rem;
                                background-color: #f9fafb;
                                color: #374151;
                                cursor: pointer;
                                transition: background-color 0.2s, color 0.2s;
                            }

                            .save-button:hover {
                                background-color: #2563eb;
                                color: #fff;
                            }
                        </style>
                        <div class="container">
                            <div class="grid gap-8">
                            <div class="grid-cols-2 gap-8">
                            <div class="col-12">
                    <a href="{{ route('export.avis', $guide->id) }}" class="btn btn-success">
                        Exporter les avis des expériences de {{$guide->name}}
                    </a>
                </div></div>
                                <div class="grid-cols-2 gap-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Personal Information</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="input-group">
                                                <label for="name" class="">Nom et prenom :</label>
                                                <input id="name" placeholder="" class="text-dark"
                                                    value="{{ $guide->name ?? 'Pas trouvé' }}" />
                                            </div>
                                            <div class="input-group">
                                                <label for="phone">Téléphone</label>
                                                <input id="name" placeholder="" class="text-dark"
                                                    value="{{ $guide->phone_number ?? 'Pas trouvé' }}" />
                                            </div>
                                            <div class="input-group">
                                                <label for="email">Email</label>
                                                <input id="name" placeholder="" class="text-dark"
                                                    value="{{ $guide->email ?? 'Pas trouvé' }}" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Coordonnées bancaires</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="input-group">
                                                <label for="iban">IBAN</label>
                                                <input id="name" placeholder="" class="text-dark"
                                                    value="{{ $guide->IBAN ?? 'Pas trouvé' }}" />
                                            </div>
                                            <div class="input-group">
                                                <label for="bic">BIC</label>
                                                <input id="name" placeholder="" class="text-dark"
                                                    value="{{ $guide->BIC ?? 'Pas trouvé' }}" />
                                            </div>
                                            <div class="input-group">
                                                <label for="account-holder">Nom du titulaire</label>
                                                <input id="name" placeholder="" class="text-dark"
                                                    value="{{ $guide->nom_du_titulaire ?? 'Pas trouvé' }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid-cols-2 gap-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Address</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="input-group">
                                                <label for="street">Rue</label>
                                                <input id="name" placeholder="" class="text-dark"
                                                    value="{{ $guide->rue }}" />
                                            </div>
                                            <div class="input-group">
                                                <label for="postal-code">Postal Code</label>
                                                <input id="name" placeholder="" class="text-dark"
                                                    value="{{ $guide->code_postal }}" />
                                            </div>
                                            <div class="input-group">
                                                <label for="city">Ville</label>
                                                <input id="name" placeholder="" class="text-dark"
                                                    value="{{ $guide->ville }}" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Device Information</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="input-group">
                                                <h8>Device Model :</h8>
                                                <h8 style="margin-left: 5% !important">
                                                    {{ $device->deviceModel ?? 'Pas trouvé' }}</h8>
                                            </div>
                                            <div class="input-group">
                                                <h8>Device Brand :</h8>
                                                <h8 style="margin-left: 5% !important">
                                                    {{ $device->deviceBrand ?? 'Pas trouvé' }}</h8>
                                            </div>
                                            <div class="input-group">
                                                <h8>OS Version :</h8>
                                                <h8 style="margin-left: 5% !important">
                                                    {{ $device->deviceOsVersion ?? 'Pas trouvé' }}</h8>
                                            </div>
                                            <div class="input-group">
                                                <h8>App Version :</h8>
                                                <h8 style="margin-left: 5% !important">
                                                    {{ $device->appVersion ?? 'Pas trouvé' }}</h8>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Identity Documents</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="grid-cols-3 gap-4">
                                            <div class="input-group">
                                                <h8>Piece Identité</h8>
                                                <div>
                                                    <img id="img1" src="{{ $guide->piece_d_identite ?? 'https://media.istockphoto.com/id/1409329028/vector/no-picture-available-placeholder-thumbnail-icon-illustration-design.jpg?s=612x612&w=0&k=20&c=_zOuJu755g2eEUioiOUdz_mHKJQJn-tDgIAhQzyeKUQ=' }}"
                                                         alt="Identity Document"
                                                         style="width: 300px; border-radius: 8%; height: 300px; object-fit: cover; margin-top: 32% !important;">
                                                </div>
                                            </div>
                                            <div class="input-group">
                                                <h8>Piece Identité verso</h8>
                                                <div>
                                                    @if ($guide->piece_d_identite_verso == "" || $guide->piece_d_identite_verso == null)
                                                    <img id="img2" src="https://media.istockphoto.com/id/1409329028/vector/no-picture-available-placeholder-thumbnail-icon-illustration-design.jpg?s=612x612&w=0&k=20&c=_zOuJu755g2eEUioiOUdz_mHKJQJn-tDgIAhQzyeKUQ="
                                                         alt="Identity Document"
                                                         style="width: 300px; border-radius: 8%; height: 300px; object-fit: cover; margin-top: 32% !important;">
                                                    @else
                                                    <img id="img2" src="{{ $guide->piece_d_identite_verso}}"
                                                         alt="Identity Document"
                                                         style="width: 300px; border-radius: 8%; height: 300px; object-fit: cover; margin-top: 32% !important;">
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="input-group">
                                                <h8>Kbis</h8>
                                                <div>
                                                    <img id="img3" src="{{ $guide->KBIS_file ?? 'https://media.istockphoto.com/id/1409329028/vector/no-picture-available-placeholder-thumbnail-icon-illustration-design.jpg?s=612x612&w=0&k=20&c=_zOuJu755g2eEUioiOUdz_mHKJQJn-tDgIAhQzyeKUQ=' }}"
                                                         alt="Identity Document"
                                                         style="width: 300px; border-radius: 8%; height: 300px; object-fit: cover; margin-top: 32% !important;">
                                                </div>
                                            </div>
                                            @if($other_document)
                                            @foreach ($other_document as $other_documentt)
                                            <div class="input-group">
                                                <h8>Autre document</h8>
                                                <div>
                                                    <img id="img{{ $loop->index + 4 }}" src="{{ $other_documentt->document_path }}"
                                                         alt="{{ $other_documentt->document_title ?? ' ' }}"
                                                         style="width: 300px; height: 300px; border-radius: 8%; object-fit: cover; margin-top: 32% !important;">
                                                    <h8>Nom du document: {{ $other_documentt->document_title ?? ' ' }}</h8>
                                                </div>
                                            </div>
                                            @endforeach
                                            @endif
                                        </div>
                                    </div>

                                    <!-- The Modal -->
                                    <div id="myModal" class="modal">
                                        <!-- The Close Button -->
                                        <span class="close">&times;</span>
                                        <!-- Modal Content (The Image) -->
                                        <img class="modal-content" id="img01">
                                        <!-- Modal Caption (Image Text) -->
                                        <div id="caption"></div>
                                    </div>

                                    <script>
                                        // Get the modal
                                        var modal = document.getElementById("myModal");

                                        // Get the modal image and caption
                                        var modalImg = document.getElementById("img01");
                                        var captionText = document.getElementById("caption");

                                        // Get all images and add click event listeners
                                        var images = document.querySelectorAll('.input-group img');
                                        images.forEach(function(img) {
                                            img.onclick = function() {
                                                modal.style.display = "block";
                                                modalImg.src = this.src;
                                                captionText.innerHTML = this.alt;
                                            }
                                        });

                                        // Get the <span> element that closes the modal
                                        var span = document.getElementsByClassName("close")[0];

                                        // When the user clicks on <span> (x), close the modal
                                        span.onclick = function() {
                                            modal.style.display = "none";
                                        }
                                    </script>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-left: 10%">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            Listes des expériences
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                         
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
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($experiences as $key => $experience)
                                            <tr>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ $experience['title'] }}</p>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span
                                                        class="text-secondary text-xs font-weight-bold">{{ $experience['description'] }}</span>
                                                </td>                                            
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
                                                    @if (isset($experience['photoprincipal']))
                                                        <img src="{{ $experience['photoprincipal']->photo_url }}"
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
            
            <footer class="footer pt-3  ">
                <div class="container-fluid">
                    <div class="row align-items-center justify-content-lg-between">
                        <div class="col-lg-6 mb-lg-0 mb-4">
                        </div>
                    </div>
            </footer>
        </div>
    </main>

@endsection
