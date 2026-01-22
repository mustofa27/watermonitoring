<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Water Monitoring System - Politeknik Negeri Madura</title>
    
    <!-- Font Awesome icons -->
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    
    <!-- Google fonts-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700" rel="stylesheet" type="text/css" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --bs-primary: #0d6efd;
            --bs-primary-rgb: 13, 110, 253;
        }
        
        body {
            font-family: "Roboto Slab", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }
        
        .navbar {
            padding-top: 1rem;
            padding-bottom: 1rem;
            background-color: #212529;
        }
        
        .navbar-brand {
            font-size: 1.75em;
            font-family: "Montserrat", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-weight: 700;
            color: #ffc800;
        }
        
        .navbar-nav .nav-link {
            font-size: 0.95rem;
            color: #fff;
            font-family: "Montserrat", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-weight: 400;
            letter-spacing: 0.0625em;
        }
        
        .navbar-nav .nav-link:hover {
            color: #ffc800;
        }
        
        .masthead {
            padding-top: 10.5rem;
            padding-bottom: 6rem;
            text-align: center;
            color: #fff;
            background-image: url('https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=1920');
            background-repeat: no-repeat;
            background-attachment: scroll;
            background-position: center center;
            background-size: cover;
        }
        
        .masthead .masthead-subheading {
            font-size: 1.5rem;
            font-style: italic;
            line-height: 1.5rem;
            margin-bottom: 25px;
            font-family: "Roboto Slab", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        
        .masthead .masthead-heading {
            font-size: 3.25rem;
            font-weight: 700;
            line-height: 3.25rem;
            margin-bottom: 2rem;
            font-family: "Montserrat", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        
        .page-section {
            padding: 6rem 0;
        }
        
        .section-heading {
            font-size: 2.5rem;
            margin-top: 0;
            margin-bottom: 1rem;
            font-family: "Montserrat", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-weight: 700;
        }
        
        .section-subheading {
            font-size: 1rem;
            font-weight: 400;
            font-style: italic;
            margin-bottom: 4rem;
            font-family: "Roboto Slab", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        
        .btn-xl {
            padding: 1.25rem 2.5rem;
            font-size: 1.125rem;
            font-weight: 700;
        }
        
        .fa-stack {
            margin-bottom: 1rem;
        }
        
        .text-primary {
            color: #ffc800 !important;
        }
        
        .btn-primary {
            background-color: #ffc800;
            border-color: #ffc800;
            color: #000;
        }
        
        .btn-primary:hover {
            background-color: #e6b400;
            border-color: #e6b400;
        }
        
        .timeline {
            position: relative;
            padding: 0;
            list-style: none;
        }
        
        .timeline:before {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 40px;
            width: 2px;
            margin-left: -1.5px;
            content: "";
            background-color: #e9ecef;
        }
        
        .timeline > li {
            position: relative;
            min-height: 50px;
            margin-bottom: 50px;
        }
        
        .timeline > li:after, .timeline > li:before {
            display: table;
            content: " ";
        }
        
        .timeline > li:after {
            clear: both;
        }
        
        .timeline > li .timeline-panel {
            position: relative;
            float: right;
            width: 100%;
            padding: 0 20px 0 100px;
            text-align: left;
        }
        
        .timeline > li .timeline-image {
            position: absolute;
            z-index: 100;
            left: 0;
            width: 80px;
            height: 80px;
            margin-left: 0;
            text-align: center;
            color: white;
            border: 7px solid #e9ecef;
            border-radius: 100%;
            background-color: #ffc800;
        }
        
        .timeline > li .timeline-image h4 {
            font-size: 10px;
            line-height: 14px;
            margin-top: 12px;
        }
        
        .timeline > li.timeline-inverted > .timeline-panel {
            float: right;
            padding: 0 20px 0 100px;
            text-align: left;
        }
        
        .team-member {
            margin-bottom: 3rem;
            text-align: center;
        }
        
        .team-member img {
            width: 14rem;
            height: 14rem;
            border: 0.5rem solid rgba(0, 0, 0, 0.1);
        }
        
        .team-member h4 {
            margin-top: 1.5rem;
            margin-bottom: 0;
        }
        
        .footer {
            padding: 1.5rem 0;
            text-align: center;
        }
        
        .btn-social {
            height: 2.5rem;
            width: 2.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            border-radius: 100%;
        }
        
        @media (min-width: 768px) {
            .masthead {
                padding-top: 17rem;
                padding-bottom: 12.5rem;
            }
            
            .masthead .masthead-subheading {
                font-size: 2.25rem;
                font-style: italic;
                line-height: 2.25rem;
                margin-bottom: 2rem;
            }
            
            .masthead .masthead-heading {
                font-size: 4.5rem;
                font-weight: 700;
                line-height: 4.5rem;
                margin-bottom: 4rem;
            }
        }
        
        @media (min-width: 992px) {
            .timeline > li {
                min-height: 150px;
            }
            
            .timeline > li .timeline-panel {
                float: left;
                width: 41%;
                padding: 0 20px 20px 30px;
                text-align: right;
            }
            
            .timeline > li .timeline-image {
                left: 50%;
                width: 150px;
                height: 150px;
                margin-left: -75px;
            }
            
            .timeline > li .timeline-image h4 {
                font-size: 13px;
                line-height: 18px;
                margin-top: 30px;
            }
            
            .timeline > li.timeline-inverted > .timeline-panel {
                float: right;
                padding: 0 30px 20px 20px;
                text-align: left;
            }
        }
    </style>
</head>
<body id="page-top">

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="#page-top"><i class="fas fa-tint"></i> Water Monitor</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive">
                Menu
                <i class="fas fa-bars ms-1"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav text-uppercase ms-auto py-4 py-lg-0">
                    <li class="nav-item"><a class="nav-link" href="#services">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#portfolio">Monitoring</a></li>
                    @if(session('user'))
                        <li class="nav-item"><a class="nav-link" href="{{ route('tandons.index') }}">Dashboard</a></li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link text-uppercase" style="border: none; background: none;">Logout</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="position: fixed; top: 80px; right: 20px; z-index: 9999;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Masthead -->
    <header class="masthead">
        <div class="container">
            <div class="masthead-subheading">Welcome To Smart Water Management</div>
            <div class="masthead-heading text-uppercase">Automated Water Control System</div>
            @if(!session('user'))
                <a class="btn btn-primary btn-xl text-uppercase" href="{{ route('login') }}">Get Started</a>
            @else
                <a class="btn btn-primary btn-xl text-uppercase" href="{{ route('tandons.index') }}">Go to Dashboard</a>
            @endif
        </div>
    </header>

    <!-- Services -->
    <section class="page-section" id="services">
        <div class="container">
            <div class="text-center">
                <h2 class="section-heading text-uppercase">Automation Features</h2>
                <h3 class="section-subheading text-muted">Intelligent automation for efficient water management</h3>
            </div>
            <div class="row text-center">
                <div class="col-md-4">
                    <span class="fa-stack fa-4x">
                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                        <i class="fas fa-robot fa-stack-1x fa-inverse"></i>
                    </span>
                    <h4 class="my-3">Automated Monitoring</h4>
                    <p class="text-muted">AI-driven continuous water level tracking with predictive analytics for proactive management.</p>
                </div>
                <div class="col-md-4">
                    <span class="fa-stack fa-4x">
                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                        <i class="fas fa-cog fa-stack-1x fa-inverse"></i>
                    </span>
                    <h4 class="my-3">Smart Pump Control</h4>
                    <p class="text-muted">Automatic pump activation and deactivation based on real-time demand and water levels.</p>
                </div>
                <div class="col-md-4">
                    <span class="fa-stack fa-4x">
                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                        <i class="fas fa-bell fa-stack-1x fa-inverse"></i>
                    </span>
                    <h4 class="my-3">Intelligent Alerts</h4>
                    <p class="text-muted">Automated notifications with escalation protocols for critical water level situations.</p>
                </div>
            </div>
            <div class="row text-center mt-4">
                <div class="col-md-4">
                    <span class="fa-stack fa-4x">
                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                        <i class="fas fa-chart-line fa-stack-1x fa-inverse"></i>
                    </span>
                    <h4 class="my-3">Predictive Analytics</h4>
                    <p class="text-muted">Machine learning algorithms forecasting water usage and optimizing distribution patterns.</p>
                </div>
                <div class="col-md-4">
                    <span class="fa-stack fa-4x">
                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                        <i class="fas fa-building fa-stack-1x fa-inverse"></i>
                    </span>
                    <h4 class="my-3">Multi-Building Control</h4>
                    <p class="text-muted">Hierarchical automation managing water flow across multiple campus buildings seamlessly.</p>
                </div>
                <div class="col-md-4">
                    <span class="fa-stack fa-4x">
                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                        <i class="fas fa-shield-alt fa-stack-1x fa-inverse"></i>
                    </span>
                    <h4 class="my-3">Secure Automation</h4>
                    <p class="text-muted">Encrypted control protocols with comprehensive audit trails for all automated actions.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Portfolio Grid / Monitoring Points -->
    <section class="page-section bg-light" id="portfolio">
        <div class="container">
            <div class="text-center">
                <h2 class="section-heading text-uppercase">Campus Water Monitoring</h2>
                <h3 class="section-subheading text-muted">Real-time monitoring across Politeknik Negeri Madura facilities</h3>
            </div>
            <div class="row">
                <div class="col-lg-4 col-sm-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-water fa-4x text-primary mb-3"></i>
                            <h5 class="card-title">Main Water Tank</h5>
                            <p class="text-muted">Central distribution system</p>
                            <div class="mt-3">
                                <span class="badge bg-success">Active</span>
                                <span class="badge bg-info">85% Capacity</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-university fa-4x text-primary mb-3"></i>
                            <h5 class="card-title">Engineering Building</h5>
                            <p class="text-muted">Faculty water distribution</p>
                            <div class="mt-3">
                                <span class="badge bg-success">Active</span>
                                <span class="badge bg-info">72% Capacity</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-home fa-4x text-primary mb-3"></i>
                            <h5 class="card-title">Student Dormitory</h5>
                            <p class="text-muted">Residential water supply</p>
                            <div class="mt-3">
                                <span class="badge bg-success">Active</span>
                                <span class="badge bg-info">68% Capacity</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-microscope fa-4x text-primary mb-3"></i>
                            <h5 class="card-title">Laboratory Complex</h5>
                            <p class="text-muted">Research facility supply</p>
                            <div class="mt-3">
                                <span class="badge bg-success">Active</span>
                                <span class="badge bg-info">91% Capacity</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-utensils fa-4x text-primary mb-3"></i>
                            <h5 class="card-title">Cafeteria</h5>
                            <p class="text-muted">Food service water system</p>
                            <div class="mt-3">
                                <span class="badge bg-success">Active</span>
                                <span class="badge bg-warning">55% Capacity</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-book fa-4x text-primary mb-3"></i>
                            <h5 class="card-title">Library Building</h5>
                            <p class="text-muted">Academic facility supply</p>
                            <div class="mt-3">
                                <span class="badge bg-success">Active</span>
                                <span class="badge bg-info">78% Capacity</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-4 text-lg-start">Copyright &copy; Water Monitoring System 2026</div>
                <div class="col-lg-4 my-3 my-lg-0">
                    <a class="btn btn-dark btn-social mx-2" href="#!" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-dark btn-social mx-2" href="#!" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-dark btn-social mx-2" href="#!" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a class="link-dark text-decoration-none me-3" href="#!">Privacy Policy</a>
                    <a class="link-dark text-decoration-none" href="#!">Terms of Use</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap core JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Navbar shrink function
        var navbarShrink = function () {
            const navbarCollapsible = document.body.querySelector('#mainNav');
            if (!navbarCollapsible) {
                return;
            }
            if (window.scrollY === 0) {
                navbarCollapsible.classList.remove('navbar-shrink')
            } else {
                navbarCollapsible.classList.add('navbar-shrink')
            }
        };

        // Shrink the navbar 
        navbarShrink();

        // Shrink the navbar when page is scrolled
        document.addEventListener('DOMContentLoaded', navbarShrink);
        window.addEventListener('scroll', navbarShrink);

        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
