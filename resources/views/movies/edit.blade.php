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
                    <form method="POST" action="{{ route('movies.update', $movie->id) }}">
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
                            <label for="cover_image" class="form-label">Cover Image URL</label>
                            <input id="cover_image" type="url"
                                   class="form-control @error('cover_image') is-invalid @enderror"
                                   name="cover_image" value="{{ old('cover_image', $movie->cover_image) }}"
                                   placeholder="https://example.com/movie-poster.jpg">
                            @error('cover_image')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                            <div class="form-text">
                                Optional: Provide a URL to the movie poster image.
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
