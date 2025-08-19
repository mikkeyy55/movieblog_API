@extends('layouts.app')

@section('title', 'Add New Movie - Movie Database')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Add New Movie
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('movies.store') }}" id="movieForm" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Movie Title <span class="text-danger">*</span></label>
                            <input id="title" type="text"
                                   class="form-control @error('title') is-invalid @enderror"
                                   name="title" value="{{ old('title') }}"
                                   required placeholder="Enter the movie title">
                            @error('title')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="genre" class="form-label">Genre <span class="text-danger">*</span></label>
                                    <select id="genre" class="form-select @error('genre') is-invalid @enderror"
                                            name="genre" required>
                                        <option value="">Select a genre</option>
                                        <option value="Action" {{ old('genre') === 'Action' ? 'selected' : '' }}>Action</option>
                                        <option value="Adventure" {{ old('genre') === 'Adventure' ? 'selected' : '' }}>Adventure</option>
                                        <option value="Animation" {{ old('genre') === 'Animation' ? 'selected' : '' }}>Animation</option>
                                        <option value="Comedy" {{ old('genre') === 'Comedy' ? 'selected' : '' }}>Comedy</option>
                                        <option value="Crime" {{ old('genre') === 'Crime' ? 'selected' : '' }}>Crime</option>
                                        <option value="Documentary" {{ old('genre') === 'Documentary' ? 'selected' : '' }}>Documentary</option>
                                        <option value="Drama" {{ old('genre') === 'Drama' ? 'selected' : '' }}>Drama</option>
                                        <option value="Family" {{ old('genre') === 'Family' ? 'selected' : '' }}>Family</option>
                                        <option value="Fantasy" {{ old('genre') === 'Fantasy' ? 'selected' : '' }}>Fantasy</option>
                                        <option value="Horror" {{ old('genre') === 'Horror' ? 'selected' : '' }}>Horror</option>
                                        <option value="Mystery" {{ old('genre') === 'Mystery' ? 'selected' : '' }}>Mystery</option>
                                        <option value="Romance" {{ old('genre') === 'Romance' ? 'selected' : '' }}>Romance</option>
                                        <option value="Sci-Fi" {{ old('genre') === 'Sci-Fi' ? 'selected' : '' }}>Sci-Fi</option>
                                        <option value="Thriller" {{ old('genre') === 'Thriller' ? 'selected' : '' }}>Thriller</option>
                                        <option value="War" {{ old('genre') === 'War' ? 'selected' : '' }}>War</option>
                                        <option value="Western" {{ old('genre') === 'Western' ? 'selected' : '' }}>Western</option>
                                    </select>
                                    @error('genre')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="release_year" class="form-label">Release Year</label>
                                    <input id="release_year" type="number"
                                           class="form-control @error('release_year') is-invalid @enderror"
                                           name="release_year" value="{{ old('release_year') }}"
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
                                      placeholder="Provide a detailed description of the movie">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tags" class="form-label">Tags</label>
                            <input id="tags" type="text"
                                   class="form-control @error('tags') is-invalid @enderror"
                                   name="tags" value="{{ old('tags') }}"
                                   placeholder="action, blockbuster, superhero (comma-separated)">
                            @error('tags')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                            <div class="form-text">
                                Optional: Add tags separated by commas to help categorize the movie.
                            </div>
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
                                Optional: Upload a movie poster image (JPEG, PNG, JPG, GIF, max 2MB).
                            </div>
                        </div>

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
                                Optional: Upload the movie video file (MP4, AVI, MOV, WMV, FLV, max 100MB).
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('movies.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-plus me-2"></i>Add Movie
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('movieForm');
    const submitBtn = document.getElementById('submitBtn');

    form.addEventListener('submit', function(e) {
        // Disable submit button to prevent double submission
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Movie...';
    });

    // Character counter for description
    const description = document.getElementById('description');
    const descriptionCounter = document.createElement('small');
    descriptionCounter.className = 'text-muted';
    descriptionCounter.style.fontSize = '0.875rem';
    description.parentNode.appendChild(descriptionCounter);

    function updateCounter() {
        const length = description.value.length;
        const minLength = 10;
        const color = length >= minLength ? 'text-success' : 'text-danger';
        descriptionCounter.className = `text-muted ${color}`;
        descriptionCounter.textContent = `${length} characters (minimum ${minLength})`;
    }

    description.addEventListener('input', updateCounter);
    updateCounter();
});
</script>
@endsection
