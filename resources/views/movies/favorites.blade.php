@extends('layouts.app')

@section('title', 'My Favorites - Movie Database')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="display-6 fw-bold">
            <i class="fas fa-heart me-2"></i>My Favorites
        </h1>
        <a href="{{ route('movies.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Back to Movies
        </a>
    </div>

    @if($favorites->count() > 0)
        <div class="row">
            @foreach($favorites as $item)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card movie-card h-100">
                        <div class="position-relative">
                            @if($item->movie->cover_image)
                                <img src="{{ $item->movie->cover_image }}" class="card-img-top movie-poster" alt="{{ $item->movie->title }}">
                            @else
                                <div class="movie-poster d-flex align-items-center justify-content-center">
                                    <i class="fas fa-film fa-4x text-white opacity-50"></i>
                                </div>
                            @endif

                            <!-- Rating Badge -->
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-dark bg-opacity-75 fs-6">
                                    <i class="fas fa-star text-warning me-1"></i>
                                    {{ number_format($item->movie->ratings_avg_rating ?? 0, 1) }}
                                    <small>({{ $item->movie->ratings_count ?? 0 }})</small>
                                </span>
                            </div>

                            <!-- Remove from Favorites Button -->
                            <div class="position-absolute top-0 start-0 m-2">
                                <form action="{{ route('favorites.toggle') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="movie_id" value="{{ $item->movie->id }}">
                                    <button type="submit" class="btn btn-sm btn-danger"
                                            title="Remove from Favorites"
                                            onclick="return confirm('Remove from favorites?')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold">{{ $item->movie->title }}</h5>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">
                                    <i class="fas fa-tag me-1"></i>{{ $item->movie->genre }}
                                </span>
                                @if(isset($item->movie->release_year))
                                    <span class="badge bg-secondary">{{ $item->movie->release_year }}</span>
                                @endif
                            </div>
                            <p class="card-text text-muted small flex-grow-1">
                                {{ Str::limit($item->movie->description, 100) }}
                            </p>

                            <div class="mt-auto pt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        By {{ $item->movie->creator->name ?? 'Unknown' }}
                                    </small>
                                    <a href="{{ route('movies.show', $item->movie->id) }}" class="btn btn-primary btn-sm">
                                        View Details <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-heart fa-5x text-muted mb-4"></i>
            <h3 class="text-muted">Your Favorites is Empty</h3>
            <p class="text-muted">Start adding movies to your favorites to see them here!</p>
            <a href="{{ route('movies.index') }}" class="btn btn-primary">
                <i class="fas fa-film me-2"></i>Browse Movies
            </a>
        </div>
    @endif
</div>
@endsection
