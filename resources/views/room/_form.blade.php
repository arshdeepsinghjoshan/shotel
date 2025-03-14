<form action="{{ route(empty($model->exists) ? 'room.add' : 'room.update', $model->id) }}" method="post" id="room-update"
    enctype="multipart/form-data">
    @csrf
    <div class="row align-items-starts">
        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Room Number </label>
                <input type="text" class="form-control d-block" name="room_number"
                    value="{{ old('room_number', $model->room_number) }}">
            </div>
            @error('room_number')
                <p style="color:red;">{{ $errors->first('room_number') }}</p>
            @enderror
        </div>


        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Select Room Type </label>
                <select name="type_id" class="validate form-control" id="type_id">
                    @foreach ($model->getTypeOptions() as $key => $type)
                        <option value="{{ $key }}"
                            {{ old('type_id', $model->type_id) == $key ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>
            @error('type_id')
                <p style="color:red;">{{ $errors->first('type_id') }}</p>
            @enderror
        </div>
        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> AC/Non AC </label>
                <select name="ac_type" class="validate form-control" id="ac_type">
                    @foreach ($model->getAcTypeOptions() as $key => $acType)
                        <option value="{{ $key }}"
                            {{ old('ac_type', $model->ac_type) == $key ? 'selected' : '' }}>
                            {{ $acType }}
                        </option>
                    @endforeach
                </select>
            </div>
            @error('ac_type')
                <p style="color:red;">{{ $errors->first('ac_type') }}</p>
            @enderror
        </div>
        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Select Meal </label>
                <select name="meal_type" class="validate form-control" id="meal_type">
                    @foreach ($model->getMealTypeOptions() as $key => $meal)
                        <option value="{{ $key }}"
                            {{ old('meal_type', $model->meal_type) == $key ? 'selected' : '' }}>
                            {{ $meal }}
                        </option>
                    @endforeach
                </select>
            </div>
            @error('meal_type')
                <p style="color:red;">{{ $errors->first('meal_type') }}</p>
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
            @error('category_id')
                <p style="color:red;">{{ $errors->first('category_id') }}</p>
            @enderror
        </div>

        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Price </label>
                <input type="text" class="form-control d-block" name="price"
                    value="{{ old('price', $model->price) }}">
            </div>
            @error('price')
                <p style="color:red;">{{ $errors->first('price') }}</p>
            @enderror
        </div>


        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Capacity </label>
                <input type="text" class="form-control d-block" name="capacity"
                    value="{{ old('capacity', $model->capacity) }}">
            </div>
            @error('capacity')
                <p style="color:red;">{{ $errors->first('capacity') }}</p>
            @enderror
        </div>

        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 ">
                <label class="pt-2 fw-bold" for="btncheck1"> Note </label>
                <textarea class="form-control d-block" rows="1" name="note"></textarea>
            </div>
            @error('price')
                <p style="color:red;">{{ $errors->first('price') }}</p>
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
