@extends('layouts.guest')

@section('title', 'Login Page')
@section('content')
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            <!-- Register -->

            <div class="card">
                <div class="card-body">
                    <!-- Logo -->
                    @include('include.message')

                    <div class="app-brand justify-content-center">
                        <a href="index.html" class="app-brand-link gap-2">

                            <span class=" text-body fw-bolder">{{ env('APP_NAME') }}</span>
                        </a>
                    </div>
                    <!-- /Logo -->
                    <h4 class="mb-2">Welcome to {{ env('APP_NAME') }}!</h4>
                    <p class="mb-4">Please verify your email address. We have sent an OTP to your email.</p>

                    <form id="formAuthentication" class="mb-3" action="{{ route('confirm.email',$model->activation_key) }}" method="POST">
                        @csrf

                        <div class="mb-3 required">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" class="form-control" id="email" name="email" value="{{ old('email', $model->email) }}" placeholder="Enter your email" autofocus />
                        </div>
                        <div class="mb-3 required form-password-toggle">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password">Otp</label>
                            </div>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control" name="otp" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                        </div>

                        <div class="mb-3 ">
                            <button class="btn btn-primary d-grid w-100" type="submit">Verify</button>
                        </div>
                    </form>

                    <p class="text-center">
                        <span>New on our platform?</span>
                        <a href="{{ url('register') }}">
                            <span>Create an account</span>
                        </a>
                    </p>
                </div>
            </div>
            <!-- /Register -->
        </div>
    </div>
</div>
@stop