@extends('layouts.app')

@section('title', 'Admin Login - Movie Database')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-danger">
                <div class="card-header bg-danger text-white text-center py-4">
                    <i class="fas fa-user-shield fa-3x mb-3"></i>
                    <h2 class="fw-bold mb-0">Admin Access</h2>
                    <p class="mb-0 opacity-75">Movieblog Administration Panel</p>
                </div>

                <div class="card-body p-5">
                    <div class="alert alert-warning d-flex align-items-center mb-4">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div>
                            <strong>Restricted Access:</strong> Only administrators can access this area.
                        </div>
                    </div>

                    <div class="alert alert-info d-flex align-items-center mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <div>
                            <strong>Two-Step Authentication:</strong> After entering your admin credentials, you'll receive an OTP to complete the login.
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.login.post') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Admin Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-danger text-white">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input id="email" type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email') }}"
                                       required autocomplete="email" autofocus
                                       placeholder="Enter admin email">
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">Admin Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-danger text-white">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input id="password" type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       name="password" required autocomplete="current-password"
                                       placeholder="Enter admin password">
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Keep me signed in
                                </label>
                            </div>
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-danger btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Access Admin Panel
                            </button>
                        </div>
                    </form>

                    <!-- Demo Credentials Info -->
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title text-muted">
                                <i class="fas fa-info-circle me-2"></i>Demo Credentials
                            </h6>
                            <div class="row small">
                                <div class="col-sm-4">
                                    <strong>Email:</strong>
                                </div>
                                <div class="col-sm-8">
                                    <code>moviebloggroup3@gmail.com</code>
                                </div>
                            </div>
                            <div class="row small">
                                <div class="col-sm-4">
                                    <strong>Password:</strong>
                                </div>
                                <div class="col-sm-8">
                                    <code>Movie@123</code>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="mb-0 text-muted">Not an admin?
                            <a href="{{ route('login') }}" class="text-decoration-none">Regular Login</a>
                        </p>
                        <p class="mb-0 text-muted mt-2">
                            <a href="{{ route('movies.index') }}" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>Back to Movies
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('extra-css')
<style>
    .card {
        border-radius: 15px;
        overflow: hidden;
    }
    .card-header {
        border-bottom: none;
    }
    .input-group-text {
        border: none;
    }
    .form-control {
        border-left: none;
    }
    .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
        border-color: #dc3545;
    }
</style>
@endsection
@endsection
