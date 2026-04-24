<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alatika — Lupa Kata Sandi</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        body: ['"Inter"', 'sans-serif'],
                    },
                    colors: {
                        navy:      '#1E2B4A',
                        'navy-800':'#152038',
                        'navy-900':'#0D1B2E',
                        primary:   '#185FA5',
                        accent:    '#378ADD',
                        surface:   '#E6F1FB',
                        'blue-surface': '#F4F7FB',
                    },
                }
            }
        }
    </script>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #0D1B2E;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Grid background — same as landing page */
        .bg-grid {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background-image:
                linear-gradient(to right, rgba(55,138,221,0.08) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(55,138,221,0.08) 1px, transparent 1px);
            background-size: 44px 44px;
        }

        /* Decorative blobs */
        .blob {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(80px);
            opacity: 0.18;
            z-index: 0;
        }

        /* Card wrapper */
        .card-wrapper {
            position: relative;
            z-index: 10;
            width: 420px;
            max-width: 98vw;
            min-height: 480px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(55,138,221,0.18),
                0 32px 80px rgba(0,0,0,0.55),
                0 8px 24px rgba(0,0,0,0.3);
            background: #ffffff;
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 22px;
            font-weight: 800;
            color: #1E2B4A;
            margin-bottom: 6px;
            text-align: center;
        }

        .form-sub {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 28px;
            line-height: 1.5;
            text-align: center;
        }

        /* Input */
        .inp {
            width: 100%;
            padding: 11px 14px;
            background: #F4F7FB;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 13.5px;
            color: #1E2B4A;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            margin-bottom: 12px;
        }
        .inp:focus {
            border-color: #185FA5;
            box-shadow: 0 0 0 3px rgba(24,95,165,0.12);
            background: #fff;
        }
        .inp::placeholder { color: #94a3b8; font-size: 13px; }

        /* Label */
        .inp-label {
            font-size: 11.5px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 5px;
            display: block;
            letter-spacing: 0.3px;
        }

        /* Primary button */
        .btn-primary {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #1E2B4A 0%, #185FA5 100%);
            color: #fff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13.5px;
            font-weight: 700;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            letter-spacing: 0.5px;
            transition: all 0.25s ease;
            margin-top: 6px;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #152038 0%, #1a6bbf 100%);
            box-shadow: 0 8px 24px rgba(24,95,165,0.35);
            transform: translateY(-1px);
        }
        .btn-primary:active { transform: scale(0.98); }

        /* Error box */
        .error-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 12px;
            margin-bottom: 14px;
            line-height: 1.5;
        }

        /* Back link */
        .back-link {
            position: absolute;
            top: 16px;
            left: 16px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            transition: color 0.2s;
            z-index: 10;
        }
        .back-link:hover { color: #fff; }

        /* Input group with icon */
        .inp-group { position: relative; margin-bottom: 12px; }
        .inp-group .inp { padding-left: 40px; margin-bottom: 0; }
        .inp-group .inp-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 13px;
        }
    </style>
</head>
<body>

<!-- Background decorations -->
<div class="bg-grid"></div>
<div class="blob" style="width:400px;height:400px;background:#185FA5;top:-100px;left:-100px;"></div>
<div class="blob" style="width:350px;height:350px;background:#378ADD;bottom:-80px;right:-80px;opacity:0.12;"></div>

<!-- Back to login -->
<a href="{{ route('login-new') }}" class="back-link" style="position:fixed;top:24px;left:24px;z-index:100;
   background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);
   padding:8px 14px;border-radius:8px;backdrop-filter:blur(8px);">
    <i class="fas fa-arrow-left text-xs"></i>
    <span>Kembali</span>
</a>

<!-- Main card -->
<div class="card-wrapper">
    <div class="role-badge" style="display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #E6F1FB; color: #185FA5; margin-bottom: 20px; justify-content: center;">
        <i class="fas fa-key text-xs"></i>
        Reset Kata Sandi
    </div>
    <h2 class="form-title">Lupa Kata Sandi?</h2>
    <p class="form-sub">Masukkan NIM kamu dan kami akan mengirim link reset kata sandi ke email kamu.</p>

    @if($errors->has('email'))
    <div class="error-box">
        <i class="fas fa-circle-exclamation mr-1"></i>
        {{ $errors->first('email') }}
    </div>
    @endif

    @if(session('status'))
    <div class="error-box" style="background: #f0fdf4; border-color: #bbf7d0; color: #166534;">
        <i class="fas fa-check-circle mr-1"></i>
        {{ session('status') }}
    </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div>
            <label class="inp-label">NIM</label>
            <div class="inp-group">
                <input type="text" name="nim" class="inp" placeholder="NIM" value="{{ old('nim') }}" required pattern="[0-9]+" title="Hanya angka">
                <i class="fas fa-id-card inp-icon"></i>
            </div>
        </div>
        <button type="submit" class="btn-primary">
            <i class="fas fa-paper-plane mr-2"></i>Kirim Link Reset
        </button>
    </form>

    <div style="margin-top: 20px; text-align: center;">
        <a href="{{ route('login-new') }}" style="font-size:12px;color:#64748b;text-decoration:none;">
            <i class="fas fa-arrow-left mr-1"></i>Kembali ke Login
        </a>
    </div>
</div>

<script>
    // Simple validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const nim = document.querySelector('input[name="nim"]').value;
        if (!/^\d+$/.test(nim)) {
            e.preventDefault();
            alert('NIM harus berupa angka.');
        }
    });
</script>

</body>
</html>