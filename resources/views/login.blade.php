<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
    <title>Log in - Water Monitoring System</title>
</head>
<body>
    <div class="wm-auth">
        <section class="wm-auth__brand" aria-hidden="true">
            <a href="/" class="wm-sidebar__brand">
                <span class="wm-sidebar__brand-icon"><i class="fas fa-droplet"></i></span>
                Water Monitor
            </a>
            <div>
                <h2>Know your water, in real time.</h2>
                <p>Tank levels, pump control and threshold alerts for Politeknik Negeri Madura, in one place.</p>
            </div>
            <p class="small">Politeknik Negeri Madura</p>
        </section>

        <section class="wm-auth__panel">
            <div class="wm-auth__card">
                <h1>Welcome back</h1>
                <p class="text-muted mb-4">Log in to open the monitoring dashboard.</p>

                @if(session('error'))
                    <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="d-grid gap-3">
                    @csrf
                    <div>
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="you@example.com" autocomplete="username" required autofocus>
                    </div>
                    <div>
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" autocomplete="current-password" required>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2" style="min-height: 2.75rem;">Log in</button>
                    <a href="/" class="btn btn-link text-decoration-none">Back to home</a>
                </form>
            </div>
        </section>
    </div>
</body>
</html>
