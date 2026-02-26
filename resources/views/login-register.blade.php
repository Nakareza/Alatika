<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register Slide</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;800&display=swap');

        * {
            box-sizing: border-box;
        }

        body {
            background: #f6f5f7;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            font-family: 'Montserrat', sans-serif;
            height: 100vh;
            margin: -20px 0 50px;
        }

        h1 {
            font-weight: bold;
            margin: 0;
        }

        p {
            font-size: 14px;
            font-weight: 100;
            line-height: 20px;
            letter-spacing: 0.5px;
            margin: 20px 0 30px;
        }

        span {
            font-size: 12px;
        }

        a {
            color: #333;
            font-size: 14px;
            text-decoration: none;
            margin: 15px 0;
        }

        button {
            border-radius: 20px;
            border: 1px solid #4C6EF5; /* Warna Biru Utama */
            background-color: #4C6EF5;
            color: #FFFFFF;
            font-size: 12px;
            font-weight: bold;
            padding: 12px 45px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: transform 80ms ease-in;
            cursor: pointer;
        }

        button:active {
            transform: scale(0.95);
        }

        button:focus {
            outline: none;
        }

        button.ghost {
            background-color: transparent;
            border-color: #FFFFFF;
            position: relative;
            z-index: 1;
            cursor: pointer;
        }

        button.ghost:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        form {
            background-color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 50px;
            height: 100%;
            text-align: center;
        }

        form h1 {
            margin-bottom: 20px;
        }

        input {
            background-color: #eee;
            border: none;
            padding: 12px 15px;
            margin: 8px 0;
            width: 100%;
            border-radius: 5px; /* Sedikit rounded di input */
        }

        /* Container Utama */
        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 14px 28px rgba(0,0,0,0.25), 
                        0 10px 10px rgba(0,0,0,0.22);
            position: relative;
            overflow: hidden;
            width: 768px;
            max-width: 100%;
            min-height: 480px;
        }

        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }

        /* Sign In Container (Default Visible) */
        .sign-in-container {
            left: 0;
            width: 50%;
            z-index: 2;
        }

        .container.right-panel-active .sign-in-container {
            transform: translateX(100%);
        }

        /* Sign Up Container (Default Hidden) */
        .sign-up-container {
            left: 0;
            width: 50%;
            opacity: 0;
            z-index: 1;
        }

        .container.right-panel-active .sign-up-container {
            transform: translateX(100%);
            opacity: 1;
            z-index: 5;
            animation: show 0.6s;
        }

        @keyframes show {
            0%, 49.99% {
                opacity: 0;
                z-index: 1;
            }
            50%, 100% {
                opacity: 1;
                z-index: 5;
            }
        }

        /* Overlay Container (Bagian Biru Bergerak) */
        .overlay-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.6s ease-in-out;
            z-index: 100;
            pointer-events: auto;
        }

        .container.right-panel-active .overlay-container {
            transform: translateX(-100%);
        }

        .overlay {
            background: #4C6EF5;
            background: -webkit-linear-gradient(to right, #5B86E5, #36D1DC);
            background: linear-gradient(to right, #5B86E5, #36D1DC); /* Gradient Biru seperti di video */
            background-repeat: no-repeat;
            background-size: cover;
            background-position: 0 0;
            color: #FFFFFF;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }

        .container.right-panel-active .overlay {
            transform: translateX(50%);
        }

        .overlay-panel {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 40px;
            text-align: center;
            top: 0;
            height: 100%;
            width: 50%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
            z-index: 1;
        }

        .overlay-panel button {
            pointer-events: auto;
            z-index: 10;
        }

        .overlay-left {
            transform: translateX(-20%);
        }

        .container.right-panel-active .overlay-left {
            transform: translateX(0);
        }

        .overlay-right {
            right: 0;
            transform: translateX(0);
        }

        .container.right-panel-active .overlay-right {
            transform: translateX(20%);
        }

        .social-container {
            margin: 20px 0;
        }

        .social-container a {
            border: 1px solid #DDDDDD;
            border-radius: 50%;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin: 0 5px;
            height: 40px;
            width: 40px;
        }

        /* Logo Polines */
        .logo-polines {
            position: absolute;
            top: 15px;
            left: 15px;
            width: 50px;
            height: auto;
            z-index: 150;
            transition: all 0.6s ease-in-out;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.15));
        }

        /* Logo pindah ke kanan atas saat register aktif */
        .container.right-panel-active .logo-polines {
            left: calc(100% - 65px);
        }
    </style>
</head>
<body>

@if(session('success'))
    <div style="position: fixed; top: 20px; right: 20px; background: #4CAF50; color: white; padding: 15px 20px; border-radius: 5px; z-index: 9999; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
        {{ session('success') }}
    </div>
@endif

<div class="container" id="container">
    
    <!-- Logo Polines -->
    <img src="{{ asset('images/logo-polines.png') }}" alt="Logo Polines" class="logo-polines">

    <div class="form-container sign-up-container">
        <form action="{{ route('register') }}" method="POST">
            @csrf
            <h1>Buat akun</h1>
            <div class="social-container">
                <a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
                <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
            </div>
            <span>Atau gunakan email Anda untuk mendaftar</span>
            
            @if($errors->any() && old('_token') && request()->route()->getName() !== 'login')
                <div style="background: #f44336; color: white; padding: 10px; border-radius: 5px; margin: 10px 0; font-size: 12px;">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
            
            <input type="text" name="name" placeholder="Nama Lengkap" value="{{ old('name') }}" required />
            <input type="text" name="nim" placeholder="NIM (Nomor Induk Mahasiswa)" value="{{ old('nim') }}" required />
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required />
            <input type="password" name="password" placeholder="Kata Sandi" required />
            <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required />
            <button type="submit">Daftar</button>
        </form>
    </div>
    <div class="form-container sign-in-container">
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <h1>Masuk</h1>
            <div class="social-container">
                <a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
                <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
            </div>
            <span>or use your account</span>
            
            @if($errors->has('email') && !old('name'))
                <div style="background: #f44336; color: white; padding: 10px; border-radius: 5px; margin: 10px 0; font-size: 12px;">
                    {{ $errors->first('email') }}
                </div>
            @endif
            
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required />
            <input type="password" name="password" placeholder="Password" required />
            <a href="#">Lupa kata sandi?</a>
            <button type="submit">Masuk</button>
        </form>
    </div>

    <div class="overlay-container">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h1>Selamat Datang Kembali!</h1>
                <p>Tetap terhubung dengan kami, silakan masuk dengan info pribadi Anda</p>
                <button type="button" class="ghost" id="signIn">Masuk</button>
            </div>
            <div class="overlay-panel overlay-right">
                <h1>Halo, Teman!</h1>
                <p>Masukkan detail pribadi Anda dan mulai perjalanan bersama kami</p>
                <button type="button" class="ghost" id="signUp">Daftar</button>
            </div>
        </div>
    </div>
</div>

<script>
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('container');

    console.log('signUpButton:', signUpButton);
    console.log('signInButton:', signInButton);

    // Tambahkan class 'right-panel-active' untuk menggeser ke mode Register
    if (signUpButton) {
        signUpButton.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('Sign Up clicked');
            container.classList.add("right-panel-active");
        });
    }

    // Hapus class 'right-panel-active' untuk kembali ke mode Login
    if (signInButton) {
        signInButton.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('Sign In clicked');
            container.classList.remove("right-panel-active");
        });
    }

    // Auto-hide success message after 5 seconds
    setTimeout(() => {
        const successMsg = document.querySelector('[style*="position: fixed"]');
        if (successMsg) {
            successMsg.style.opacity = '0';
            successMsg.style.transition = 'opacity 0.5s';
            setTimeout(() => successMsg.remove(), 500);
        }
    }, 5000);

    // Show register panel if there are registration errors
    @if($errors->any() && old('name'))
        container.classList.add("right-panel-active");
    @endif
</script>

</body>
</html>