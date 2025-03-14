@extends('layouts.master')
@section('title', 'Room Index')

@section('content')



    <x-a-breadcrumb :columns="[
        [
            'url' => '/',
            'label' => 'Home',
        ],
        [
            'url' => 'room',
            'label' => 'Rooms',
        ],
    ]" />

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">

            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <h5 class="card-header">{{ __('Index') }}</h5>
                    <div class="card-body">
                        <x-a-update-menu-items :model="$model" :action="'index'" />
                        <div class="table-responsive">
                            <x-a-grid-view :id="'product_table'" :model="$model" :url="'room/get-list/'" :columns="[
                                'id',
                                'room_number',
                                'capacity',
                                'price',
                                'type',
                                'ac_type',
                                'meal',
                                'status',
                                'action',
                            ]" />
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
