<form class="row g-3 needs-validation" action="{{ !empty($model->exists) ? route('kyc.update') :  route('kyc.add') }}" method="post" id="kyc_form" novalidate enctype="multipart/form-data">
    @csrf
    <div class="col-md-4 required">
        <label for="validationCustom01" class="form-label">Name</label>
        <input type="text" class="form-control" value="{{ old('name', !empty($model->name) ? $model->name : '') }}" name="name" id="validationCustom01" required>
        <div class="valid-feedback">
            Looks good!
        </div>
    </div>
    <input type="hidden" name="id" id="id" value="{{ !empty($model->id) ? $model->id : '' }}" required />

    <div class="col-md-4 required">
        <label for="validationCustom02" class="form-label">Email</label>
        <input type="text" class="form-control" value="{{ old('email', !empty($model->email) ? $model->email : '') }}" name="email" id="validationCustom02" required>
        <div class="valid-feedback">
            Looks good!
        </div>
    </div>
    <div class="col-md-4 required">
        <label for="validationCustomUsername" class="form-label">Contact Number</label>
        <div class="input-group has-validation">
            <input type="text" class="form-control" id="validationCustomUsername" value="{{ old('contact_number', !empty($model->contact_number) ? $model->contact_number : '') }}" name="contact_number" aria-describedby="inputGroupPrepend" required>
            <div class="invalid-feedback">
                Please choose a contact_number.
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-4 col-md-6 col-12">
        <div class="mb-3 required">
            <label class="pt-2 fw-bold" for="btncheck1"> National Type </label>
            <select name="type_id" class="validate form-control" id="type_id">
                @foreach ($model->getTypeOptions() as $key => $role)
                <option value="{{ $key }}" {{ old('type_id', $model->type_id) == $key ? 'selected' : '' }}>
                    {{ $role }}
                </option>
                @endforeach
            </select>
        </div>
        @error('type_id')
        <p style="color:red;">{{ $errors->first('type_id') }}</p>
        @enderror
    </div>
    <div class="col-md-4 required">
        <label class="pt-2 fw-bold" for="btncheck1">National Id</label>
        <input type="text" class="form-control" value="{{ old('national_id', !empty($model->national_id) ? $model->national_id : '') }}" name="national_id" required>
        <div class="invalid-feedback">
            Please provide a valid national_id.
        </div>
    </div>


    <div class="col-md-4 required">
        <label class="pt-2 fw-bold" for="btncheck1">National Front Image</label>
        <input type="file" class="form-control" name="front_image" required>
        <div class="invalid-feedback">
            Please provide a valid front_image.
        </div>
    </div>

    <div class="col-md-4 required">
        <label class="pt-2 fw-bold" for="btncheck1">National Back Image</label>
        <input type="file" class="form-control" name="back_image" required>
        <div class="invalid-feedback">
            Please provide a valid Back Image.
        </div>
    </div>
    <div class="col-md-4 required">
        <label class="pt-2 fw-bold" for="btncheck1">Selfie Image</label>
        <input type="file" class="form-control" name="selfie_image" required>
        <div class="invalid-feedback">
            Please provide a valid Selfie Image.
        </div>
    </div>

    <div class="col-md-4 required">
        <label class="pt-2 fw-bold" for="btncheck1">Short Video</label>
        <input type="file" class="form-control" id="import_video" name="video" required>
        <div class="invalid-feedback">
            Please provide a valid video.
        </div>
    </div>


    <div class="col-12 text-right">
        <button class="btn btn-outline-primary " type="submit">Submit form</button>
    </div>
</form><!-- End Custom Styled Validation -->
<script src="{{ url('/assets/vendor/libs/jquery/jquery.js') }}"></script>

<script>
    var isFileInputChange = false;

    function readURL(input, submit = false) {
        if (input.files && input.files[0]) {
            var file = input.files[0];
            var extension = file.name.split('.').pop().toLowerCase();
            var allowedExtensions = ['mp4', 'mov']; // Allowed video extensions
            var maxSize = 10000000; // Maximum file size in bytes (10 MB)
            if (allowedExtensions.includes(extension)) {
                if (file.size <= maxSize) {
                    if (submit) {
                        isFileInputChange = true;
                        $('#kyc_form').submit();
                    }
                } else {
                    alert('File is too large. Please choose a file smaller than 10 MB.');
                    return;
                }
            } else {
                alert('Invalid file extension. Please choose an MP4 or MOV file.');
                return;
            }
        }
    }

    $("#import_video").change(function() {
        readURL(this, false);
    });

    $("form").on('submit', function(e) {
        if (!isFileInputChange) {
            e.preventDefault();
            readURL($("#import_video")[0], true);
        }
    });
</script>