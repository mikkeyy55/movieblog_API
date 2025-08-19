@extends('layouts.app')

@section('title', 'OTP Login - Movie Database')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-mobile-alt fa-3x text-primary mb-3"></i>
                        <h2 class="fw-bold">OTP Verification</h2>
                        <p class="text-muted">Enter your email to receive a verification code</p>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Email Verification:</strong> A 6-digit verification code will be sent to your email address.
                        </div>
                    </div>

                    <!-- Step 1: Email Input -->
                    <div id="step1">
                        <form id="emailForm">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input id="email" type="email"
                                           class="form-control"
                                           name="email" required
                                           value="{{ $email ?? '' }}"
                                           placeholder="Enter your email address">
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Send OTP
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Step 2: OTP Input -->
                    <div id="step2" style="display: none;">
                        <form id="otpForm" method="POST" action="{{ route('verify.otp') }}">
                            @csrf
                            <input type="hidden" name="email" id="hiddenEmail">
                            <div class="mb-3">
                                <label for="otp" class="form-label">Enter OTP</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-key"></i>
                                    </span>
                                    <input id="otp" type="text"
                                           class="form-control text-center"
                                           name="otp" required maxlength="6"
                                           placeholder="000000">
                                </div>
                                <div class="form-text">
                                    Enter the 6-digit code sent to your email
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-check me-2"></i>Verify & Login
                                </button>
                                <button type="button" id="resendBtn" class="btn btn-outline-secondary">
                                    <i class="fas fa-redo me-2"></i>Resend OTP
                                </button>
                            </div>
                        </form>
                    </div>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="mb-0">Prefer password login?
                            <a href="{{ route('login') }}" class="text-decoration-none">Sign in here</a>
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

    // Check if email is pre-filled (coming from login form)
    const prefilledEmail = document.getElementById('email').value;
    if (prefilledEmail) {
        userEmail = prefilledEmail;
        hiddenEmail.value = userEmail;
        step1.style.display = 'none';
        step2.style.display = 'block';

        // Show info that OTP has been sent
        showMessage('OTP has been sent to your email address.', 'info');
    }

    emailForm.addEventListener('submit', function(e) {
    e.preventDefault();
    userEmail = document.getElementById('email').value;
    hiddenEmail.value = userEmail;

    // Disable button during request
    const submitBtn = e.target.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';

    fetch('{{ route("send.otp") }}', {
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
        if (data.message === 'OTP sent successfully') {
            // Show step 2
            step1.style.display = 'none';
            step2.style.display = 'block';
            showMessage('OTP has been sent to your email address.', 'success');
            
            // For development - show OTP in console
            if (data.otp) {
                console.log('Development OTP:', data.otp);
                showMessage('Development OTP: ' + data.otp + ' (Check console)', 'warning');
            }
        } else {
            showMessage('Failed to send OTP. Please try again.', 'error');
        }
    })
    .catch(error => {
        showMessage('An error occurred. Please try again.', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Send OTP';
    });
});

    resendBtn.addEventListener('click', function() {
        if (!userEmail) return;

        // Disable button temporarily
        resendBtn.disabled = true;
        resendBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';

        fetch('{{ route("resend.otp") }}', {
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
                    console.log('Development OTP:', data.otp);
                    showMessage('Development OTP: ' + data.otp + ' (Check console)', 'warning');
                }
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            showMessage('Failed to resend OTP. Please try again.', 'error');
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
