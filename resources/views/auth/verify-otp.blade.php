@extends('layouts.app')

@section('title', 'Verify OTP - Movie Database')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="mb-3">Email Verification</h4>
                    <p>We have sent a 6-digit code to your email. Enter it below to activate your account.</p>

                    <form method="POST" action="{{ route('verify.otp') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('email') }}">
                        
                        <div class="mb-3">
                            <label>Enter OTP</label>
                            <input type="text" name="otp" class="form-control" required maxlength="6" placeholder="123456">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Verify</button>
                    </form>

                    @if ($errors->any())
                        <div class="alert alert-danger mt-3">
                            {{ $errors->first() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
