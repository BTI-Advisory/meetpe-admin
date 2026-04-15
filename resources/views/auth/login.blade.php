<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MeetPe — Connexion</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('img/LogoMeetpe.png') }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
        }

        /* ── Panneau gauche (formulaire) ─────────────────────────── */
        .login-left {
            width: 100%;
            max-width: 480px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 48px 56px;
            background: #fff;
            position: relative;
            z-index: 1;
        }

        .login-logo {
            width: 140px;
            margin-bottom: 40px;
        }

        .login-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 6px;
        }

        .login-subtitle {
            font-size: 0.9rem;
            color: #6b7280;
            margin-bottom: 36px;
        }

        /* ── Champs ────────────────────────────────────────────────── */
        .field-group {
            margin-bottom: 18px;
        }

        .field-group label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
            letter-spacing: .02em;
        }

        .field-group .input-wrap {
            position: relative;
        }

        .field-group .input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: .85rem;
        }

        .field-group input {
            width: 100%;
            padding: 12px 14px 12px 38px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            color: #1f2937;
            background: #fafafa;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }

        .field-group input:focus {
            border-color: #FF4C00;
            box-shadow: 0 0 0 3px rgba(255, 76, 0, .12);
            background: #fff;
        }

        .field-group input::placeholder { color: #d1d5db; }

        /* ── Erreurs ───────────────────────────────────────────────── */
        .field-error {
            font-size: 0.78rem;
            color: #ef4444;
            margin-top: 5px;
        }

        /* ── Remember me ──────────────────────────────────────────── */
        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 28px;
        }

        .remember-row input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #FF4C00;
            cursor: pointer;
        }

        .remember-row label {
            font-size: 0.85rem;
            color: #6b7280;
            cursor: pointer;
        }

        /* ── Bouton ────────────────────────────────────────────────── */
        .btn-login {
            width: 100%;
            padding: 13px;
            background: #FF4C00;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            letter-spacing: .03em;
            transition: background .2s, transform .1s, box-shadow .2s;
            box-shadow: 0 4px 14px rgba(255, 76, 0, .35);
        }

        .btn-login:hover {
            background: #e04300;
            box-shadow: 0 6px 18px rgba(255, 76, 0, .45);
        }

        .btn-login:active { transform: scale(.98); }

        /* ── Panneau droit (illustration) ─────────────────────────── */
        .login-right {
            flex: 1;
            background: linear-gradient(135deg, #FF4C00 0%, #ff8a50 60%, #ffb347 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
            position: relative;
            overflow: hidden;
        }

        .login-right::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: rgba(255,255,255,.07);
            top: -120px;
            right: -120px;
        }

        .login-right::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255,255,255,.05);
            bottom: -60px;
            left: -60px;
        }

        .login-right-content {
            position: relative;
            z-index: 1;
            text-align: center;
            color: #fff;
        }

        .login-right-content .big-icon {
            font-size: 5rem;
            margin-bottom: 28px;
            opacity: .9;
        }

        .login-right-content h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 14px;
            line-height: 1.25;
        }

        .login-right-content p {
            font-size: 1rem;
            opacity: .85;
            max-width: 340px;
            line-height: 1.65;
        }

        .badge-list {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .badge-item {
            background: rgba(255,255,255,.18);
            border: 1px solid rgba(255,255,255,.3);
            border-radius: 50px;
            padding: 6px 16px;
            font-size: 0.8rem;
            font-weight: 500;
            color: #fff;
            backdrop-filter: blur(4px);
        }

        /* ── Responsive ────────────────────────────────────────────── */
        @media (max-width: 768px) {
            .login-right { display: none; }
            .login-left {
                max-width: 100%;
                padding: 40px 28px;
            }
        }
    </style>
</head>
<body>

    <!-- ── Panneau formulaire ───────────────────────────── -->
    <div class="login-left">

        <img src="{{ asset('img/LogoMeetpe.png') }}" alt="MeetPe" class="login-logo">

        <h1 class="login-title">Bon retour 👋</h1>
        <p class="login-subtitle">Connectez-vous à votre espace administrateur.</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="field-group">
                <label for="email">Adresse e-mail</label>
                <div class="input-wrap">
                    <i class="fa-regular fa-envelope"></i>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="vous@exemple.com"
                        required
                        autofocus
                        autocomplete="username"
                    >
                </div>
                @error('email')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mot de passe -->
            <div class="field-group">
                <label for="password">Mot de passe</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password"
                    >
                </div>
                @error('password')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember me -->
            <div class="remember-row">
                <input type="checkbox" id="remember_me" name="remember">
                <label for="remember_me">Rester connecté</label>
            </div>

            <button type="submit" class="btn-login">
                Se connecter
            </button>
        </form>

    </div>

    <!-- ── Panneau illustration ─────────────────────────── -->
    <div class="login-right">
        <div class="login-right-content">
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

</body>
</html>
