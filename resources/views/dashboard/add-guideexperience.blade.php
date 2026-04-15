@extends('dashboard.index')
@section('main')
    <main class="main-content position-relative border-radius-lg ">
        @include('dashboard.navbar')

        <div class="container-fluid py-4 ">
            <div class="row ">
                <div class="col-12">

                    <div class="col-md-8  w-100">
                        <div class="card">
                            <div class="card-header">{{ __('Create Guide Experience') }}</div>

                            <div class="card-body">
                                <form method="POST" action=" {{ url('guide-experience/store/' . $id) }}"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <!-- Part One Fields -->
                                    <div class="form-group row">
                                        <label for="nom"
                                            class="col-md-4 col-form-label text-md-right">{{ __('Nom') }}</label>

                                        <div class="col-md-6">
                                            <input id="nom" type="text"
                                                class="form-control @error('nom') is-invalid @enderror" name="nom"
                                                value="{{ old('nom') }}" autocomplete="nom" autofocus>

                                            @error('nom')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="description"
                                            class="col-md-4 col-form-label text-md-right">{{ __('Description') }}</label>

                                        <div class="col-md-6">
                                            <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description">{{ old('description') }}</textarea>

                                            @error('description')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="duree"
                                            class="col-md-4 col-form-label text-md-right">{{ __('Durée') }}</label>

                                        <div class="col-md-6">
                                            <input id="duree" type="number"
                                                class="form-control @error('duree') is-invalid @enderror" name="duree"
                                                value="{{ old('duree') }}">

                                            @error('duree')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="country"
                                            class="col-md-4 col-form-label text-md-right">{{ __('country') }}</label>

                                        <div class="col-md-6">
                                            <input id="country" type="number"
                                                class="form-control @error('country') is-invalid @enderror" name="country"
                                                value="{{ old('country') }}">

                                            @error('country')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- Category Field -->
                                    <div class="form-group row">
                                        <label for="categorie"
                                            class="col-md-4 col-form-label text-md-right">{{ __('Category') }}</label>

                                        <div class="col-md-6">
                                            <select id="categorie" multiple="multiple"
                                                class="form-control @error('categorie') is-invalid @enderror"
                                                name="categorie[]">
                                                <option value="" disabled selected>Select an option</option>
                                                @foreach ($guide_categorie_de_lexperience as $guide_categorie_de_lexperienc)
                                                    <option value="{{ $guide_categorie_de_lexperienc['id'] }}">
                                                        {{ $guide_categorie_de_lexperienc['choice_txt'] }} -
                                                        {{ $guide_categorie_de_lexperienc['id'] }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            @error('categorie')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="categorie"
                                            class="col-md-4 col-form-label text-md-right">{{ __('support_group_prive') }}</label>

                                        <div class="col-md-6">
                                            <select id="support_group_prive"
                                                class="form-control @error('support_group_prive') is-invalid @enderror"
                                                name="support_group_prive">
                                                <option value="false">faux</option>
                                                <option value="true">vrai</option>
                                                {{--  @foreach ($guide_categorie_de_lexperience as $guide_categorie_de_lexperienc)
                                                    <option value="{{ $guide_categorie_de_lexperienc['id'] }}">
                                                        {{ $guide_categorie_de_lexperienc['choice_txt'] }} -
                                                        {{ $guide_categorie_de_lexperienc['id'] }}
                                                    </option>
                                                @endforeach --}}
                                            </select>

                                            @error('support_group_prive')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="categorie"
                                            class="col-md-4 col-form-label text-md-right">{{ __('dernier_minute_reservation') }}</label>

                                        <div class="col-md-6">
                                            <select id="dernier_minute_reservation"
                                                class="form-control @error('dernier_minute_reservation') is-invalid @enderror"
                                                name="dernier_minute_reservation">
                                                <option value="1 Heur">1 Heur</option>
                                                <option value="3 Heur">3 Heur</option>
                                                <option value="12 Heur">12 Heur</option>
                                                <option value="24 Heur">24 Heur</option>
                                                {{--  @foreach ($guide_categorie_de_lexperience as $guide_categorie_de_lexperienc)
                                                    <option value="{{ $guide_categorie_de_lexperienc['id'] }}">
                                                        {{ $guide_categorie_de_lexperienc['choice_txt'] }} -
                                                        {{ $guide_categorie_de_lexperienc['id'] }}
                                                    </option>
                                                @endforeach --}}
                                            </select>

                                            @error('dernier_minute_reservation')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="categorie"
                                            class="col-md-4 col-form-label text-md-right">{{ __('discount_kids_between_2_and_12') }}</label>

                                        <div class="col-md-6">
                                            <select id="discount_kids_between_2_and_12"
                                                class="form-control @error('discount_kids_between_2_and_12') is-invalid @enderror"
                                                name="discount_kids_between_2_and_12">
                                                <option value="false">faux</option>
                                                <option value="true">vrai</option>
                                                {{--  @foreach ($guide_categorie_de_lexperience as $guide_categorie_de_lexperienc)
                                                    <option value="{{ $guide_categorie_de_lexperienc['id'] }}">
                                                        {{ $guide_categorie_de_lexperienc['choice_txt'] }} -
                                                        {{ $guide_categorie_de_lexperienc['id'] }}
                                                    </option>
                                                @endforeach --}}
                                            </select>

                                            @error('discount_kids_between_2_and_12')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>


                                    <!-- Part Two Fields -->
                                    <div class="form-group row">
                                        <label for="prix_par_voyageur"
                                            class="col-md-4 col-form-label text-md-right">{{ __('Prix Par Voyageur') }}</label>

                                        <div class="col-md-6">
                                            <input id="prix_par_voyageur" type="number"
                                                class="form-control @error('prix_par_voyageur') is-invalid @enderror"
                                                name="prix_par_voyageur" value="{{ old('prix_par_voyageur') }}">

                                            @error('prix_par_voyageur')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- et_avec_ça Field -->
                                    <div class="form-group row">
                                        <label for="et_avec_ça"
                                            class="col-md-4 col-form-label text-md-right">{{ __('Et Avec Ça') }}</label>
                                        <div class="col-md-6">
                                            <select id="et_avec_ça"
                                                class="form-control @error('et_avec_ça') is-invalid @enderror"
                                                multiple="multiple" name="et_avec_ça[]">
                                                @foreach ($et_avec_ça as $et_avec_ça_item)
                                                    <option value="{{ $et_avec_ça_item['id'] }}">
                                                        {{ $et_avec_ça_item['choice_txt'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('et_avec_ça')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- 'nombre_des_voyageur' Field -->
                                    <div class="form-group row">
                                        <label for="nombre_des_voyageur"
                                            class="col-md-4 col-form-label text-md-right">{{ __('Nombre des Voyageur') }}</label>

                                        <div class="col-md-6">
                                            <input id="nombre_des_voyageur" type="number"
                                                class="form-control @error('nombre_des_voyageur') is-invalid @enderror"
                                                name="nombre_des_voyageur" value="{{ old('nombre_des_voyageur') }}">

                                            @error('nombre_des_voyageur')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="nombre_des_voyageur"
                                            class="col-md-4 col-form-label text-md-right">{{ __('price_group_prive') }}</label>

                                        <div class="col-md-6">
                                            <input id="price_group_prive" type="number"
                                                class="form-control @error('price_group_prive') is-invalid @enderror"
                                                name="price_group_prive" value="{{ old('price_group_prive') }}">

                                            @error('price_group_prive')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="nombre_des_voyageur"
                                            class="col-md-4 col-form-label text-md-right">{{ __('max_group_size') }}</label>

                                        <div class="col-md-6">
                                            <input id="max_group_size" type="number"
                                                class="form-control @error('max_group_size') is-invalid @enderror"
                                                name="max_group_size" value="{{ old('max_group_size') }}">

                                            @error('max_group_size')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- 'guide_personnes_peuves_participer' Field -->
                                    <div class="form-group row">
                                        <label for="guide_personnes_peuves_participer"
                                            class="col-md-4 col-form-label text-md-right">{{ __('Guide Personnes Peuves Participer') }}</label>
                                        <div class="col-md-6">
                                            <select id="guide_personnes_peuves_participer" multiple="multiple"
                                                class="form-control @error('guide_personnes_peuves_participer') is-invalid @enderror"
                                                name="guide_personnes_peuves_participer[]">
                                                <option value="" disabled selected>Select an option</option>
                                                @foreach ($guide_personnes_peuves_participer as $item)
                                                    <option value="{{ $item['id'] }}">{{ $item['choice_txt'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('guide_personnes_peuves_participer')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>


                                    <!-- 'ville' Field -->
                                    <div class="form-group row">
                                        <label for="ville"
                                            class="col-md-4 col-form-label text-md-right">{{ __('Ville') }}</label>

                                        <div class="col-md-6">
                                            <input id="ville" type="text"
                                                class="form-control @error('ville') is-invalid @enderror" name="ville"
                                                value="{{ old('ville') }}">

                                            @error('ville')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- 'addresse' Field -->
                                    <div class="form-group row">
                                        <label for="addresse"
                                            class="col-md-4 col-form-label text-md-right">{{ __('Addresse') }}</label>

                                        <div class="col-md-6">
                                            <input id="addresse" type="text"
                                                class="form-control @error('addresse') is-invalid @enderror"
                                                name="addresse" value="{{ old('addresse') }}">

                                            @error('addresse')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- 'code_postale' Field -->
                                    <div class="form-group row">
                                        <label for="code_postale"
                                            class="col-md-4 col-form-label text-md-right">{{ __('Code Postale') }}</label>

                                        <div class="col-md-6">
                                            <input id="code_postale" type="text"
                                                class="form-control @error('code_postale') is-invalid @enderror"
                                                name="code_postale" value="{{ old('code_postale') }}">

                                            @error('code_postale')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- 'experience_id' Field -->
                                    {{--   <div class="form-group row">
                                        <label for="experience_id"
                                            class="col-md-4 col-form-label text-md-right">{{ __('Experience ID') }}</label>

                                        <div class="col-md-6">
                                            <input id="experience_id" type="number"
                                                class="form-control @error('experience_id') is-invalid @enderror"
                                                name="experience_id" value="{{ old('experience_id') }}">

                                            @error('experience_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div> --}}
                                    <!-- 'image_principale' Field -->
                                    <div class="form-group row">
                                        <label for="image_principale"
                                            class="col-md-4 col-form-label text-md-right">image_principale</label>

                                        <div class="col-md-6">
                                            <input id="image_principale" type="file"
                                                class="form-control @error('image_principale') is-invalid @enderror"
                                                name="image_principale" value="{{ old('image_principale') }}">

                                            @error('image_principale')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="image_1"
                                            class="col-md-4 col-form-label text-md-right">image_1</label>

                                        <div class="col-md-6">
                                            <input id="image_1" type="file"
                                                class="form-control @error('image_1') is-invalid @enderror"
                                                name="image_1" value="{{ old('image_1') }}">

                                            @error('image_1')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="image_0"
                                            class="col-md-4 col-form-label text-md-right">image_0</label>

                                        <div class="col-md-6">
                                            <input id="image_0" type="file"
                                                class="form-control @error('image_0') is-invalid @enderror"
                                                name="image_0" value="{{ old('image_0') }}">

                                            @error('image_0')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="image_2"
                                            class="col-md-4 col-form-label text-md-right">image_2</label>

                                        <div class="col-md-6">
                                            <input id="image_2" type="file"
                                                class="form-control @error('image_2') is-invalid @enderror"
                                                name="image_2" value="{{ old('image_2') }}">

                                            @error('image_2')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="image_3"
                                            class="col-md-4 col-form-label text-md-right">image_3</label>

                                        <div class="col-md-6">
                                            <input id="image_3" type="file"
                                                class="form-control @error('image_3') is-invalid @enderror"
                                                name="image_3" value="{{ old('image_3') }}">

                                            @error('image_3')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="image_4"
                                            class="col-md-4 col-form-label text-md-right">image_4</label>

                                        <div class="col-md-6">
                                            <input id="image_4" type="file"
                                                class="form-control @error('image_4') is-invalid @enderror"
                                                name="image_4" value="{{ old('image_4') }}">

                                            @error('image_4')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="audio_file"
                                            class="col-md-4 col-form-label text-md-right">audio_file</label>

                                        <div class="col-md-6">
                                            <input id="audio_file" type="file"
                                                class="form-control @error('audio_file') is-invalid @enderror"
                                                name="audio_file" value="{{ old('audio_file') }}">

                                            @error('audio_file')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 offset-md-4">
                                            <button type="submit" class="btn btn-primary">
                                                {{ __('Create') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{--  <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
    <script>
        $(document).ready(function() {
            /// $('select[name="categorie"]').multiselect();
            $('select[name="categorie"]').each(function() {
                $(this).multiselect();
            });
        });
    </script> --}}
@endsection
