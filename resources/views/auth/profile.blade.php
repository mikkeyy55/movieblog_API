@extends('layouts.app')

@section('title', 'Profile - Movie Database')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <!-- Profile Header -->
            <div class="card shadow mb-4">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center"
                             style="width: 100px; height: 100px;">
                            <i class="fas fa-user fa-3x text-white"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold">{{ Auth::user()->name }}</h2>
                    <p class="text-muted">{{ Auth::user()->email }}</p>
                    @if(Auth::user()->isAdmin())
                        <span class="badge bg-danger fs-6">
                            <i class="fas fa-crown me-1"></i>Administrator
                        </span>

                    @endif
                    <div class="mt-3">
                        <small class="text-muted">
                            Member since {{ Auth::user()->created_at->format('M Y') }}
                        </small>
                    </div>
                </div>
            </div>

            <!-- User Statistics -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-star fa-2x text-warning mb-2"></i>
                            <h4 class="fw-bold">{{ Auth::user()->ratings()->count() }}</h4>
                            <p class="text-muted mb-0">Ratings Given</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-comments fa-2x text-primary mb-2"></i>
                            <h4 class="fw-bold">{{ Auth::user()->comments()->count() }}</h4>
                            <p class="text-muted mb-0">Comments Made</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            @if(Auth::user()->isAdmin())
                                <i class="fas fa-film fa-2x text-success mb-2"></i>
                                <h4 class="fw-bold">{{ Auth::user()->movies()->count() }}</h4>
                                <p class="text-muted mb-0">Movies Added</p>
                            @else
                                <i class="fas fa-calendar fa-2x text-info mb-2"></i>
                                <h4 class="fw-bold">{{ Auth::user()->created_at->diffInDays() }}</h4>
                                <p class="text-muted mb-0">Days Active</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Recent Activity
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $recentComments = Auth::user()->comments()->with('movie')->latest()->take(5)->get();
                        $recentRatings = Auth::user()->ratings()->with('movie')->latest()->take(5)->get();
                        $activities = collect();

                        foreach($recentComments as $comment) {
                            $activities->push([
                                'type' => 'comment',
                                'icon' => 'fas fa-comment',
                                'color' => 'text-primary',
                                'action' => 'commented on',
                                'movie' => $comment->movie,
                                'date' => $comment->created_at
                            ]);
                        }

                        foreach($recentRatings as $rating) {
                            $activities->push([
                                'type' => 'rating',
                                'icon' => 'fas fa-star',
                                'color' => 'text-warning',
                                'action' => 'rated',
                                'movie' => $rating->movie,
                                'rating' => $rating->rating,
                                'date' => $rating->created_at
                            ]);
                        }

                        $activities = $activities->sortByDesc('date')->take(10);
                    @endphp

                    @if($activities->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Activity Yet</h5>
                            <p class="text-muted">Start rating movies and leaving comments to see your activity here!</p>
                            <a href="{{ route('movies.index') }}" class="btn btn-primary">
                                Browse Movies
                            </a>
                        </div>
                    @else
                        <div class="timeline">
                            @foreach($activities as $activity)
                                <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                                    <div class="me-3">
                                        <i class="{{ $activity['icon'] }} {{ $activity['color'] }} fa-lg"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-1">
                                            You {{ $activity['action'] }}
                                            <a href="{{ route('movies.show', $activity['movie']->id) }}" class="text-decoration-none">
                                                <strong>{{ $activity['movie']->title }}</strong>
                                            </a>
                                            @if($activity['type'] === 'rating')
                                                <span class="ms-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $activity['rating'] ? 'text-warning' : 'text-muted' }}"></i>
                                                    @endfor
                                                </span>
                                            @endif
                                        </p>
                                        <small class="text-muted">
                                            {{ $activity['date']->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
