@extends('layouts.app')

@section('title', 'Admin OTP Login - Movie Database')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-danger">
                <div class="card-header bg-danger text-white text-center py-4">
                    <i class="fas fa-user-shield fa-3x mb-3"></i>
                    <h2 class="fw-bold mb-0">Admin OTP Verification</h2>
                    <p class="mb-0 opacity-75">Secure Admin Access</p>
                </div>

                <div class="card-body p-5">
                    <div class="alert alert-warning d-flex align-items-center mb-4">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div>
                            <strong>Admin Only:</strong> This verification is for administrators only.
                        </div>
                    </div>

                    <div class="alert alert-info d-flex align-items-center mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <div>
                            <strong>Email Verification:</strong> A 6-digit admin verification code will be sent to your email address.
                        </div>
                    </div>

                    <!-- Step 1: Email Input -->
                    <div id="step1">
                        <form id="emailForm">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Admin Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-danger text-white">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input id="email" type="email"
                                           class="form-control"
                                           name="email" required
                                           value="{{ $email ?? '' }}"
                                           placeholder="Enter admin email">
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-danger btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Send Admin OTP
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Step 2: OTP Input -->
                    <div id="step2" style="display: none;">
                        <form id="otpForm" method="POST" action="{{ route('verify.admin.otp') }}">
                            @csrf
                            <input type="hidden" name="email" id="hiddenEmail">
                            <div class="mb-3">
                                <label for="otp" class="form-label fw-semibold">Enter Admin OTP</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-danger text-white">
                                        <i class="fas fa-key"></i>
                                    </span>
                                    <input id="otp" type="text"
                                           class="form-control text-center"
                                           name="otp" required maxlength="6"
                                           placeholder="000000">
                                </div>
                                <div class="form-text">
                                    Enter the 6-digit admin verification code
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-check me-2"></i>Verify & Access Admin
                                </button>
                                <button type="button" id="resendBtn" class="btn btn-outline-secondary">
                                    <i class="fas fa-redo me-2"></i>Resend OTP
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Demo Credentials Info -->
                    <div class="card bg-light mt-4">
                        <div class="card-body">
                            <h6 class="card-title text-muted">
                                <i class="fas fa-info-circle me-2"></i>Demo Admin Credentials
                            </h6>
                            <div class="row small">
                                <div class="col-sm-4">
                                    <strong>Email:</strong>
                                </div>
                                <div class="col-sm-8">
                                    <code>moviebloggroup3@gmail.com</code>
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

@section('extra-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const emailForm = document.getElementById('emailForm');
    const otpForm = document.getElementById('otpForm');
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const resendBtn = document.getElementById('resendBtn');
    const hiddenEmail = document.getElementById('hiddenEmail');
    let userEmail = '';

    // Check if email is pre-filled (coming from admin login form)
    const prefilledEmail = document.getElementById('email').value;
    if (prefilledEmail) {
        userEmail = prefilledEmail;
        hiddenEmail.value = userEmail;
        step1.style.display = 'none';
        step2.style.display = 'block';

        // Show info that admin OTP has been sent
        showMessage('Admin OTP has been sent to your email address.', 'info');
    }

    emailForm.addEventListener('submit', function(e) {
        e.preventDefault();
        userEmail = document.getElementById('email').value;
        hiddenEmail.value = userEmail;

        // Show step 2 and indicate admin OTP was sent
        step1.style.display = 'none';
        step2.style.display = 'block';

        showMessage('Admin OTP has been sent to your email address.', 'info');
    });

    resendBtn.addEventListener('click', function() {
        if (!userEmail) return;

        // Disable button temporarily
        resendBtn.disabled = true;
        resendBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';

        fetch('{{ route("resend.admin.otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                email: userEmail
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                // For development - show OTP in console
                if (data.otp) {
                    console.log('Development Admin OTP:', data.otp);
                    showMessage('Development Admin OTP: ' + data.otp + ' (Check console)', 'warning');
                }
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            showMessage('Failed to resend admin OTP. Please try again.', 'error');
        })
        .finally(() => {
            // Re-enable button
            resendBtn.disabled = false;
            resendBtn.innerHTML = '<i class="fas fa-redo me-2"></i>Resend OTP';
        });
    });

    function showMessage(message, type) {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.temp-alert');
        existingAlerts.forEach(alert => alert.remove());

        // Create new alert
        const alertClass = type === 'error' ? 'alert-danger' :
                          type === 'success' ? 'alert-success' :
                          type === 'warning' ? 'alert-warning' : 'alert-info';

        const alertHtml = `
            <div class="alert ${alertClass} temp-alert mt-3">
                <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : type === 'success' ? 'check' : 'info-circle'} me-2"></i>
                ${message}
            </div>
        `;

        const step2Element = document.getElementById('step2');
        step2Element.insertAdjacentHTML('beforeend', alertHtml);

        // Remove alert after 5 seconds
        setTimeout(() => {
            const tempAlert = document.querySelector('.temp-alert');
            if (tempAlert) tempAlert.remove();
        }, 5000);
    }
});
</script>
@endsection
@endsection
