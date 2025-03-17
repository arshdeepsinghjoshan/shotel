<form action="{{ route(empty($model->exists) ? 'reservation.add' : 'reservation.update', $model->id) }}" method="post"
    id="reservation-update" enctype="multipart/form-data">
    @csrf
    <div class="row align-items-starts">

        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Customer </label>
                <select name="user_id" class="validate form-control" id="user_id">
                    <option value="">Select Customer</option>

                    @foreach ($model->getUserOption() as $user)
                    <option value="{{ $user->id }}"
                        {{ old('user_id', $model->user_id) == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                    @endforeach
                </select>

            </div>
            @error('user_id')
            <p style="color:red;">{{ $errors->first('user_id') }}</p>
            @enderror
        </div>

        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Table </label>
                <select name="table_id" class="validate form-control" id="table_id">
                    <option value="">Select Table</option>
                    @foreach ($model->getTableOption() as $table)
                    <option value="{{ $table->id }}"
                        {{ old('table_id', $model->table_id) == $table->id ? 'selected' : '' }}>
                        {{ $table->table_number }}
                    </option>
                    @endforeach
                </select>

            </div>
            @error('table_id')
            <p style="color:red;">{{ $errors->first('table_id') }}</p>
            @enderror
        </div>

        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1">Reservation Time </label>
                <input type="datetime-local" class="form-control d-block" name="reservation_time"
                    value="{{ old('reservation_time', $model->reservation_time) }}">
            </div>
            @error('reservation_time')
            <p style="color:red;">{{ $errors->first('reservation_time') }}</p>
            @enderror
        </div>

        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Status </label>

                <select name="state_id" class="validate form-control" id="state_id">
                    @foreach ($model->getStateOptions() as $key => $state)
                    <option value="{{ $key }}"
                        {{ old('state_id', $model->state_id) == $key ? 'selected' : '' }}>
                        {{ $state }}
                    </option>
                    @endforeach
                </select>
            </div>
            @error('state_id')
            <p style="color:red;">{{ $errors->first('state_id') }}</p>
            @enderror
        </div>
        <input type="hidden" name="id" id="id" value="{{ $model->id }}" required />
        <div class="preview-images"></div>
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