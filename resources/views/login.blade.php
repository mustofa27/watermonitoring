<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Water Monitoring System</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Source+Serif+4:wght@500;700&display=swap" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="wm-login-page d-flex align-items-center justify-content-center" style="background: linear-gradient(155deg, #112a24 0%, #123f36 55%, #f5b700 160%); padding: 1rem;">
    <div class="wm-bg-orb wm-bg-orb--one"></div>
    <div class="wm-bg-orb wm-bg-orb--two"></div>

    <div class="wm-card" style="width: 100%; max-width: 430px; border: 0; overflow: hidden;">
        <div class="p-4 text-center" style="background: linear-gradient(145deg, #f5b700, #f2cc4c);">
            <i class="fas fa-droplet" style="font-size: 2.2rem; color: #0f2a24;"></i>
            <h1 class="mt-2 mb-1" style="font-family: 'Source Serif 4', serif; font-size: 1.7rem; color: #0f2a24;">Water Monitoring System</h1>
            <p class="mb-0" style="color: #243f37;">Politeknik Negeri Madura</p>
        </div>

        <div class="wm-card__body p-4">
            @if(session('error'))
                <div class="alert alert-danger mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required autofocus>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn wm-btn wm-btn--primary" style="padding: 0.7rem 1rem;">
                        <i class="fas fa-sign-in-alt me-2"></i>Login to Dashboard
                    </button>
                    <a href="/" class="btn wm-btn wm-btn--secondary">Back to Home</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
