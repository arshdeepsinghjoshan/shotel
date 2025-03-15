@extends('layouts.master')
@section('title', 'Room Add')

@section('content')
    <x-a-breadcrumb :columns="[
        [
            'url' => '/',
            'label' => 'Home',
        ],
        [
            'url' => 'table',
            'label' => 'Tables',
        ],
    ]" />

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <div class="card-header">
                        <h3>
                            @empty($model->exists)
                                {{ __('Add') }}
                            @else
                                {{ __('Update') }}
                            @endempty
                        </h3>
                    </div>
                    <div class="card-body">
                        @include('table._form')
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
