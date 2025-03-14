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
<form id="blog-category-form" class="row needs-validation justify-content-center"
    action="{{ route(empty($model->exists) ? 'user.add' : 'user.update', $model->id) }}" method="post" novalidate
    enctype="multipart/form-data">
    @csrf
    <div class="row align-items-starts">
        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> First Name </label>
                <input type="text" class="form-control d-block" name="name"
                    value="{{ old('name', $model->name) }}">
            </div>
            @error('name')
            <p style="color:red;">{{ $errors->first('name') }}</p>
            @enderror
        </div>

        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Phone Number </label>
                <input type="text" class="form-control d-block" name="contact_no"
                    value="{{ old('contact_no', $model->contact_no) }}">
            </div>
            @error('contact_no')
            <p style="color:red;">{{ $errors->first('contact_no') }}</p>
            @enderror
        </div>

        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3">
                <label class="pt-2 fw-bold" for="btncheck1"> Father Name </label>
                <input type="text" class="form-control d-block" name="father_name"
                    value="{{ old('father_name', $model->father_name) }}">
            </div>
            @error('father_name')
            <p style="color:red;">{{ $errors->first('father_name') }}</p>
            @enderror
        </div>
        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3">
                <label class="pt-2 fw-bold" for="btncheck1"> Email </label>
                <input type="text" class="form-control d-block" name="email"
                    value="{{ old('email', $model->email) }}">
            </div>
            @error('email')
            <p style="color:red;">{{ $errors->first('email') }}</p>
            @enderror
        </div>

        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 ">
                <label class="pt-2 fw-bold" for="btncheck1"> Address </label>
                <input type="text" class="form-control d-block" name="address"
                    value="{{ old('address', $model->address) }}">
            </div>
            @error('address')
            <p style="color:red;">{{ $errors->first('address') }}</p>
            @enderror
        </div>



        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3">
                <label class="pt-2 fw-bold" for="btncheck1"> Profile Image </label>
                <input type="file" class="form-control d-block"
                    name="profile_image">
            </div>
            @error('profile_image')
            <p style="color:red;">{{ $errors->first('profile_image') }}</p>
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

<x-a-typeahead :model="''" :column="[
    [
        'id' => 'typeahead-input',
        'url' => 'pin-code/list',
    ],
]" />