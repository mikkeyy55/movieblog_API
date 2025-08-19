<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Movie Blog')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Movie Blog - Custom Color Scheme */
        :root {
            --primary-orange: #ff6b35;
            --primary-pink: #f7931e;
            --gradient-orange: #ff6b35;
            --gradient-pink: #f7931e;
            --dark-bg: #1a1a2e;
            --card-bg: #16213e;
            --text-light: #ffffff;
            --text-muted: #b8b8b8;
            --accent-orange: #ff8c42;
            --accent-pink: #ff6b9d;
        }

        /* Global Styles */
        body {
            background-color: var(--dark-bg);
            color: var(--text-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Titles: orange */
        h1, h2, h3, h4, h5, h6,
        .display-1, .display-2, .display-3, .display-4, .display-5, .display-6,
        .card-header h5, .section-title {
            color: var(--primary-orange) !important;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, var(--gradient-orange) 0%, var(--gradient-pink) 100%) !important;
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            color: var(--text-light) !important;
        }

        .navbar-nav .nav-link {
            color: var(--text-light) !important;
            font-weight: 500;
        }

        .navbar-nav .nav-link:hover {
            color: var(--text-light) !important;
            opacity: 0.8;
        }

        /* Logo */
        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-image {
            height: 50px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
            transition: transform 0.3s ease;
        }

        .logo-image:hover {
            transform: scale(1.05);
        }

        .hero-logo {
            height: 150px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.4));
            margin-bottom: 20px;
        }

        /* Responsive logo sizing */
        @media (max-width: 768px) {
            .logo-image {
                height: 40px;
            }

            .hero-logo {
                height: 120px;
            }
        }

        @media (max-width: 576px) {
            .logo-image {
                height: 35px;
            }

            .hero-logo {
                height: 100px;
            }
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--gradient-orange) 0%, var(--gradient-pink) 100%);
            color: var(--text-light);
            padding: 4rem 0;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="film" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="2" fill="rgba(255,255,255,0.1)"/><rect x="8" y="0" width="4" height="20" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23film)"/></svg>');
            opacity: 0.3;
        }



        /* Cards */
        .card {
            background: var(--card-bg);
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 53, 0.2);
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--gradient-orange) 0%, var(--gradient-pink) 100%);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
        }

        .btn-outline-light {
            border: 2px solid var(--text-light);
            color: var(--text-light);
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-light:hover {
            background: var(--text-light);
            color: var(--gradient-orange);
        }

        .btn-light {
            background: var(--text-light);
            color: var(--gradient-orange);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-light:hover {
            background: var(--text-light);
            color: var(--gradient-pink);
            transform: translateY(-2px);
        }

        /* Footer */
        footer {
            background: linear-gradient(135deg, var(--gradient-orange) 0%, var(--gradient-pink) 100%) !important;
            color: var(--text-light);
        }

        /* Dropdown */
        .dropdown-menu {
            background: var(--card-bg);
            border: 1px solid rgba(255, 107, 53, 0.2);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .dropdown-item {
            color: var(--text-light);
            border-radius: 10px;
            margin: 2px 8px;
            padding: 8px 15px;
        }

        .dropdown-item:hover {
            background: linear-gradient(135deg, var(--gradient-orange) 0%, var(--gradient-pink) 100%);
            color: var(--text-light);
        }

        /* Additional text color overrides */
        .text-white { color: var(--text-light) !important; }
        .text-dark { color: var(--text-light) !important; }

        /* High-contrast defaults on dark background */
        body, p, small, span, li, label, .form-text, .list-group-item, dt, dd {
            color: var(--text-light) !important;
        }

        /* Make muted/secondary utilities readable */
        .text-muted,
        .text-secondary,
        .text-body-secondary,
        .text-white-50,
        .text-black-50,
        .text-body,
        .card .text-muted,
        .card .text-body-secondary {
            color: rgba(255, 255, 255, 0.82) !important;
        }

        /* Link color on dark */
        a { color: #ff9f6b; }
        a:hover { color: #ffb98f; }

        /* Ensure all layout containers inherit readable text */
        .container, .row, .col, .col-md, .col-lg { color: var(--text-light) !important; }

        /* Text selection: white background with black text */
        ::selection { background: #ffffff; color: #000000; }
        ::-moz-selection { background: #ffffff; color: #000000; }
    </style>
    @yield('extra-css')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-transparent">
        <div class="container">
                        <a class="navbar-brand" href="{{ route('welcome') }}">
                <div class="logo-container">
                    <img src="{{ asset('storage/picture/logo.png') }}" alt="Movie Blog Logo" class="logo-image">
                </div>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                                            <li class="nav-item">
                            <a class="nav-link" href="{{ route('movies.index') }}">Movies</a>
                        </li>
                        @auth
                            @if(!Auth::user()->isAdmin())
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-list me-1"></i>My Lists
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('watchlist.show') }}">
                                            <i class="fas fa-bookmark me-2"></i>My Watchlist
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('favorites.show') }}">
                                            <i class="fas fa-heart me-2"></i>My Favorites
                                        </a></li>
                                    </ul>
                                </li>
                            @endif

                        @endauth
                </ul>

                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>

                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}

                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('profile') }}">
                                    <i class="fas fa-user-circle me-2"></i>Profile
                                </a></li>
                                @if(Auth::user()->isAdmin())
                                    <li><hr class="dropdown-divider"></li>
                                    <li><h6 class="dropdown-header">
                                        <i class="fas fa-shield-alt me-1"></i>Admin Tools
                                    </h6></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('movies.create') }}">
                                        <i class="fas fa-plus me-2"></i>Add New Movie
                                    </a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-0" role="alert">
            <div class="container">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-0" role="alert">
            <div class="container">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} Movie Blog. Built with Laravel & Bootstrap.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Star rating functionality
        document.addEventListener('DOMContentLoaded', function() {
            const ratingForms = document.querySelectorAll('.rating-form');

            ratingForms.forEach(form => {
                const stars = form.querySelectorAll('.btn-star');
                const ratingInput = form.querySelector('input[name="rating"]');

                stars.forEach((star, index) => {
                    star.addEventListener('click', function(e) {
                        e.preventDefault();
                        const rating = index + 1;
                        ratingInput.value = rating;

                        // Update visual state
                        stars.forEach((s, i) => {
                            if (i < rating) {
                                s.classList.add('active');
                            } else {
                                s.classList.remove('active');
                            }
                        });

                        // Submit form
                        form.submit();
                    });

                    star.addEventListener('mouseover', function() {
                        const rating = index + 1;
                        stars.forEach((s, i) => {
                            if (i < rating) {
                                s.style.color = '#ffc107';
                            } else {
                                s.style.color = '#ddd';
                            }
                        });
                    });
                });

                form.addEventListener('mouseleave', function() {
                    const currentRating = parseInt(ratingInput.value) || 0;
                    stars.forEach((s, i) => {
                        if (i < currentRating) {
                            s.style.color = '#ffc107';
                        } else {
                            s.style.color = '#ddd';
                        }
                    });
                });
            });
        });

        // Toast notification functionality
        @auth
        function showToast(message, type = 'info') {
            const toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                const container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'position-fixed top-0 end-0 p-3';
                container.style.zIndex = '1055';
                document.body.appendChild(container);
            }

            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} border-0`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');

            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;

            document.getElementById('toast-container').appendChild(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();

            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        }
        @endauth

        function showListModal(movies, type) {
            const title = type === 'watchlist' ? 'My Watchlist' : 'My Favorites';
            const icon = type === 'watchlist' ? 'bookmark' : 'heart';

            let content = movies.length === 0
                ? `<p class="text-center text-muted">No movies in your ${type} yet.</p>`
                : movies.map(item => `
                    <div class="d-flex align-items-center mb-2 p-2 border rounded">
                        <i class="fas fa-${icon} me-2"></i>
                        <div class="flex-grow-1">
                            <strong>${item.movie.title}</strong>
                            <br><small class="text-muted">${item.movie.genre}</small>
                        </div>
                        <a href="/movies/${item.movie.id}" class="btn btn-sm btn-primary">View</a>
                    </div>
                `).join('');

            // Create and show modal (you might want to use a proper modal library)
            alert(title + ':\n\n' + movies.map(item => item.movie.title).join('\n') || 'No movies found');
        }

        function showToast(message, type) {
            // Simple toast notification (you might want to use a proper toast library)
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed top-0 end-0 m-3`;
            toast.style.zIndex = '9999';
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
    });
    </script>

    @yield('extra-js')
</body>
</html>
