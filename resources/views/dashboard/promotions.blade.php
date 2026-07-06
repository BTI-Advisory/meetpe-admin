@extends('dashboard.index')
@section('main')
<main class="main-content position-relative border-radius-lg">
    @include('dashboard.navbar')

    <div class="container-fluid py-4">

        {{-- Alertes --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Header --}}
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Promotions</h5>
                    <p class="text-sm text-secondary mb-0">
                        @if($active)
                            <span class="badge bg-gradient-success">Promo active : {{ $active->label }} — {{ $active->discount_percentage }}% de réduction</span>
                        @else
                            <span class="badge bg-gradient-secondary">Aucune promotion active</span>
                        @endif
                    </p>
                </div>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreatePromo">
                    + Nouvelle promotion
                </button>
            </div>
        </div>

        {{-- Tableau --}}
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nom</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Réduction</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Début</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Fin</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Statut</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($promotions as $promo)
                                        <tr>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0 ps-3">{{ $promo->label }}</p>
                                            </td>
                                            <td class="text-center">
                                                <span class="text-sm font-weight-bold text-primary">{{ $promo->discount_percentage }}%</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="text-xs text-secondary">
                                                    {{ $promo->starts_at ? $promo->starts_at->format('d/m/Y') : '—' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="text-xs text-secondary">
                                                    {{ $promo->ends_at ? $promo->ends_at->format('d/m/Y') : '—' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if($promo->is_active)
                                                    <span class="badge badge-sm bg-gradient-success">Actif</span>
                                                @elseif($promo->ends_at && $promo->ends_at->isPast())
                                                    <span class="badge badge-sm bg-gradient-secondary">Expiré</span>
                                                @elseif($promo->starts_at && $promo->starts_at->isFuture())
                                                    <span class="badge badge-sm bg-gradient-info">Planifié</span>
                                                @else
                                                    <span class="badge badge-sm bg-gradient-warning">Inactif</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{-- Toggle activer/désactiver --}}
                                                <form action="{{ route('promotions.toggle', $promo) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit"
                                                        class="btn btn-sm {{ $promo->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} py-1 px-2 mb-0"
                                                        title="{{ $promo->is_active ? 'Désactiver' : 'Activer' }}">
                                                        {{ $promo->is_active ? 'Désactiver' : 'Activer' }}
                                                    </button>
                                                </form>
                                                {{-- Supprimer --}}
                                                <form action="{{ route('promotions.destroy', $promo) }}" method="POST" class="d-inline"
                                                    onsubmit="return confirm('Supprimer cette promotion ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2 mb-0" title="Supprimer">
                                                        <i class="ni ni-fat-remove"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-secondary py-4">
                                                Aucune promotion créée pour l'instant.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

{{-- Modal création --}}
<div class="modal fade" id="modalCreatePromo" tabindex="-1" aria-labelledby="modalCreatePromoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('promotions.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title" id="modalCreatePromoLabel">Nouvelle promotion</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label text-xs font-weight-bolder text-uppercase">Nom de la promotion</label>
                        <input type="text" name="label" class="form-control" placeholder="Ex : Été 2026" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-xs font-weight-bolder text-uppercase">
                            Réduction : <span id="discountValue">10</span>%
                        </label>
                        <input type="range" name="discount_percentage" id="discountRange"
                            class="form-range" min="1" max="100" value="10"
                            oninput="document.getElementById('discountValue').textContent = this.value">
                        <div class="d-flex justify-content-between">
                            <small class="text-secondary">1%</small>
                            <small class="text-secondary">100%</small>
                        </div>
                    </div>

                    <div class="mb-3 p-3 bg-light border-radius-lg">
                        <p class="text-xs text-secondary mb-1">Aperçu du prix</p>
                        <p class="text-sm mb-0">
                            Un prix de <strong>100 €</strong> deviendra
                            <strong class="text-primary" id="previewPrice">90,00 €</strong>
                        </p>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <label class="form-label text-xs font-weight-bolder text-uppercase">Date début</label>
                            <input type="date" name="starts_at" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label text-xs font-weight-bolder text-uppercase">Date fin</label>
                            <input type="date" name="ends_at" class="form-control">
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary btn-sm">Créer la promotion</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('discountRange').addEventListener('input', function () {
        const pct = parseFloat(this.value);
        const result = (100 * (1 - pct / 100)).toFixed(2).replace('.', ',');
        document.getElementById('previewPrice').textContent = result + ' €';
    });
</script>
@endsection
