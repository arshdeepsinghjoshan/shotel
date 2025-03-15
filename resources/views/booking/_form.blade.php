<form action="{{ route(empty($model->exists) ? 'booking.add' : 'booking.update', $model->id) }}" method="post"
    id="booking-update" enctype="multipart/form-data">
    @csrf
    <div class="row align-items-starts">

        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Customer </label>
                <select name="user_id" class="validate form-control" id="user_id">
                    <option value="">Select Customer</option>

                    @foreach ($model->getUserOption() as $user)
                        <option value="{{ $user->id }}"
                            {{ $user->id == $model->user_id ? 'selected' : '' }}>{{ $user->name }}
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
                <label class="pt-2 fw-bold" for="btncheck1"> Room </label>
                <select name="room_id" class="validate form-control" id="room_id">
                    <option value="">Select Room</option>
                    @foreach ($model->getRoomOption() as $room)
                        <option value="{{ $room->id }}"
                            {{ $room->id == $model->room_id ? 'selected' : '' }}>{{ $room->room_number }} - ₹{{ $room->price }}/night
                        </option>
                    @endforeach

                </select>
            </div>
            @error('room_id')
                <p style="color:red;">{{ $errors->first('room_id') }}</p>
            @enderror
        </div>

        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Check-in Date </label>
                <input type="date" class="form-control d-block" name="check_in"
                    value="{{ old('check_in', $model->check_in) }}">
            </div>
            @error('check_in')
                <p style="color:red;">{{ $errors->first('check_in') }}</p>
            @enderror
        </div>
        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Check-out Date </label>
                <input type="date" class="form-control d-block" name="check_out"
                    value="{{ old('check_out', $model->check_out) }}">
            </div>
            @error('check_out')
                <p style="color:red;">{{ $errors->first('check_out') }}</p>
            @enderror
        </div>
        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Is Paid </label>

                <select name="is_paid" class="validate form-control" id="is_paid">
                    @foreach ($model->getIsPaidOptions() as $key => $state)
                        <option value="{{ $key }}"
                            {{ old('is_paid', $model->is_paid) == $key ? 'selected' : '' }}>
                            {{ $state }}
                        </option>
                    @endforeach
                </select>
            </div>
            @error('is_paid')
                <p style="color:red;">{{ $errors->first('is_paid') }}</p>
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
