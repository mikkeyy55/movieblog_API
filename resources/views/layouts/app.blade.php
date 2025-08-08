<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Movie Database')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .movie-card {
            transition: transform 0.2s;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .movie-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .movie-poster {
            height: 300px;
            object-fit: cover;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .rating-stars {
            color: #ffc107;
        }
        .rating-form .btn-star {
            background: none;
            border: none;
            color: #ddd;
            font-size: 1.5rem;
            padding: 2px;
        }
        .rating-form .btn-star:hover,
        .rating-form .btn-star.active {
            color: #ffc107;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
            margin-bottom: 2rem;
        }
        .comment-card {
            border-left: 4px solid #007bff;
            margin-bottom: 1rem;
        }
    </style>
    @yield('extra-css')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" >
                <i class="fas fa-film me-2"></i>Movie Blogs
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
                                @if(Auth::user()->isAdmin())
                                    <span class="badge bg-danger ms-1">
                                        <i class="fas fa-crown me-1"></i>Admin
                                    </span>
                                @else
                                    <span class="badge bg-primary ms-1">
                                        <i class="fas fa-user me-1"></i>User
                                    </span>
                                @endif
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
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} Movie Blogs. Built with Laravel & Bootstrap.</p>
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
