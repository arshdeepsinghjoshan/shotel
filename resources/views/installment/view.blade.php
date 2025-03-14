@extends('layouts.master')
@section('title', $model->order_number ?? 'Order View')

@section('content')
<?php

use App\Models\User;
?>
<x-a-breadcrumb :columns="[
        [
            'url' => '/',
            'label' => 'Home',
        ],
        [
          'url' => 'installment',
            'label' => 'Installments',
        ],
        $model->order->order_number,
    ]" />

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-body">
                    <h5>{{ !empty($model->amount) ? (strlen($model->amount) > 100 ? substr($model->amount, 0, 100) . '...' : $model->amount) : 'N/A' }}
                        <span class="{{ $model->getStateBadgeOption() }}">{{ $model->getState() }}</span>
                    </h5>

                    <x-a-detail-view :model="$model" :column="
    [
    'id',
       [
        'attribute' => 'order_number',
        'value' =>   $model->order->order_number,
        'visible'=> true   
     ],
         [
        'attribute' => 'amount',
        'value' =>  number_format($model->amount, 2),
        'visible'=> true   
     ],

     
      [
        'attribute' => 'created_at',
        'label' => 'Created at',
        'value' => (empty($model->created_at)) ? 'N/A' : date('Y-m-d h:i:s A', strtotime($model->created_at)),
     ],
     
     
      [
        'attribute' => 'updated_at',
        'label' => 'Updated at',
        'value' => (empty($model->updated_at)) ? 'N/A' : date('Y-m-d h:i:s A', strtotime($model->updated_at)),
        'visible'=> true   
     ],
     [
        'attribute' => 'created_by_id',
        'label' => 'Created By',
        'value' => !empty($model->createdBy && $model->createdBy->name) ? $model->createdBy->name : 'N/A',
     ],
    ]
    " />
                </div>
            </div>
        </div>
    </div>
    <!-- <x-a-user-action :model="$model" attribute="state_id" :states="$model->getStateOptions()" :title="'Order'" /> -->






</div>

@endsection