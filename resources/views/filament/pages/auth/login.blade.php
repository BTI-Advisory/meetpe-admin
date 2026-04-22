<div>
<style>
    body { background: #fff !important; }

    /* Turn vertical layout into a left panel (460px) */
    .fi-simple-layout {
        flex-direction: row !important;
        align-items: stretch !important;
        min-height: 100vh !important;
    }

    .fi-simple-main-ctn {
        width: 460px !important;
        max-width: 460px !important;
        flex-grow: 0 !important;
        flex-shrink: 0 !important;
        justify-content: center !important;
        align-items: stretch !important;
        padding: 0 !important;
        background: #fff;
    }

    .fi-simple-main {
        width: 100% !important;
        max-width: 100% !important;
        min-height: 100vh !important;
        margin: 0 !important;
        padding: 48px 56px !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        outline: none !important;
        --tw-ring-shadow: none !important;
        --tw-shadow: none !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        font-family: 'Inter', sans-serif;
    }

    /* Hide Filament's default page header (logo + heading) */
    .fi-simple-header { display: none !important; }

    /* Our header inside the form slot */
    .meetpe-login-header .login-logo  { width: 140px; margin-bottom: 40px; display: block; }
    .meetpe-login-header .login-title { font-size: 1.6rem; font-weight: 700; color: #1a1a2e; margin-bottom: 6px; font-family: 'Inter', sans-serif; }
    .meetpe-login-header .login-sub   { font-size: 0.9rem; color: #6b7280; margin-bottom: 32px; font-family: 'Inter', sans-serif; }

    /* Style submit button with brand color */
    .fi-simple-main .fi-btn-primary,
    .fi-simple-main button[type="submit"][class*="fi-btn"] {
        background-color: #FF4C00 !important;
        border-radius: 10px !important;
        box-shadow: 0 4px 14px rgba(255,76,0,.35) !important;
    }
    .fi-simple-main .fi-btn-primary:hover { background-color: #e04300 !important; }

    /* Right panel */
    .meetpe-right-panel {
        position: fixed;
        top: 0; bottom: 0; right: 0;
        left: 460px;
        z-index: 10;
        background: linear-gradient(135deg, #FF4C00 0%, #ff8a50 60%, #ffb347 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 40px;
        overflow: hidden;
        font-family: 'Inter', sans-serif;
    }

    .meetpe-right-panel::before {
        content: '';
        position: absolute;
        width: 500px; height: 500px;
        border-radius: 50%;
        background: rgba(255,255,255,.07);
        top: -120px; right: -120px;
    }

    .meetpe-right-panel::after {
        content: '';
        position: absolute;
        width: 300px; height: 300px;
        border-radius: 50%;
        background: rgba(255,255,255,.05);
        bottom: -60px; left: -60px;
    }

    .meetpe-right-inner {
        position: relative;
        z-index: 1;
        text-align: center;
        color: #fff;
    }

    .meetpe-right-inner .big-icon { font-size: 5rem; margin-bottom: 28px; opacity: .9; }
    .meetpe-right-inner h2        { font-size: 2rem; font-weight: 700; margin-bottom: 14px; line-height: 1.25; }
    .meetpe-right-inner p         { font-size: 1rem; opacity: .85; max-width: 340px; line-height: 1.65; }

    .badge-list {
        display: flex; gap: 10px;
        justify-content: center; flex-wrap: wrap;
        margin-top: 30px;
    }

    .badge-item {
        background: rgba(255,255,255,.18);
        border: 1px solid rgba(255,255,255,.3);
        border-radius: 50px; padding: 6px 16px;
        font-size: 0.8rem; font-weight: 500; color: #fff;
        backdrop-filter: blur(4px);
    }

    @media (max-width: 768px) {
        .meetpe-right-panel { display: none !important; }
        .fi-simple-main-ctn { width: 100% !important; max-width: 100% !important; }
        .fi-simple-main { padding: 40px 28px !important; }
    }
</style>

{{-- Form kept inside page.simple so Livewire wiring stays intact --}}
<x-filament-panels::page.simple>
    <div class="meetpe-login-header" style="margin-bottom: 0;">
        <img src="{{ asset('img/LogoMeetpe.png') }}" alt="MeetPe" class="login-logo">
        <h1 class="login-title">Bon retour 👋</h1>
        <p class="login-sub">Connectez-vous à votre espace administrateur.</p>
    </div>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

    <x-filament-panels::form id="form" wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}
</x-filament-panels::page.simple>

{{-- Right decorative panel --}}
<div class="meetpe-right-panel">
    <div class="meetpe-right-inner">
        <div class="big-icon">🌍</div>
        <h2>Plateforme MeetPe</h2>
        <p>Gérez vos guides, expériences et voyageurs depuis votre espace d'administration.</p>
        <div class="badge-list">
            <span class="badge-item">✈️ Voyageurs</span>
            <span class="badge-item">🧭 Guides</span>
            <span class="badge-item">📅 Réservations</span>
            <span class="badge-item">⭐ Avis</span>
        </div>
    </div>
</div>
</div>
