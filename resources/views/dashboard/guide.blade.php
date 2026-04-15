@extends('dashboard.index')
@section('main')
    <main class="main-content position-relative border-radius-lg ">
        @include('dashboard.navbar')
        <div class="container-fluid py-4">
        <div class="row">
                <div class="col-12">
                    <a href="{{ route('export.guides') }}" class="btn btn-success">
                        Exporter les guides
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <div class="d-flex align-items-center">
                                <a class="btn btn-primary btn-sm ms-auto" href="{{ route('add-guide') }}">Ajouter un guide</a>
                            </div>
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
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Nom</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                numéro de téléphone</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Code SIREN</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Professionel</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Assujettis à la TVA </th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                ID Stripe Connect</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Status Stripe Connect</th>

                                                <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                A propos de guide</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">
                                                Plus de détails</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($guides as $guide)
                                            <tr>
                                                <td style="text-align: center">
                                                    <p class="text-xs font-weight-bold mb-0">{{ $loop->index + 1 }}</p>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ $guide->email }}<br>{{$guide->phone_number}}</p>
                                                    <!-- Assuming 'name' is a property of the Guide -->
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span
                                                        class="text-secondary text-xs font-weight-bold">{{ $guide->name }}</span>
                                                    <!-- Assuming 'email' is a property of the Guide -->
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span
                                                        class="text-secondary text-xs font-weight-bold">{{ $guide->phone_number }}</span>
                                                    <!-- Assuming 'email' is a property of the Guide -->
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span
                                                        class="text-secondary text-xs font-weight-bold">{{ $guide->siren_number }}</span>
                                                    <!-- Assuming 'email' is a property of the Guide -->
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span
                                                        class="text-secondary text-xs font-weight-bold"> {{ !empty($guide->siren_number) ? "Oui" : "Non" }} </span>
                                                </td>

                                                <td class="align-middle text-center">
                                                    <span
                                                        class="text-secondary text-xs font-weight-bold"> {{ $guide->is_tva_applicable ? "Oui" : "Non" }} </span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    @if($guide->guide[0]->stripe_connect_form_status && $guide->guide[0]->stripe_account_id && $guide->guide[0]->stripe_connect_form_url)
                                                    <span class="text-secondary text-xs font-weight-bold">{{$guide->guide[0]->stripe_account_id}}</span>
                                                    @else
                                                        <a class="btn btn-warning" href="{{ url('/make-stripe-account-guide/' . $guide->id) }}">Créer son compte stripe maintenant</a>
                                                    @endif
                                                </td>
                                                <td class="align-middle text-center">
                                                @php
                                                        $statusClass = '';
                                                        
                                                            switch($guide->guide[0]->stripe_connect_form_status)
                                                            {
                                                                case 'success':
                                                                    $statusClass = 'bg-gradient-success';
                                                                    break;
                                                                case 'pending':
                                                                    $statusClass = 'bg-gradient-primary';
                                                                    break;
                                                                case 'fail':
                                                                    $statusClass = 'bg-gradient-warning';
                                                                    break;
                                                                case 'sent':
                                                                    $statusClass = 'bg-gradient-secondary';
                                                                    break;
                                                                default:
                                                                    $statusClass = 'bg-gradient-warning';
                                                                    break;
                                                            }
                                                       
                                                    @endphp
                                                    
                                                    <span class="badge badge-sm {{ $statusClass }}">
                                                           {{ ($guide->guide[0]->stripe_connect_form_status)??"not set"}}                                        
                                                    </span>
                                                    @if($guide->guide[0]->stripe_connect_form_status === "sent" || $guide->guide[0]->stripe_connect_form_status === "pending")
                                                        <a href="{{ url('/make-stripe-account-guide/' . $guide->id) }}">
                                                            <img width ="30px" style="color:orange" src="{{ asset('img/resend.png') }}">
                                                        </a>
                                                    @endif
                                                    <!-- Assuming 'email' is a property of the Guide -->
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span
                                                        class="text-secondary text-xs font-weight-bold">{{ $guide->about_me}}
                                                    </span>
                                                    @if(!empty($guide->about_me_audio))
                                                        <br>
                                                        <audio controls style="margin-top:5px; width: 200px;">
                                                            <source src="{{ $guide->about_me_audio }}" type="audio/mpeg">
                                                            Votre navigateur ne supporte pas la lecture audio.
                                                        </audio>
                                                    @endif


                                                </td>
                                                <td class="align-middle text-center">
                                                    <a class="btn btn-warning"
                                                        href="{{ url('/guidesFiles/' . $guide->id) }}">données
                                                    </a>
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
                                <div class="pagination mt-4">{{ $guides->links('pagination::bootstrap-4') }}</div>
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
    </main>
@endsection
