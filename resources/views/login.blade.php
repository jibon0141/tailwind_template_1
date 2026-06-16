<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | Tahsin Pharma</title>
    <script src="{{ asset('assets/backend_assets/js/tailwind/tailwind.js') }}"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0b1120;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow-x: hidden;
        }

        .bg-grid {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(59,130,246,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59,130,246,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            z-index: 0;
        }

        .bg-glow {
            position: fixed;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.15;
            z-index: 0;
            pointer-events: none;
        }
        .bg-glow-1 { top: -200px; left: -200px; background: #3b82f6; }
        .bg-glow-2 { bottom: -200px; right: -200px; background: #8b5cf6; }
        .bg-glow-3 { top: 50%; left: 50%; transform: translate(-50%, -50%); background: #1d4ed8; width: 400px; height: 400px; opacity: 0.05; }

        .login-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 1.25rem;
            padding: 2.5rem 2rem;
            box-shadow:
                0 24px 80px rgba(0,0,0,0.4),
                inset 0 1px 0 rgba(255,255,255,0.06);
            animation: cardIn 0.6s ease both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(24px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .brand-icon {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(59,130,246,0.3);
        }
        .brand-icon span { color: #fff; font-weight: 800; font-size: 1.375rem; }

        .form-group { position: relative; }
        .form-group .icon {
            position: absolute;
            left: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.3);
            font-size: 0.875rem;
            pointer-events: none;
            transition: color 0.2s;
        }
        .form-group:focus-within .icon { color: #60a5fa; }

        .form-input {
            width: 100%;
            padding: 0.75rem 0.875rem 0.75rem 2.5rem;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 0.75rem;
            color: #fff;
            font-size: 0.875rem;
            transition: all 0.25s ease;
            outline: none;
        }
        .form-input::placeholder { color: rgba(255,255,255,0.25); }
        .form-input:hover { background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.15); }
        .form-input:focus {
            background: rgba(255,255,255,0.1);
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15), 0 4px 16px rgba(59,130,246,0.1);
        }

        .btn-signin {
            width: 100%;
            padding: 0.8125rem;
            border: none;
            border-radius: 0.75rem;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: #fff;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 16px rgba(59,130,246,0.3);
            position: relative;
            overflow: hidden;
        }
        .btn-signin:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 28px rgba(59,130,246,0.4);
        }
        .btn-signin:active { transform: translateY(0); }
        .btn-signin.loading {
            pointer-events: none;
            opacity: 0.8;
        }
        .btn-signin .spinner {
            display: none;
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            position: absolute;
        }
        .btn-signin.loading .spinner { display: block; }
        .btn-signin.loading .btn-text { opacity: 0; }

        @keyframes spin { to { transform: rotate(360deg); } }

        .register-link {
            display: block;
            width: 100%;
            text-align: center;
            padding: 0.8125rem;
            border-radius: 0.75rem;
            border: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.7);
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .register-link:hover {
            background: rgba(255,255,255,0.05);
            border-color: rgba(255,255,255,0.2);
            color: #fff;
        }

        .animate-in {
            animation: fadeUp 0.5s ease both;
        }
        .animate-in-d1 { animation-delay: 0.1s; }
        .animate-in-d2 { animation-delay: 0.2s; }
        .animate-in-d3 { animation-delay: 0.3s; }
        .animate-in-d4 { animation-delay: 0.4s; }
        .animate-in-d5 { animation-delay: 0.5s; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert {
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.8125rem;
            display: flex;
            align-items: center;
            gap: 0.625rem;
            animation: fadeUp 0.3s ease both;
        }
        .alert-error { background: rgba(220,38,38,0.12); border: 1px solid rgba(220,38,38,0.2); color: #fca5a5; }
        .alert-success { background: rgba(22,163,74,0.12); border: 1px solid rgba(22,163,74,0.2); color: #86efac; }
    </style>
</head>

<body>

<div class="bg-grid"></div>
<div class="bg-glow bg-glow-1"></div>
<div class="bg-glow bg-glow-2"></div>
<div class="bg-glow bg-glow-3"></div>

<div class="login-card">

    <div class="text-center mb-7 animate-in">
        <div class="brand-icon mx-auto mb-4">
            <span>T</span>
        </div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Welcome Back</h1>
        <p class="text-sm text-blue-200/60 mt-1">Sign in to your account</p>
    </div>

    @if ($message = session('error'))
        <div class="alert alert-error mb-5 animate-in animate-in-d1">
            <i class="fa-solid fa-circle-exclamation flex-shrink-0"></i>
            <span>{{ $message }}</span>
        </div>
    @endif
    @if ($message = session('success'))
        <div class="alert alert-success mb-5 animate-in animate-in-d1">
            <i class="fa-solid fa-circle-check flex-shrink-0"></i>
            <span>{{ $message }}</span>
        </div>
    @endif

    <form action="{{ url('/login') }}" method="POST" class="space-y-4" id="login-form">
        @csrf

        <div class="form-group animate-in animate-in-d1">
            <label class="block text-sm font-medium text-blue-100/70 mb-1.5" for="email">Email</label>
            <div class="relative">
                <i class="fa-regular fa-envelope icon"></i>
                <input type="email" name="email" id="email" required
                       placeholder="you@example.com"
                       value="{{ old('email') }}"
                       class="form-input">
            </div>
            @error('email')
            <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
            </p>
            @enderror
        </div>

        <div class="form-group animate-in animate-in-d2">
            <label class="block text-sm font-medium text-blue-100/70 mb-1.5" for="password">Password</label>
            <div class="relative">
                <i class="fa-solid fa-lock icon"></i>
                <input type="password" name="password" id="password" required
                       placeholder="Enter your password"
                       class="form-input pr-10">
                <button type="button" onclick="togglePassword()"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-blue-400/70 hover:text-blue-300 transition text-sm">
                    <i class="fa-solid fa-eye" id="eye-icon"></i>
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between animate-in animate-in-d3">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" id="remember_me"
                       class="w-4 h-4 rounded border-white/20 bg-white/5 accent-blue-500 text-blue-600 focus:ring-blue-500/30">
                <span class="text-white/50 text-sm">Remember me</span>
            </label>
        </div>

        <button type="submit" class="btn-signin animate-in animate-in-d4" id="signin-btn">
            <div class="spinner"></div>
            <span class="btn-text"><i class="fa-solid fa-arrow-right-to-bracket mr-2"></i>Sign In</span>
        </button>
    </form>

    <div class="flex items-center gap-3 my-6 animate-in animate-in-d5">
        <div class="flex-1 h-px bg-white/5"></div>
        <span class="text-white/30 text-xs whitespace-nowrap">New here?</span>
        <div class="flex-1 h-px bg-white/5"></div>
    </div>

    <a href="{{ url('/login') }}" class="register-link animate-in animate-in-d5">
        <i class="fa-regular fa-user mr-2 text-blue-400/70"></i>Create an Account
    </a>

    <p class="text-center text-white/20 text-xs mt-8 animate-in animate-in-d5">
        &copy; {{ date('Y') }}Easy IT Solution LTD. All Right Reserved.
    </p>

</div>

<script>
    function togglePassword() {
        var input = document.getElementById('password');
        var icon = document.getElementById('eye-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    document.getElementById('login-form').addEventListener('submit', function () {
        var btn = document.getElementById('signin-btn');
        btn.classList.add('loading');
    });
</script>

</body>
</html>
