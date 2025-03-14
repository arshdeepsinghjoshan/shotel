@extends('layouts.guest')

@section('title', 'Forget Password')
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
                    <h4 class="mb-2">Forgot Password?</h4>
                    <p class="mb-4">Enter your email and we'll send you instructions to reset your password</p>

                    <form id="formAuthentication" class="mb-3" action="{{ route('forget.password') }}" method="POST">
                        @csrf

                        <div class="mb-3 required">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" class="form-control" id="email" name="email" value="{{ old('email', $model->email) }}" placeholder="Enter your email" autofocus />
                        </div>
                        <div class="mb-3 required form-password-toggle">
                                <label class="form-label" for="password">New Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password" />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>

                            <div class="mb-3 required form-password-toggle">
                                <label class="form-label" for="password">New Confirm Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="confirm_password" class="form-control" name="confirm_password" />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>
                        <div class="mb-3 ">
                            <button class="btn btn-primary d-grid w-100" type="submit">Send Reset Link</button>
                        </div>
                    </form>

                    <p class="text-center">
                        <a href="{{ url('login') }}">
                            <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
                            <span>Back to login</span>
                        </a>
                    </p>
                </div>
            </div>
            <!-- /Register -->
        </div>
    </div>
</div>
@stop