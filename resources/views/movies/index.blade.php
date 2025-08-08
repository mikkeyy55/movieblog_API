@extends('layouts.app')

@section('title', 'Movies - Movie Database')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container text-center">
        <h1 class="display-4 fw-bold">
            <i class="fas fa-film me-3"></i>Discover Amazing Movies
        </h1>
        <p class="lead">Explore our curated collection of movies, rate them, and share your thoughts!</p>

        <!-- Role-based welcome message -->
        @auth
            <div class="alert alert-info d-inline-block mt-3">
                @if(Auth::user()->isAdmin())
                    <i class="fas fa-crown me-2"></i>
                    <strong>Welcome Admin!</strong> You can create, edit, and delete movies, plus manage all user comments.
                @else
                    <i class="fas fa-star me-2"></i>
                    <strong>Welcome {{ Auth::user()->name }}!</strong> You can rate movies and leave comments.
                @endif
            </div>
            <br>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('movies.create') }}" class="btn btn-light btn-lg mt-3">
                    <i class="fas fa-plus me-2"></i>Add New Movie
                </a>
            @endif
        @else
            <div class="alert alert-warning d-inline-block mt-3">
                <i class="fas fa-info-circle me-2"></i>
                <a href="{{ route('login') }}" class="alert-link">Login</a> to rate movies and leave comments, or
                <a href="{{ route('admin.login') }}" class="alert-link">login as admin</a> to manage movies.
            </div>
        @endauth
    </div>
</section>

<div class="container">
    <!-- Search and Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('movies.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="fas fa-search me-1"></i>Search Movies
                            </label>
                            <input type="text" name="search" class="form-control"
                                   placeholder="Search by title..."
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">
                                <i class="fas fa-tag me-1"></i>Genre
                            </label>
                            <select name="genre" class="form-select">
                                <option value="">All Genres</option>
                                <option value="Action" {{ request('genre') == 'Action' ? 'selected' : '' }}>Action</option>
                                <option value="Comedy" {{ request('genre') == 'Comedy' ? 'selected' : '' }}>Comedy</option>
                                <option value="Drama" {{ request('genre') == 'Drama' ? 'selected' : '' }}>Drama</option>
                                <option value="Horror" {{ request('genre') == 'Horror' ? 'selected' : '' }}>Horror</option>
                                <option value="Sci-Fi" {{ request('genre') == 'Sci-Fi' ? 'selected' : '' }}>Sci-Fi</option>
                                <option value="Romance" {{ request('genre') == 'Romance' ? 'selected' : '' }}>Romance</option>
                                <option value="Thriller" {{ request('genre') == 'Thriller' ? 'selected' : '' }}>Thriller</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">
                                <i class="fas fa-calendar me-1"></i>Year
                            </label>
                            <select name="year" class="form-select">
                                <option value="">All Years</option>
                                @for($year = date('Y'); $year >= 1950; $year--)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">
                                <i class="fas fa-sort me-1"></i>Sort By
                            </label>
                            <select name="sort_by" class="form-select">
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Newest</option>
                                <option value="rating" {{ request('sort_by') == 'rating' ? 'selected' : '' }}>Rating</option>
                                <option value="title" {{ request('sort_by') == 'title' ? 'selected' : '' }}>Title</option>
                                <option value="year" {{ request('sort_by') == 'year' ? 'selected' : '' }}>Year</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="{{ route('movies.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Filter Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('movies.index') }}?sort_by=rating"
                   class="btn btn-outline-warning btn-sm {{ request('sort_by') == 'rating' ? 'active' : '' }}">
                    <i class="fas fa-star me-1"></i>Top Rated
                </a>
                <a href="{{ route('movies.index') }}?sort_by=created_at"
                   class="btn btn-outline-info btn-sm {{ request('sort_by') == 'created_at' ? 'active' : '' }}">
                    <i class="fas fa-clock me-1"></i>Latest
                </a>
                <a href="{{ route('movies.index') }}?genre=Action"
                   class="btn btn-outline-danger btn-sm {{ request('genre') == 'Action' ? 'active' : '' }}">
                    <i class="fas fa-fist-raised me-1"></i>Action
                </a>
                <a href="{{ route('movies.index') }}?genre=Comedy"
                   class="btn btn-outline-success btn-sm {{ request('genre') == 'Comedy' ? 'active' : '' }}">
                    <i class="fas fa-laugh me-1"></i>Comedy
                </a>
                <a href="{{ route('movies.index') }}?genre=Horror"
                   class="btn btn-outline-dark btn-sm {{ request('genre') == 'Horror' ? 'active' : '' }}">
                    <i class="fas fa-ghost me-1"></i>Horror
                </a>
            </div>
        </div>
    </div>

    @if(empty($movies))
        <div class="text-center py-5">
            <i class="fas fa-film fa-5x text-muted mb-4"></i>
            <h3 class="text-muted">No Movies Found</h3>
            <p class="text-muted">Try adjusting your search criteria or add a new movie!</p>
            @auth
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('movies.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add First Movie
                    </a>
                @endif
            @endauth
        </div>
    @else
        <div class="row">
            @foreach($movies as $movie)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card movie-card h-100">
                        <div class="position-relative">
                            @if($movie->cover_image)
                                <img src="{{ $movie->cover_image }}" class="card-img-top movie-poster" alt="{{ $movie->title }}">
                            @else
                                <div class="movie-poster d-flex align-items-center justify-content-center">
                                    <i class="fas fa-film fa-4x text-white opacity-50"></i>
                                </div>
                            @endif

                            <!-- Rating Badge -->
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-dark bg-opacity-75 fs-6">
                                    <i class="fas fa-star text-warning me-1"></i>
                                    {{ number_format($movie->ratings_avg_rating ?? 0, 1) }}
                                    <small>({{ $movie->ratings_count ?? 0 }})</small>
                                </span>
                            </div>

                            <!-- Watchlist/Favorite Buttons -->
                            @auth
                                @if(!Auth::user()->isAdmin())
                                    <div class="position-absolute top-0 start-0 m-2">
                                        <div class="btn-group-vertical">
                                            <form action="{{ route('watchlist.toggle') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="movie_id" value="{{ $movie->id }}">
                                                <button type="submit" class="btn btn-sm btn-light"
                                                        title="Add to Watchlist">
                                                    <i class="fas fa-bookmark"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('favorites.toggle') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="movie_id" value="{{ $movie->id }}">
                                                <button type="submit" class="btn btn-sm btn-light"
                                                        title="Add to Favorites">
                                                    <i class="fas fa-heart"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @endauth
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold">{{ $movie->title }}</h5>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">
                                    <i class="fas fa-tag me-1"></i>{{ $movie->genre }}
                                </span>
                                @if(isset($movie->release_year))
                                    <span class="badge bg-secondary">{{ $movie->release_year }}</span>
                                @endif
                            </div>
                            <p class="card-text text-muted small flex-grow-1">
                                {{ Str::limit($movie->description, 100) }}
                            </p>

                            <div class="mt-auto pt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        By {{ $movie->creator->name ?? 'Unknown' }}
                                    </small>
                                    <a href="{{ route('movies.show', $movie->id) }}" class="btn btn-primary btn-sm">
                                        View Details <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
