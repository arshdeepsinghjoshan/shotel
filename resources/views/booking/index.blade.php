@extends('layouts.master')
@section('title', 'Room Index')

@section('content')



    <x-a-breadcrumb :columns="[
        [
            'url' => '/',
            'label' => 'Home',
        ],
        [
            'url' => 'booking',
            'label' => 'Bookings',
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
                            <x-a-grid-view :id="'booking_table'" :model="$model" :url="'booking/get-list/'" :columns="['id', 'check_in', 'check_out','total_price','is_paid', 'status', 'action']" />
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
