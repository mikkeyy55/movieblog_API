@extends('layouts.app')

@section('title', 'Welcome to Movie Database')

@section('content')
<div class="hero-section">
    <div class="container text-center">
        <h1 class="display-2 fw-bold mb-4">
            <i class="fas fa-film me-3"></i>Movie Blogs
        </h1>
        <p class="lead fs-4 mb-4">Discover, rate, and discuss your favorite movies with fellow film enthusiasts</p>

        @guest
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-user-plus me-2"></i>Get Started
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </a>
            </div>
                    @else
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('movies.index') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-play me-2"></i>Browse Movies
                </a>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('movies.create') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-plus me-2"></i>Add Movie
                    </a>
                @endif
                                </div>
        @endguest
                            </div>
                                </div>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <i class="fas fa-star fa-3x text-warning mb-3"></i>
                    <h4 class="fw-bold">Rate Movies</h4>
                    <p class="text-muted">Share your opinions by rating movies from 1 to 5 stars and help others discover great films.</p>
                            </div>
                        </div>
                    </div>

        <div class="col-lg-4 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <i class="fas fa-comments fa-3x text-primary mb-3"></i>
                    <h4 class="fw-bold">Join Discussions</h4>
                    <p class="text-muted">Engage with the community by leaving thoughtful comments and reviews on your favorite movies.</p>
                </div>
            </div>
                    </div>

        <div class="col-lg-4 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <i class="fas fa-search fa-3x text-success mb-3"></i>
                    <h4 class="fw-bold">Discover Films</h4>
                    <p class="text-muted">Explore our curated collection of movies across various genres and find your next favorite film.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <h3 class="mb-4">Ready to Start Your Movie Journey?</h3>
        @guest
            <p class="text-muted mb-4">Join our community of movie lovers today!</p>
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-rocket me-2"></i>Sign Up Now
            </a>
        @else
            <p class="text-muted mb-4">Welcome back, {{ Auth::user()->name }}! What would you like to watch next?</p>
            <a href="{{ route('movies.index') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-film me-2"></i>Explore Movies
            </a>
        @endguest
    </div>
</div>
@endsection
