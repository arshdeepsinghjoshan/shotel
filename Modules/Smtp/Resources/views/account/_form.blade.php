<form class="row g-3 needs-validation" action="{{ !empty($model->exists) ? route('smtp.update') :  route('smtp.add') }}" method="post" id="update_form" novalidate>
    @csrf
    <div class="col-md-4 required">
        <label for="validationCustom01" class="form-label">Mailer</label>
        <input type="text" class="form-control" value="{{ old('mailer', !empty($model->mailer) ? $model->mailer : '') }}" name="mailer" id="validationCustom01" required>
        <div class="valid-feedback">
            Looks good!
        </div>
    </div>
    <input type="hidden" name="id" id="id" value="{{ !empty($model->id) ? $model->id : '' }}" required />

    <div class="col-md-4 required">
        <label for="validationCustom02" class="form-label">Host</label>
        <input type="text" class="form-control" value="{{ old('host', !empty($model->host) ? $model->host : '') }}" name="host" id="validationCustom02" required>
        <div class="valid-feedback">
            Looks good!
        </div>
    </div>
    <div class="col-md-4 required">
        <label for="validationCustomUsername" class="form-label">Port</label>
        <div class="input-group has-validation">
            <input type="text" class="form-control" id="validationCustomUsername" value="{{ old('port', !empty($model->port) ? $model->port : '') }}" name="port" aria-describedby="inputGroupPrepend" required>
            <div class="invalid-feedback">
                Please choose a Port.
            </div>
        </div>
    </div>
    <div class="col-md-4 required">
        <label for="validationCustom03" class="form-label">Username</label>
        <input type="text" class="form-control" value="{{ old('username', !empty($model->username) ? $model->username : '') }}" name="username" id="validationCustom03" required>
        <div class="invalid-feedback">
            Please provide a valid Username.
        </div>
    </div>
    <div class="col-md-4 required">
        <label for="validationCustom03" class="form-label">Password</label>
        <input type="text" class="form-control" value="{{ old('password', !empty($model->password) ? $model->password : '') }}" name="password" id="validationCustom03" required>
        <div class="invalid-feedback">
            Please provide a valid Password.
        </div>
    </div>

    <div class="col-md-4 required">
        <label for="validationCustom03" class="form-label">Encryption</label>
        <input type="text" class="form-control" value="{{ old('encryption', !empty($model->encryption) ? $model->encryption : '') }}" name="encryption" id="validationCustom03" required>
        <div class="invalid-feedback">
            Please provide a valid Encryption.
        </div>
    </div>


    <div class="col-md-4 required">
        <label for="validationCustom03" class="form-label">From Address</label>
        <input type="text" class="form-control" value="{{ old('from_address', !empty($model->from_address) ? $model->from_address : '') }}" name="from_address" id="validationCustom03" required>
        <div class="invalid-feedback">
            Please provide a valid From Address.
        </div>
    </div>


    <div class="col-md-4 required">
        <label for="validationCustom03" class="form-label">From Name</label>
        <input type="text" class="form-control" value="{{env('APP_NAME', false)}}" disabled name="address" id="validationCustom03" required>
        <div class="invalid-feedback">
            Please provide a valid From Name.
        </div>
    </div>

    <div class="col-12 text-right">
        <button class="btn btn-outline-primary " type="submit">Submit form</button>
    </div>
</form><!-- End Custom Styled Validation -->