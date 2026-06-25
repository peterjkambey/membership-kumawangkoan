<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - Kumawangkoan</title>
    @vite('resources/css/filament/admin/theme.css')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #0f0f1a;
            overflow: hidden;
        }

        /* ── Left Side: Brand Panel ── */
        .brand-panel {
            flex: 1;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 4rem;
            position: relative;
            overflow: hidden;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 50%, rgba(212, 168, 83, 0.08) 0%, transparent 50%),
                        radial-gradient(circle at 70% 50%, rgba(45, 106, 107, 0.08) 0%, transparent 50%);
            animation: bgFloat 20s ease-in-out infinite;
        }

        @keyframes bgFloat {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(-2%, -2%) rotate(1deg); }
            66% { transform: translate(2%, 2%) rotate(-1deg); }
        }

        .brand-content {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .brand-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--kuma-gold, #d4a853), var(--kuma-gold-dark, #b8913a));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            box-shadow: 0 20px 40px rgba(212, 168, 83, 0.3);
            animation: logoFloat 3s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .brand-logo svg {
            width: 40px;
            height: 40px;
            color: white;
        }

        .brand-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.5rem;
        }

        .brand-title span {
            background: linear-gradient(135deg, #d4a853, #e8c97a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-subtitle {
            color: rgba(255, 255, 255, 0.6);
            font-size: 1rem;
            font-weight: 300;
            letter-spacing: 0.05em;
            max-width: 320px;
            line-height: 1.6;
        }

        .brand-decoration {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 1;
        }

        .brand-decoration span {
            width: 6px;
            height: 6px;
            background: rgba(212, 168, 83, 0.3);
            border-radius: 50%;
            animation: dotPulse 2s ease-in-out infinite;
        }

        .brand-decoration span:nth-child(2) { animation-delay: 0.3s; }
        .brand-decoration span:nth-child(3) { animation-delay: 0.6s; }

        @keyframes dotPulse {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.5); }
        }

        /* ── Right Side: Login Form ── */
        .form-panel {
            width: 480px;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4rem;
            position: relative;
        }

        .form-panel::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #d4a853, #2d6a6b, #1a1a2e);
        }

        .form-header {
            margin-bottom: 2.5rem;
        }

        .form-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-group .input-wrapper {
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s ease;
            background: #fafafa;
            outline: none;
        }

        .form-group input:focus {
            border-color: #d4a853;
            box-shadow: 0 0 0 4px rgba(212, 168, 83, 0.12);
            background: white;
        }

        .form-group input::placeholder {
            color: #cbd5e1;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .form-options label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: #64748b;
            cursor: pointer;
        }

        .form-options input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #d4a853;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-submit {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, #d4a853, #b8913a);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(212, 168, 83, 0.35);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .btn-submit:active::after {
            width: 300px;
            height: 300px;
        }

        .form-footer {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.8rem;
            color: #94a3b8;
        }

        .form-footer a {
            color: #d4a853;
            text-decoration: none;
            font-weight: 500;
        }

        .error-message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
            color: #dc2626;
            font-size: 0.85rem;
            display: none;
        }

        .error-message.show {
            display: block;
            animation: shake 0.4s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .brand-panel { display: none; }
            .form-panel { width: 100%; padding: 3rem 2rem; }
            .form-panel::after { width: 100%; height: 4px; }
        }
    </style>
</head>
<body>
    <div class="brand-panel">
        <div class="brand-content">
            <div class="brand-logo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </div>
            <h1 class="brand-title">Perkumpulan<br><span>Kumawangkoan</span></h1>
            <p class="brand-subtitle">Sistem informasi keanggotaan dan iuran berbasis digital untuk seluruh warga.</p>
        </div>
        <div class="brand-decoration">
            <span></span><span></span><span></span>
        </div>
    </div>

    <div class="form-panel">
        <div class="form-header">
            <h1>Selamat Datang</h1>
            <p>Masuk ke dashboard administrasi</p>
        </div>

        @if($errors->any())
            <div class="error-message show">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('filament.admin.auth.login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-wrapper">
                    <input type="email" name="email" id="email" placeholder="admin@kumawangkoan.org" value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Kata Sandi</label>
                <div class="input-wrapper">
                    <input type="password" name="password" id="password" placeholder="Masukkan kata sandi" required>
                </div>
            </div>

            <div class="form-options">
                <label>
                    <input type="checkbox" name="remember" id="remember">
                    Ingat saya
                </label>
            </div>

            <button type="submit" class="btn-submit">Masuk ke Dashboard</button>
        </form>

        <div class="form-footer">
            &copy; {{ date('Y') }} Perkumpulan Kumawangkoan &mdash; All rights reserved
        </div>
    </div>
</body>
</html>
