@extends('dashboard.index')
@section('main')
    <main class="main-content position-relative border-radius-lg ">
        @include('dashboard.navbar')
        <div class="container-fluid py-4 ">
            <div class="row">
                <div class="col-12">
                    <div class="col-md-8 w-100">
                        <div class="card">
                            <div class="card-header pb-0">
                                <div class="d-flex align-items-center">
                                    <p class="mb-0">Ajouter un guide</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('createGuide') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name" class="form-control-label">Nom</label>
                                                <input id="name" name="name" class="form-control" type="text" placeholder="Nom">
                                                @error('name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email" class="form-control-label">Email address</label>
                                                <input id="email" name="email" class="form-control" type="email" placeholder="Email">
                                                @error('email')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="siren_number" class="form-control-label">CODE SIRENE
                                                </label>
                                                <input id="siren_number" name="siren_number" class="form-control" type="text" placeholder="CODE SIRENE
                                                ">
                                                @error('siren_number')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone_number" class="form-control-label">Numéro de téléphone</label>
                                                <input id="phone_number" name="phone_number" class="form-control" type="text" placeholder="Numéro de téléphone">
                                                @error('phone_number')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary">Enregistrement</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
    </main>
@endsection
