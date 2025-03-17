@extends('layouts.master')

@section('title', 'Admin Dashboard')
@section('content')
<script src="https://code.highcharts.com/highcharts.js"></script>

<?php

use App\Models\WalletTransaction;
use App\Models\SubscribedPlan;
use App\Models\User;
?>

<!-- Content -->
<x-a-breadcrumb :columns="[
        [
            'url' => '/',
            'label' => 'Home',
        ],
        'Dashboard',
    ]" />

<style>
    .icons {
        color: #696cff;
        background-color: #696cff2e;
        padding: 7px;
        border-radius: 5px;
    }
</style>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">



        <div class="col-lg-12 col-md-12 order-1">
            <div class="row">

                <div class="col-lg-3 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="menu-icon tf-icons bx bx-dock-top icons"></i>
                                </div>

                            </div>
                            <span>Total Booking</span>
                            <h3 class="card-title text-nowrap mb-1">
                                {{ $data['total_bookings'] }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="menu-icon tf-icons bx bx-user icons"></i>

                                </div>

                            </div>
                            <span>Total Customer</span>
                            <h3 class="card-title text-nowrap mb-1">
                                {{ $data['total_customers'] }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="menu-icon tf-icons bx bx-dock-top icons"></i>
                                </div>

                            </div>
                            <span>Total Open Room</span>
                            <h3 class="card-title text-nowrap mb-1">
                                {{ $data['total_open_rooms'] }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="menu-icon tf-icons bx bx-dock-top icons"></i>

                                </div>

                            </div>
                            <span>Total Booked Room</span>
                            <h3 class="card-title text-nowrap mb-1">
                                {{ $data['total_booked_rooms'] }}
                            </h3>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="menu-icon tf-icons bx bx-dock-top icons"></i>

                                </div>

                            </div>
                            <span>Total Incative Room</span>
                            <h3 class="card-title text-nowrap mb-1">
                                {{ $data['total_inactive_rooms'] }}
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="menu-icon tf-icons bx bx-table icons"></i>

                                </div>

                            </div>
                            <span>Total Available Table</span>
                            <h3 class="card-title text-nowrap mb-1">
                                {{ $data['total_available_tables'] }}
                            </h3>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="menu-icon tf-icons bx bx-table icons"></i>

                                </div>

                            </div>
                            <span>Total Booked Table</span>
                            <h3 class="card-title text-nowrap mb-1">
                                {{ $data['total_booked_tables'] }}
                            </h3>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="menu-icon tf-icons bx bx-user icons"></i>
                                </div>

                            </div>
                            <span>Total Active Customer</span>
                            <h3 class="card-title text-nowrap mb-1">
                                {{ $data['total_active_customers'] }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            @if (User::isUser())
            <div class="col-lg-6 col-md-12 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ url('/assets/img/icons/unicons/wallet-info.png') }}" alt="Credit Card"
                                    class="rounded" />
                            </div>

                        </div>
                        <span>Total Investment</span>
                        <h3 class="card-title text-nowrap mb-1">${{ Auth::user()->getTotalSubscribedPlanAmount() }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    <!-- Total Revenue -->

</div>






@php
$transactions = Auth::user()->transactions()->latest()->paginate(4);
@endphp
<div class="col-md-12 mt-2">
    <div class="col-lg-12 mb-4 order-0">
        <div class="card">
            <h5 class="card-header">{{ __('Current Booking') }}</h5>
            <div class="card-body">
                <div class="table-responsive">
                    <x-a-grid-view :id="'booking_table'" :model="''" :url="url('booking/get-list/')" :columns="['id','user','room', 'check_in', 'check_out','total_price','is_paid', 'status', 'action']" />
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-12 mt-2">
    <div class="col-lg-12 mb-4 order-0">
        <div class="card">
            <h5 class="card-header">{{ __('Orders') }}</h5>
            <div class="card-body">
                <div class="table-responsive">
                    <x-a-grid-view :id="'order_table'" :model="''" :url="url('order/get-list/')" :columns="['id', 'order_number', 'total_amount', 'created_at', 'created_by', 'action']" />
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- / Content -->

<!-- Footer -->



@stop