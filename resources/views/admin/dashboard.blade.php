@extends('layouts.app')

@section('title', 'Admin Dashboard - Movie Database')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
                </h1>
                <div>
                    <a href="{{ route('movies.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Movie
                    </a>
                    <a href="{{ route('admin.movies') }}" class="btn btn-outline-primary ms-2">
                        <i class="fas fa-film me-2"></i>Manage Movies
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Movies</h6>
                            <h2 class="mb-0">{{ $stats['total_movies'] }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-film fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Users</h6>
                            <h2 class="mb-0">{{ $stats['total_users'] }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Comments</h6>
                            <h2 class="mb-0">{{ $stats['total_comments'] }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-comments fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Ratings</h6>
                            <h2 class="mb-0">{{ $stats['total_ratings'] }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-star fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Rows -->
    <div class="row">
        <!-- Recent Movies -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Recent Movies
                    </h5>
                </div>
                <div class="card-body">
                    @if($stats['recent_movies']->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($stats['recent_movies'] as $movie)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $movie->title }}</h6>
                                        <small class="text-muted">{{ $movie->genre }} • {{ $movie->release_year }}</small>
                                    </div>
                                    <div>
                                        <a href="{{ route('movies.show', $movie->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('movies.edit', $movie->id) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No movies added yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>Recent Users
                    </h5>
                </div>
                <div class="card-body">
                    @if($stats['recent_users']->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($stats['recent_users'] as $user)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $user->name }}</h6>
                                        <small class="text-muted">{{ $user->email }}</small>
                                        @if($user->isAdmin())
                                            <span class="badge bg-danger ms-2">Admin</span>
                                        @else
                                            <span class="badge bg-primary ms-2">User</span>
                                        @endif
                                    </div>
                                    <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No users registered yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Top Rated Movies -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-star me-2"></i>Top Rated Movies
                    </h5>
                </div>
                <div class="card-body">
                    @if($stats['top_rated_movies']->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($stats['top_rated_movies'] as $movie)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $movie->title }}</h6>
                                        <div class="d-flex align-items-center">
                                            <div class="rating-stars me-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $movie->ratings_avg_rating ? 'text-warning' : 'text-muted' }}"></i>
                                                @endfor
                                            </div>
                                            <small class="text-muted">{{ number_format($movie->ratings_avg_rating, 1) }} ({{ $movie->ratings_count }} ratings)</small>
                                        </div>
                                    </div>
                                    <a href="{{ route('movies.show', $movie->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No rated movies yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Most Commented Movies -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-comments me-2"></i>Most Commented Movies
                    </h5>
                </div>
                <div class="card-body">
                    @if($stats['most_commented_movies']->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($stats['most_commented_movies'] as $movie)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $movie->title }}</h6>
                                        <small class="text-muted">{{ $movie->genre }} • {{ $movie->comments_count }} comments</small>
                                    </div>
                                    <a href="{{ route('movies.show', $movie->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No commented movies yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
