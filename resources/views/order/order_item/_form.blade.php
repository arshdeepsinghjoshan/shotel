<form action="{{ route('product.add') }}" method="post" id="product-update" enctype="multipart/form-data">
    @csrf
    <div class="row align-items-starts">
        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Name </label>
                <input type="text" class="form-control d-block" name="name" value="{{ old('name', $model->name) }}">
            </div>
            @error("Title")
            <p style="color:red;">{{ $errors->first("name")}}</p>
            @enderror
        </div>

        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Category </label>
                <select name="category_id" class="validate form-control" id="category_id">
                    @foreach($model->getCategoryOption() as $category)
                    <option value="{{$category->id}}" {{$category->id == $model->category_id ? 'selected' : ''}}>{{$category->name}}</option>
                    @endforeach

                </select>
            </div>
            @error("category_id")
            <p style="color:red;">{{ $errors->first("category_id")}}</p>
            @enderror
        </div>
        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Price </label>
                <input type="text" class="form-control d-block" name="price" value="{{ old('price', $model->price) }}">
            </div>
            @error("price")
            <p style="color:red;">{{ $errors->first("price")}}</p>
            @enderror
        </div>
        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Quantity </label>
                <input type="text" class="form-control d-block" name="quantity_in_stock" value="{{ old('quantity_in_stock', $model->quantity_in_stock) }}">
            </div>
            @error("quantity_in_stock")
            <p style="color:red;">{{ $errors->first("quantity_in_stock")}}</p>
            @enderror
        </div>


        <input type="hidden" name="id" id="id" value="{{ $model->id }}" required />


        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1">Description</label>
                <textarea rows="1" name="description" id="description" class="validate form-control">{{$model->description}} </textarea>
            </div>
            @error("description")
            <p style="color:red;">{{ $errors->first("description")}}</p>
            @enderror
        </div>
        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> HSN Code </label>
                <input type="text" class="form-control d-block" name="hsn_code" value="{{ old('hsn_code', $model->hsn_code) }}">
            </div>
            @error("hsn_code")
            <p style="color:red;">{{ $errors->first("hsn_code")}}</p>
            @enderror
        </div>

        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Expiry Date </label>
                <input type="date" class="form-control d-block" name="expiry_date" value="{{ old('expiry_date', $model->expiry_date) }}">
            </div>
            @error("expiry_date")
            <p style="color:red;">{{ $errors->first("expiry_date")}}</p>
            @enderror
        </div>
        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Bill Date </label>
                <input type="date" class="form-control d-block" name="bill_date" value="{{ old('bill_date', $model->bill_date) }}">
            </div>
            @error("bill_date")
            <p style="color:red;">{{ $errors->first("bill_date")}}</p>
            @enderror
        </div>

        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Salt </label>
                <input type="text" class="form-control d-block" name="salt" value="{{ old('salt', $model->salt) }}">
            </div>
            @error("salt")
            <p style="color:red;">{{ $errors->first("salt")}}</p>
            @enderror
        </div>
        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> GST </label>
                <input type="text" class="form-control d-block" name="tax_id" value="{{ old('tax_id', $model->tax_id) }}">
            </div>
            @error("tax_id")
            <p style="color:red;">{{ $errors->first("tax_id")}}</p>
            @enderror
        </div>
        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Agency Name </label>
                <input type="text" class="form-control d-block" name="agency_name" value="{{ old('agency_name', $model->agency_name) }}">
            </div>
            @error("agency_name")
            <p style="color:red;">{{ $errors->first("agency_name")}}</p>
            @enderror
        </div>
        <div class="col-xl-4 col-lg-4 col-md-6 col-12">
            <div class="mb-3 required">
                <label class="pt-2 fw-bold" for="btncheck1"> Batch Number </label>
                <input type="text" class="form-control d-block" name="batch_no" value="{{ old('batch_no', $model->batch_no) }}">
            </div>
            @error("batch_no")
            <p style="color:red;">{{ $errors->first("batch_no")}}</p>
            @enderror
        </div>

        <div class="col-xl-4 col-lg-4 col-md-6 col-4">
            <div class="mb-6">
                <label for="message">Bill Images</label>
                <div class="input-group">
                    <input type="file" class="form-control ticket_images" name="images[]" multiple onchange="previewImages(event)">
                </div>
            </div>
            @error("image")
            <p style="color:red;">{{ $errors->first("image")}}</p>
            @enderror
        </div>
        <div class="preview-images"></div>
        <div class="col-lg-12">
            <div class="d-flex align-items-center justify-content-end">
                <div class="downoad-btns text-end my-4">
                    <button class="btn btn-primary text-white ms-2">@empty($model->exists) {{ __('Add') }} @else {{ __('Update') }} @endempty</button>

                </div>
            </div>
        </div>
    </div>
</form>