<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alatika — Masuk & Daftar</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #F5F8FF;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: fixed;
            width: 560px; height: 560px;
            background: radial-gradient(circle, rgba(55,138,221,0.10) 0%, transparent 65%);
            top: -160px; right: -120px;
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(24,95,165,0.07) 0%, transparent 65%);
            bottom: -100px; left: -80px;
            pointer-events: none;
        }

        /* ══════════════════════════════════════
           CARD — posisi relative, overflow hidden
           agar overlay tidak keluar card
        ══════════════════════════════════════ */
        .card-wrapper {
            position: relative;
            z-index: 10;
            width: 860px;
            max-width: 100%;
            height: 580px;
            border-radius: 24px;
            overflow: hidden;
            background: #fff;
            border: 1px solid #EBF3FD;
            box-shadow: 0 8px 48px rgba(30,43,74,0.10), 0 2px 12px rgba(30,43,74,0.06);
        }

        /* ══════════════════════════════════════
           DUA FORM PANEL
           Keduanya absolute, full height, lebar 50%.
           Login di kiri, Register di kanan.
        ══════════════════════════════════════ */
        .form-panel {
            position: absolute;
            top: 0;
            height: 100%;
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 48px 44px;
            background: #fff;
            transition: opacity 0.45s ease, transform 0.45s ease;
            z-index: 1;
        }

        /* Login — kiri, tampil default */
        .panel-login {
            left: 0;
            opacity: 1;
            transform: translateX(0);
            pointer-events: all;
        }

        /* Register — kanan, tersembunyi default */
        .panel-register {
            left: 50%;
            opacity: 0;
            transform: translateX(20px);
            pointer-events: none;
        }

        /* Register mode: login fade keluar */
        .card-wrapper.register-mode .panel-login {
            opacity: 0;
            transform: translateX(-20px);
            pointer-events: none;
        }

        /* Register mode: register fade masuk */
        .card-wrapper.register-mode .panel-register {
            opacity: 1;
            transform: translateX(0);
            pointer-events: all;
        }

        /* ══════════════════════════════════════
           OVERLAY BIRU
           Awalnya di kanan (left:50%).
           Saat register-mode: geser ke kiri (left:0).
           Transisi pada left + border-radius.
        ══════════════════════════════════════ */
        .overlay {
            position: absolute;
            top: 0;
            left: 50%;                /* default: di kanan */
            width: 50%;
            height: 100%;
            z-index: 10;
            background: linear-gradient(145deg, #1E2B4A 0%, #185FA5 55%, #378ADD 100%);
            transition: left 0.65s cubic-bezier(0.77, 0, 0.175, 1),
                        border-radius 0.65s cubic-bezier(0.77, 0, 0.175, 1);
            overflow: hidden;
        }

        /* Register mode: overlay geser ke kiri */
        .card-wrapper.register-mode .overlay {
            left: 0;
        }

        /* Dot grid dekoratif di dalam overlay */
        .overlay::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,0.06) 1px, transparent 1px);
            background-size: 24px 24px;
            pointer-events: none;
        }
        /* Glow dekoratif */
        .overlay::after {
            content: '';
            position: absolute;
            width: 320px; height: 320px;
            background: radial-gradient(circle, rgba(55,138,221,0.28) 0%, transparent 65%);
            top: -80px; right: -60px;
            pointer-events: none;
        }

        /* ── Isi overlay: dua konten fade bergantian ── */
        .overlay-content {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 44px 36px;
            text-align: center;
            z-index: 1;
            transition: opacity 0.3s ease 0.15s, transform 0.35s ease 0.15s;
        }

        /* Konten login: tampil default */
        .overlay-for-login {
            opacity: 1;
            transform: translateX(0);
            pointer-events: all;
        }
        /* Konten register: tersembunyi default */
        .overlay-for-register {
            opacity: 0;
            transform: translateX(20px);
            pointer-events: none;
        }

        .card-wrapper.register-mode .overlay-for-login {
            opacity: 0;
            transform: translateX(-20px);
            pointer-events: none;
            transition-delay: 0s;
        }
        .card-wrapper.register-mode .overlay-for-register {
            opacity: 1;
            transform: translateX(0);
            pointer-events: all;
        }

        /* ══════════════════════════════════════
           OVERLAY ELEMENTS
        ══════════════════════════════════════ */
        .overlay-logo-box {
            width: 60px; height: 60px;
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 22px;
        }
        .overlay-logo-box img { width: 38px; height: 38px; object-fit: contain; }

        .overlay-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 23px;
            font-weight: 800;
            color: #fff;
            line-height: 1.2;
            margin-bottom: 10px;
            letter-spacing: -0.02em;
        }
        .overlay-title span { color: #93c5fd; }

        .overlay-desc {
            font-size: 12.5px;
            color: rgba(255,255,255,0.62);
            line-height: 1.65;
            max-width: 215px;
            margin-bottom: 28px;
        }

        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 26px;
            background: rgba(255,255,255,0.10);
            color: #fff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13px;
            font-weight: 700;
            border: 1.5px solid rgba(255,255,255,0.35);
            border-radius: 10px;
            cursor: pointer;
            letter-spacing: .3px;
            transition: all .2s;
        }
        .btn-ghost:hover {
            background: rgba(255,255,255,0.18);
            border-color: rgba(255,255,255,0.7);
        }

        .overlay-dots {
            position: absolute;
            bottom: 24px;
            display: flex;
            gap: 5px;
        }
        .overlay-dots span {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: rgba(255,255,255,0.25);
            transition: all .35s;
        }
        .overlay-dots span.active {
            width: 18px;
            border-radius: 3px;
            background: rgba(255,255,255,0.85);
        }

        /* ══════════════════════════════════════
           FORM ELEMENTS
        ══════════════════════════════════════ */
        .form-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #185FA5;
            background: #EBF3FD;
            padding: 4px 12px;
            border-radius: 99px;
            margin-bottom: 16px;
            width: fit-content;
        }

        .form-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 22px;
            font-weight: 800;
            color: #1E2B4A;
            margin-bottom: 5px;
            letter-spacing: -0.02em;
        }
        .form-sub {
            font-size: 12.5px;
            color: #64748b;
            margin-bottom: 22px;
            line-height: 1.6;
        }

        .inp-label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 4px;
            letter-spacing: .02em;
        }
        .inp-group {
            position: relative;
            margin-bottom: 12px;
        }
        .inp-group .inp { padding-left: 40px; }

        .inp {
            width: 100%;
            padding: 10.5px 14px;
            background: #F5F8FF;
            border: 1.5px solid #D4E6F8;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            color: #1E2B4A;
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }
        .inp:focus {
            border-color: #378ADD;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(55,138,221,0.10);
        }
        .inp::placeholder { color: #A0BBCC; font-size: 12.5px; }

        select.inp {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%2394a3b8' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 15px;
            padding-right: 32px;
            cursor: pointer;
        }

        .inp-icon {
            position: absolute;
            left: 13px; top: 50%;
            transform: translateY(-50%);
            color: #B5D4F4;
            font-size: 12px;
            pointer-events: none;
            transition: color .2s;
        }
        .inp-group:focus-within .inp-icon { color: #378ADD; }

        .toggle-pw {
            position: absolute;
            right: 12px; top: 50%;
            transform: translateY(-50%);
            color: #B5D4F4;
            font-size: 12px;
            cursor: pointer;
            transition: color .15s;
        }
        .toggle-pw:hover { color: #185FA5; }

        .btn-primary {
            width: 100%;
            padding: 11.5px;
            background: #1E2B4A;
            color: #fff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13px;
            font-weight: 700;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            letter-spacing: .3px;
            transition: all .2s;
            margin-top: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-primary:hover {
            background: #185FA5;
            box-shadow: 0 8px 22px rgba(24,95,165,0.28);
            transform: translateY(-1px);
        }

        .error-box {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #DC2626;
            padding: 9px 13px;
            border-radius: 9px;
            font-size: 11.5px;
            margin-bottom: 14px;
            line-height: 1.5;
        }

        .link-subtle {
            font-size: 11.5px;
            color: #64748b;
            text-decoration: none;
            transition: color .15s;
            margin-bottom: 12px;
            display: inline-block;
        }
        .link-subtle:hover { color: #185FA5; }

        .divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 6px 0 12px;
            font-size: 11px;
            color: #B5D4F4;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 1px; background: #EBF3FD;
        }

        /* ── Back link ── */
        .back-link {
            position: fixed;
            top: 20px; left: 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            background: #fff;
            border: 1px solid #EBF3FD;
            padding: 7px 14px;
            border-radius: 9px;
            box-shadow: 0 2px 10px rgba(30,43,74,0.07);
            transition: all .2s;
            z-index: 200;
        }
        .back-link:hover { color: #185FA5; border-color: #B5D4F4; }

        /* ── Toast ── */
        .toast {
            position: fixed;
            top: 20px; right: 20px;
            background: #fff;
            border: 1px solid #B5D4F4;
            color: #185FA5;
            padding: 12px 18px;
            border-radius: 12px;
            font-size: 13px;
            z-index: 9999;
            box-shadow: 0 8px 24px rgba(30,43,74,0.10);
            display: flex;
            align-items: center;
            gap: 10px;
            animation: toastIn .3s ease;
        }
        @keyframes toastIn {
            from { transform: translateX(80px); opacity: 0; }
            to   { transform: translateX(0); opacity: 1; }
        }

        /* ── Mobile ── */
        @media (max-width: 640px) {
            .card-wrapper { height: auto; min-height: unset; border-radius: 20px; }
            .form-panel   { position: relative; width: 100%; height: auto; left: 0 !important;
                            opacity: 1 !important; transform: none !important; pointer-events: all !important; }
            .panel-register { display: none; }
            .card-wrapper.register-mode .panel-login    { display: none; }
            .card-wrapper.register-mode .panel-register { display: flex; }
            .overlay { display: none; }
        }
    </style>
</head>
<body>

@if(session('success'))
<div class="toast" id="toast">
    <i class="fas fa-check-circle" style="color:#378ADD;"></i>
    {{ session('success') }}
</div>
@endif

<a href="{{ url('/') }}" class="back-link">
    <i class="fas fa-arrow-left text-xs"></i>
    Kembali
</a>

<!-- ══════════ CARD ══════════ -->
<div class="card-wrapper" id="cardWrapper">

    <!-- ── PANEL LOGIN (kiri) ── -->
    <div class="form-panel panel-login">

        <h2 class="form-title">Selamat datang</h2>
        <p class="form-sub">Masuk ke akun Alatika kamu untuk mengelola peminjaman alat lab.</p>

        @if($errors->has('nim') && !old('name'))
        <div class="error-box">
            <i class="fas fa-circle-exclamation mr-1"></i>
            {{ $errors->first('nim') }}
        </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <label class="inp-label">NIM / NIP / Email</label>
<div class="inp-group">
    <input type="text" name="nim" class="inp" placeholder="Masukkan NIM, NIP, atau Email"
           value="{{ old('nim') }}" required>
    <i class="fas fa-id-card inp-icon"></i>
</div>

            <label class="inp-label">Password</label>
            <div class="inp-group">
                <input type="password" name="password" class="inp" placeholder="Kata sandi" id="pwLogin" required>
                <i class="fas fa-lock inp-icon"></i>
                <span class="toggle-pw" onclick="togglePw('pwLogin',this)"><i class="fas fa-eye"></i></span>
            </div>

            <a href="#" class="link-subtle">Lupa kata sandi?</a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-right-to-bracket"></i> Masuk
            </button>
        </form>

        <div class="divider">atau</div>
        <p style="font-size:12px;color:#64748b;text-align:center;">
            Belum punya akun?
            <span onclick="setMode(true)" style="color:#185FA5;font-weight:700;cursor:pointer;">Daftar sekarang</span>
        </p>
    </div>

    <!-- ── PANEL REGISTER (kanan) ── -->
    <div class="form-panel panel-register">
        <h2 class="form-title">Daftar Alatika</h2>
        <p class="form-sub">Isi data diri kamu untuk mulai meminjam alat laboratorium.</p>

        @if($errors->any() && old('name'))
        <div class="error-box">
            <i class="fas fa-circle-exclamation mr-1"></i>
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <label class="inp-label">Nama Lengkap</label>
            <div class="inp-group">
                <input type="text" name="name" class="inp" placeholder="Nama lengkap kamu"
                       value="{{ old('name') }}" required>
                <i class="fas fa-user inp-icon"></i>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div>
                    <label class="inp-label">Peran / Role</label>
                    <select name="role" id="roleSelect" class="inp" required>
                        <option value="mahasiswa" {{ old('role')=='mahasiswa'?'selected':'' }}>Mahasiswa</option>
                        <option value="dosen"     {{ old('role')=='dosen'    ?'selected':'' }}>Dosen</option>
                    </select>
                </div>
                <div>
                    <label class="inp-label" id="nimLabel">NIM</label>
                    <div class="inp-group" style="margin-bottom:0;">
                        <input type="text" name="nomor_induk" id="nomorInduk" class="inp"
                               placeholder="NIM" value="{{ old('nomor_induk') }}"
                               required pattern="[0-9]+" title="Hanya angka">
                        <i class="fas fa-id-card inp-icon"></i>
                    </div>
                </div>
            </div>

            <div style="margin-top:12px;">
                <label class="inp-label">Email</label>
                <div class="inp-group">
                    <input type="email" name="email" class="inp" placeholder="nama@email.com"
                           value="{{ old('email') }}" required>
                    <i class="fas fa-envelope inp-icon"></i>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div>
                    <label class="inp-label">Password</label>
                    <div class="inp-group">
                        <input type="password" name="password" class="inp" placeholder="Kata sandi" id="pwReg" required>
                        <i class="fas fa-lock inp-icon"></i>
                        <span class="toggle-pw" onclick="togglePw('pwReg',this)"><i class="fas fa-eye"></i></span>
                    </div>
                </div>
                <div>
                    <label class="inp-label">Konfirmasi</label>
                    <div class="inp-group">
                        <input type="password" name="password_confirmation" class="inp" placeholder="Ulangi" required>
                        <i class="fas fa-lock inp-icon"></i>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-primary">
                <i class="fas fa-user-plus"></i> Buat Akun
            </button>
        </form>

        <div class="divider">atau</div>
        <p style="font-size:12px;color:#64748b;text-align:center;">
            Sudah punya akun?
            <span onclick="setMode(false)" style="color:#185FA5;font-weight:700;cursor:pointer;">Masuk di sini</span>
        </p>
    </div>

    <!-- ══════════ OVERLAY BIRU ══════════
         Default: di kanan (left:50%)
         Register mode: geser ke kiri (left:0)
    ═══════════════════════════════════ -->
    <div class="overlay" id="overlay">

        {{-- Konten saat LOGIN aktif: ajak daftar --}}
        <div class="overlay-content overlay-for-login">
            <div class="overlay-logo-box">
                <img src="{{ asset('images/logo-polines.png') }}" alt="Logo Polines">
            </div>
            <h2 class="overlay-title">Belum punya<br>akun <span>Alatika</span>?</h2>
            <p class="overlay-desc">Daftar sekarang dan nikmati kemudahan peminjaman alat lab secara digital.</p>
            <button class="btn-ghost" onclick="setMode(true)">
                <i class="fas fa-user-plus"></i> Daftar Sekarang
            </button>
            <div class="overlay-dots">
                <span class="active"></span>
                <span></span>
            </div>
        </div>

        {{-- Konten saat REGISTER aktif: ajak login --}}
        <div class="overlay-content overlay-for-register">
            <div class="overlay-logo-box">
                <img src="{{ asset('images/logo-polines.png') }}" alt="Logo Polines">
            </div>
            <h2 class="overlay-title">Sudah punya<br>akun <span>Alatika</span>?</h2>
            <p class="overlay-desc">Masuk ke akunmu dan kelola peminjaman alat lab kapan saja, di mana saja.</p>
            <button class="btn-ghost" onclick="setMode(false)">
                <i class="fas fa-right-to-bracket"></i> Masuk Sekarang
            </button>
            <div class="overlay-dots">
                <span></span>
                <span class="active"></span>
            </div>
        </div>

    </div>

</div>

<script>
    const card = document.getElementById('cardWrapper');

    function setMode(isRegister) {
        card.classList.toggle('register-mode', isRegister);
    }

    function togglePw(id, el) {
        const inp  = document.getElementById(id);
        const icon = el.querySelector('i');
        const show = inp.type === 'password';
        inp.type = show ? 'text' : 'password';
        icon.classList.toggle('fa-eye',       !show);
        icon.classList.toggle('fa-eye-slash',  show);
        el.style.color = show ? '#185FA5' : '#B5D4F4';
    }

    // NIM ↔ NIP
    const roleSelect = document.getElementById('roleSelect');
    const nomorInduk = document.getElementById('nomorInduk');
    const nimLabel   = document.getElementById('nimLabel');
    if (roleSelect) {
        roleSelect.addEventListener('change', function () {
            const d = this.value === 'dosen';
            nomorInduk.placeholder = d ? 'NIP' : 'NIM';
            nimLabel.textContent   = d ? 'NIP' : 'NIM';
        });
        roleSelect.dispatchEvent(new Event('change'));
    }

    @if($errors->any() && old('name'))
        setMode(true);
    @endif

    const toast = document.getElementById('toast');
    if (toast) {
        setTimeout(() => {
            toast.style.transition = 'opacity .4s';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 400);
        }, 4500);
    }
</script>
</body>
</html>