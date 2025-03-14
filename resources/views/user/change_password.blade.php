@extends('layouts.master')
@section('title', 'User Add')
@section('content')
<x-a-breadcrumb :columns="[
        [
            'url' => '/',
            'label' => 'Home',
        ],
        [
            'url' => 'user/change-password',
            'label' => 'Change Password',
        ],
    ]" />

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-header">
                    <h3>
                        {{ __('Change Password') }}

                    </h3>
                </div>
                <div class="card-body">
                    @php
                    use App\Models\User;

                    @endphp

                    <style>
                        .eye-icon {
                            position: absolute;
                            top: 40px;
                            right: 20px;
                        }

                        .position-relative {
                            position: relative !important;
                        }
                    </style>
                    <form id="blog-category-form" class="row needs-validation justify-content-center" action="{{ route('password.update') }}" method="post" novalidate enctype="multipart/form-data">
                        @csrf
                        <div class="row align-items-starts">
                            <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                                <div class="position-relative">
                                    <div class="mb-3 required">
                                        <label class="pt-2 fw-bold" for="btncheck1">Old Password </label>
                                        <input type="password" id="old_password" class="form-control d-block" name="old_password">
                                    </div>
                                    <div class="eye-icon">
                                        <i toggle="#user-old_password" class="fa toggle-old-password fa-eye-slash"></i>
                                    </div>
                                </div>
                                @error('old_password')
                                <p style="color:red;">{{ $errors->first('old_password') }}</p>
                                @enderror
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                                <div class="position-relative">
                                    <div class="mb-3 required">
                                        <label class="pt-2 fw-bold" for="btncheck1"> New Password </label>
                                        <input type="password" id="password" class="form-control d-block" name="new_password">
                                    </div>
                                    <div class="eye-icon">
                                        <i toggle="#user-confirm_password" class="fa toggle-password fa-eye-slash"></i>
                                    </div>
                                </div>
                                @error('new_password')
                                <p style="color:red;">{{ $errors->first('new_password') }}</p>
                                @enderror
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-6 col-12 change-password-field">
                                <div class="position-relative">
                                    <div class="mb-3 required">
                                        <label class="pt-2 fw-bold" for="btncheck1">New Confirm Password </label>
                                        <input type="password" id="confirm_password" class="form-control d-block" name="confirm_password">
                                    </div>
                                    <div class="eye-icon">
                                        <i toggle="#user-confirm_password" class="fa toggle-password-confirm fa-eye-slash"></i>
                                    </div>
                                </div>
                                @error('confirm_password')
                                <p style="color:red;">{{ $errors->first('confirm_password') }}</p>
                                @enderror
                            </div>


                            <div class="col-lg-12">
                                <div class="d-flex align-items-center justify-content-end">
                                    <div class="downoad-btns text-end my-4">
                                        <button class="btn btn-primary text-white ms-2">
                                            @empty($model->exists)
                                            {{ __('Add') }}
                                            @else
                                            {{ __('Update') }}
                                            @endempty
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection