@extends('layouts.master')
@section('title', 'User View')
@section('content')

<style type="text/css">
    .grid-image {
        width: 183px;
        height: 119px;
    }
</style>
<?php

use App\Models\User;
?>
<x-a-breadcrumb :columns="[
        [
            'url' => '/',
            'label' => 'Home',
        ],
        [
            'url' => 'user',
            'label' => 'Users',
        ],
        $model->name,
    ]" />

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-body">
                    <h5>{{ !empty($model->name) ? (strlen($model->name) > 100 ? substr($model->name, 0, 100) . '...' : $model->name) : 'N/A' }}
                        <span class="{{ $model->getStateBadgeOption() }}">{{ $model->getState() }}</span>
                    </h5>
                    <div class="row">

                        <div class="col-md-3 col-lg-2">
                            <div class="admin-blog-image mt-5">

                                <img src="{{ asset($model->profile_image ? '/uploads/' . $model->profile_image : '/assets/img/avatars/8.png') }}" alt="Profile" class="grid-image">

                            </div>
                        </div>
                        <div class="col-md-9 col-lg-10">
                            <x-a-detail-view :model="$model" :type="'double'" :column="[
                                    'id',
                                    'email',
                                    'name',
                                    'father_name',
                                    'contact_no',
                                    'address',
                                    [
                                        'attribute' => 'role_id',
                                        'label' => 'Role',
                                        'value' => $model->getRole(),
                                        'visible' => true,
                                    ],
                                    
                                      [
                                        'attribute' => 'Pending_amount',
                                        'value' => number_format($model->pendingPayment(),2),
                                        'visible' => true,
                                    ],
                                    [
                                        'attribute' => 'email_verified',
                                        'label' => 'Email Verified',
                                        'value' => $model->getEmail(),
                                        'visible' => false,
                                    ],
                                    [
                                        'attribute' => 'created_at',
                                        'label' => 'Created at',
                                        'value' => empty($model->created_at)
                                            ? 'N/A'
                                            : date('Y-m-d h:i:s A', strtotime($model->created_at)),
                                    ],
                                    [
                                        'attribute' => 'updated_at',
                                        'label' => 'Updated at',
                                        'value' => empty($model->updated_at)
                                            ? 'N/A'
                                            : date('Y-m-d h:i:s A', strtotime($model->updated_at)),
                                        'visible' => false,
                                    ],
                                
                                    [
                                        'attribute' => 'created_by_id',
                                        'label' => 'Created By',
                                        'value' => !empty($model->createdBy && $model->createdBy->name)
                                            ? $model->createdBy->name
                                            : 'N/A',
                                        'visible' => false,
                                    ],
                                ]" />
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    @if ($model->role_id != User::ROLE_ADMIN && $model->id != Auth::id())
    <x-a-user-action :model="$model" attribute="state_id" :states="$model->getStateOptions()" />
    @endif


    <!-- <div class="card m"> -->
    <div class="row mt-4">

        <div class="col-xl-12">
            <div class="nav-align-top ">
                <ul class="nav nav-tabs nav-fill" role="tablist">

                    <li class="nav-item">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-order" aria-controls="navs-justified-messages" aria-selected="false">
                            <i class="tf-icons bx bx-message-square"></i> Orders
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-booking" aria-controls="navs-justified-messages" aria-selected="false">
                            <i class="tf-icons bx bx-message-square"></i> Booking
                        </button>
                    </li>

                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-reservation" aria-controls="navs-justified-messages" aria-selected="false">
                            <i class="tf-icons bx bx-message-square"></i> Reservations
                        </button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane show active" id="navs-justified-order" role="tabpanel">
                        <div class="table-responsive">



                            <x-a-relation-grid :id="'order_table'" :relation="'orders'" :model="$model" :columns=" [
                            'id',
                            'order_number',
                            'total_amount',
                            'created_at',
                            'updated_at',
                            'status',
                            'payment_status',
                            'created_by',
                            'action',
                            ]" />




                        </div>
                    </div>

                    <div class="tab-pane fade" id="navs-justified-booking" role="tabpanel">
                        <div class="table-responsive">
                            <x-a-relation-grid :id="'booking_table'" :relation="'bookings'" :model="$model" :columns="['id','user','room', 'check_in', 'check_out','total_price','is_paid', 'status', 'action']" />

                        </div>


                    </div>

                    <div class="tab-pane fade" id="navs-justified-reservation" role="tabpanel">
                        <div class="table-responsive">
                            <x-a-relation-grid :id="'reservation_table'" :relation="'reservations'" :model="$model" :columns="['id','user','table', 'reservation_time','status', 'action']" />

                        </div>


                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection