@extends('layouts.app')

@section('title', $movie->title . ' - Movie Database')

@section('content')
<div class="container py-4">
    <!-- Movie Details -->
    <div class="row">
        <div class="col-lg-4 mb-4">
            @if($movie->cover_image)
                <img src="{{ $movie->cover_image }}" class="img-fluid rounded shadow" alt="{{ $movie->title }}">
            @else
                <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="height: 400px;">
                    <i class="fas fa-film fa-5x text-white opacity-50"></i>
                </div>
            @endif

            <!-- Video Player -->
            @if($movie->video_path)
                <div class="mt-4">
                    <h5 class="mb-3">
                        <i class="fas fa-play-circle me-2"></i>Watch Movie
                    </h5>
                    <video controls class="w-100 rounded shadow" style="max-height: 300px;">
                        <source src="{{ $movie->video_path }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            @endif
        </div>

        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h1 class="display-5 fw-bold">{{ $movie->title }}</h1>
                @auth
                    @if(Auth::user()->isAdmin())
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('movies.edit', $movie->id) }}">
                                    <i class="fas fa-edit me-2"></i>Edit Movie
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('movies.destroy', $movie->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this movie?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-trash me-2"></i>Delete Movie
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endif
                @endauth
            </div>

            <div class="mb-3">
                <span class="badge bg-primary fs-6 me-2">
                    <i class="fas fa-tag me-1"></i>{{ $movie->genre }}
                </span>
                <span class="badge bg-warning text-dark fs-6">
                    <i class="fas fa-star me-1"></i>
                    {{ number_format($movie->ratings_avg_rating ?? 0, 1) }}
                    ({{ $movie->ratings_count ?? 0 }} {{ Str::plural('rating', $movie->ratings_count ?? 0) }})
                </span>
            </div>

            <p class="lead text-muted mb-3">{{ $movie->description }}</p>

            <div class="mb-4">
                <small class="text-muted">
                    Added by <strong>{{ $movie->creator->name ?? 'Unknown' }}</strong>
                    on {{ \Carbon\Carbon::parse($movie->created_at)->format('M d, Y') }}
                </small>

                                <!-- Watchlist/Favorite Buttons -->
                @auth
                    @if(!Auth::user()->isAdmin())
                        <div class="mt-3">
                            <div class="btn-group" role="group">
                                <form action="{{ route('watchlist.toggle') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="movie_id" value="{{ $movie->id }}">
                                    <button type="submit" class="btn btn-outline-warning"
                                            title="Add to Watchlist">
                                        <i class="fas fa-bookmark me-2"></i>Add to Watchlist
                                    </button>
                                </form>
                                <form action="{{ route('favorites.toggle') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="movie_id" value="{{ $movie->id }}">
                                    <button type="submit" class="btn btn-outline-danger"
                                            title="Add to Favorites">
                                        <i class="fas fa-heart me-2"></i>Add to Favorites
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endauth
            </div>

            <!-- Rating Form -->
            @auth
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Rate This Movie</h5>
                        <form action="{{ route('ratings.store') }}" method="POST" class="rating-form">
                            @csrf
                            <input type="hidden" name="movie_id" value="{{ $movie->id }}">
                            <input type="hidden" name="rating" value="">

                            <div class="d-flex align-items-center">
                                <span class="me-3">Your Rating:</span>
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" class="btn-star">
                                        <i class="fas fa-star"></i>
                                    </button>
                                @endfor
                                <span class="ms-3 text-muted">Click a star to rate</span>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <a href="{{ route('login') }}" class="alert-link">Login</a> to rate this movie and leave comments.
                </div>
            @endauth
        </div>
    </div>

    <!-- Comments Section -->
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">
                <i class="fas fa-comments me-2"></i>
                Comments ({{ count($movie->comments ?? []) }})
            </h3>

            <!-- Add Comment Form -->
            @auth
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Add a Comment</h5>
                        <form action="{{ route('comments.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="movie_id" value="{{ $movie->id }}">
                            <div class="mb-3">
                                <textarea name="content" class="form-control" rows="3"
                                          placeholder="Share your thoughts about this movie..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Post Comment
                            </button>
                        </form>
                    </div>
                </div>
            @endauth

            <!-- Comments List -->
            @if(empty($movie->comments))
                <div class="text-center py-5">
                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No comments yet</h5>
                    <p class="text-muted">Be the first to share your thoughts about this movie!</p>
                </div>
            @else
                @foreach($movie->comments as $comment)
                    <div class="card comment-card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="card-title mb-1">
                                        <i class="fas fa-user me-2"></i>{{ $comment->user->name ?? 'Anonymous' }}
                                    </h6>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}
                                    </small>
                                </div>

                                @auth
                                    @if(Auth::id() === $comment->user_id || Auth::user()->isAdmin())
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                                @if(Auth::user()->isAdmin() && Auth::id() !== $comment->user_id)
                                                    <span class="badge bg-danger ms-1">Admin</span>
                                                @endif
                                            </button>
                                            <ul class="dropdown-menu">
                                                @if(Auth::id() === $comment->user_id)
                                                    <li>
                                                        <button class="dropdown-item" data-bs-toggle="modal"
                                                                data-bs-target="#editCommentModal{{ $comment->id }}">
                                                            <i class="fas fa-edit me-2"></i>Edit
                                                        </button>
                                                    </li>
                                                @endif
                                                @if(Auth::user()->isAdmin())
                                                    <li>
                                                        <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" class="d-inline"
                                                              onsubmit="return confirm('Are you sure you want to delete this comment?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="fas fa-trash me-2"></i>
                                                                @if(Auth::id() !== $comment->user_id)
                                                                    Delete (Admin)
                                                                @else
                                                                    Delete
                                                                @endif
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    @endif
                                @endauth
                            </div>

                            <p class="card-text mt-2">{{ $comment->content }}</p>
                        </div>
                    </div>

                    <!-- Edit Comment Modal -->
                    @auth
                        @if(Auth::id() === $comment->user_id || Auth::user()->isAdmin())
                            <div class="modal fade" id="editCommentModal{{ $comment->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('comments.update', $comment->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Comment</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <textarea name="content" class="form-control" rows="4" required>{{ $comment->content }}</textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Update Comment</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endauth
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
