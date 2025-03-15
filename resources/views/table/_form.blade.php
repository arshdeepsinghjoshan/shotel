<form action="{{ route(empty($model->exists) ? 'table.add' : 'table.update', $model->id) }}" method="post"
    id="table-update" enctype="multipart/form-data">
    @csrf
    <div class="row align-items-starts">
        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Table Number </label>
                <input type="text" class="form-control d-block" name="table_number"
                    value="{{ old('table_number', $model->table_number) }}">
            </div>
            @error('table_number')
                <p style="color:red;">{{ $errors->first('table_number') }}</p>
            @enderror
        </div>


        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Seats </label>
                <input type="text" class="form-control d-block" name="seats"
                    value="{{ old('seats', $model->seats) }}">
            </div>
            @error('seats')
                <p style="color:red;">{{ $errors->first('seats') }}</p>
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
