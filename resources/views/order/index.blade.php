@extends('layouts.master')
@section('title', 'Order Index')

@section('content')



<x-a-breadcrumb :columns="[
        [
            'url' => '/',
            'label' => 'Home',
        ],
      class_basename($model)
    ]" />



<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <h5 class="card-header">{{ __('Index') }}</h5>
                <div class="card-body">
                    <x-a-update-menu-items :model="$model" :action="'index'" />
                    <div class="table-responsive">
                        <x-a-grid-view :id="'order_table'" :model="$model" :url="'order/get-list/'" :columns="[
                                'id',
                                'order_number',
                                'total_amount',
                                'created_at', 
                                'status',
                                'payment_status',
                                'created_by',
                                'action',
                            ]" />
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection