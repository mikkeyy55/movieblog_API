@extends('layouts.app')

@section('title', 'Edit ' . $movie->title . ' - Movie Database')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-warning">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Movie: {{ $movie->title }}
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('movies.update', $movie->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Movie Title <span class="text-danger">*</span></label>
                            <input id="title" type="text"
                                   class="form-control @error('title') is-invalid @enderror"
                                   name="title" value="{{ old('title', $movie->title) }}"
                                   required placeholder="Enter the movie title">
                            @error('title')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="genre" class="form-label">Genre <span class="text-danger">*</span></label>
                            <select id="genre" class="form-select @error('genre') is-invalid @enderror"
                                    name="genre" required>
                                <option value="">Select a genre</option>
                                @php
                                    $genres = ['Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 'Documentary', 'Drama', 'Family', 'Fantasy', 'Horror', 'Mystery', 'Romance', 'Sci-Fi', 'Thriller', 'War', 'Western'];
                                @endphp
                                @foreach($genres as $genre)
                                    <option value="{{ $genre }}" {{ old('genre', $movie->genre) === $genre ? 'selected' : '' }}>
                                        {{ $genre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('genre')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="release_year" class="form-label">Release Year</label>
                                    <input id="release_year" type="number"
                                           class="form-control @error('release_year') is-invalid @enderror"
                                           name="release_year" value="{{ old('release_year', $movie->release_year) }}"
                                           min="1900" max="{{ date('Y') + 5 }}"
                                           placeholder="e.g., {{ date('Y') }}">
                                    @error('release_year')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea id="description" class="form-control @error('description') is-invalid @enderror"
                                      name="description" rows="5" required
                                      placeholder="Provide a detailed description of the movie">{{ old('description', $movie->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="cover_image" class="form-label">Cover Image</label>
                            <input id="cover_image" type="file"
                                   class="form-control @error('cover_image') is-invalid @enderror"
                                   name="cover_image" accept="image/*">
                            @error('cover_image')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                            <div class="form-text">
                                Optional: Upload a new movie poster image (JPEG, PNG, JPG, GIF, max 2MB).
                            </div>
                        </div>

                        <!-- Current Image Preview -->
                        @if($movie->cover_image)
                            <div class="mb-4">
                                <label class="form-label">Current Image:</label>
                                <div class="text-center">
                                    <img src="{{ $movie->cover_image }}" alt="{{ $movie->title }}"
                                         class="img-thumbnail" style="max-height: 200px;">
                                </div>
                            </div>
                        @endif

                        <div class="mb-4">
                            <label for="video" class="form-label">Movie Video</label>
                            <input id="video" type="file"
                                   class="form-control @error('video') is-invalid @enderror"
                                   name="video" accept="video/*">
                            @error('video')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                            <div class="form-text">
                                Optional: Upload a new movie video file (MP4, AVI, MOV, WMV, FLV, max 100MB).
                            </div>
                        </div>

                        <!-- Current Video Preview -->
                        @if($movie->video_path)
                            <div class="mb-4">
                                <label class="form-label">Current Video:</label>
                                <div class="text-center">
                                    <video controls style="max-width: 100%; max-height: 300px;">
                                        <source src="{{ $movie->video_path }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                    <div class="mt-2">
                                        <small class="text-muted">Current video: {{ basename($movie->video_path) }}</small>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('movies.show', $movie->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>Update Movie
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
