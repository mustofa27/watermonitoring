<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Water Monitoring System')</title>
    
    <!-- Font Awesome icons -->
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    
    <!-- Google fonts-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700" rel="stylesheet" type="text/css" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: "Roboto Slab", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar {
            background-color: #212529;
            padding: 1rem 0;
        }
        
        .navbar-brand {
            font-size: 1.5em;
            font-family: "Montserrat", sans-serif;
            font-weight: 700;
            color: #ffc800;
        }
        
        .navbar-brand:hover {
            color: #e6b400;
        }
        
        .page-header {
            background: linear-gradient(135deg, #212529 0%, #343a40 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        
        .page-header h1 {
            font-family: "Montserrat", sans-serif;
            font-weight: 700;
            font-size: 2.5rem;
        }
        
        .btn-primary {
            background-color: #ffc800;
            border-color: #ffc800;
            color: #000;
            font-weight: 700;
            font-family: "Montserrat", sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: #e6b400;
            border-color: #e6b400;
            color: #000;
        }
        
        .btn-secondary {
            font-family: "Montserrat", sans-serif;
            font-weight: 600;
        }
        
        .card {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 0.5rem;
        }
        
        .form-label {
            font-weight: 600;
            font-family: "Montserrat", sans-serif;
            color: #495057;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 0.5px;
        }
        
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 0.375rem;
            padding: 0.625rem 0.875rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #ffc800;
            box-shadow: 0 0 0 0.2rem rgba(255, 200, 0, 0.25);
        }
        
        .alert {
            border-radius: 0.5rem;
            border: none;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background-color: #212529;
            color: #ffc800;
            font-family: "Montserrat", sans-serif;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.875rem;
            border: none;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/"><i class="fas fa-tint"></i> Water Monitor</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('dashboard') }}"><i class="fas fa-chart-pie"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('tandons.index') }}"><i class="fas fa-water"></i> Water Tanks</a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link text-white"><i class="fas fa-sign-out-alt"></i> Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')

    <!-- Bootstrap core JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
</body>
</html>
