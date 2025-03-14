@extends('layouts.master')
@section('title', 'Kyc View')
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
            'url' => 'kyc',
            'label' => 'Kyc',
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

                    <x-a-detail-view :model="$model" :type="'double'" :column="
    [
    'id',
      'name',
      'email',
      'contact_number',
      'national_id',
          [
             'attribute' => 'type_id',
             'label' => 'National type',
             'value' => $model->gettype(),
             'visible' => true,
         ],
      [
        'attribute' => 'updated_at',
        'label' => 'Updated at',
        'value' => (empty($model->updated_at)) ? 'N/A' : date('Y-m-d h:i:s A', strtotime($model->updated_at)),
        'visible'=> true   
     ],
      [
        'attribute' => 'created_at',
        'label' => 'Created at',
        'value' => (empty($model->created_at)) ? 'N/A' : date('Y-m-d h:i:s A', strtotime($model->created_at)),
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
            <div class="col-lg-12 mt-5">
                <div class="card">
                    <div class="row">
                        <div class="col-lg-4">

                            <div class="card-header">
                                <h3>Selfie Images</h3>
                            </div>
                            <div class="card-body">
                                <img src="{{ asset('uploads/'.$model->selfie_image) }}" alt="" srcset="" width="80px" height="100px">
                            </div>
                        </div>

                        <div class="col-lg-4">

                            <div class="card-header">
                                <h3>Front Images</h3>
                            </div>
                            <div class="card-body">
                                <img src="{{ asset('uploads/'.$model->front_image) }}" alt="" srcset="" width="80px" height="100px">
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card-header">
                                <h3>Back Images</h3>
                            </div>
                            <div class="card-body">
                                <img src="{{ asset('uploads/'.$model->back_image) }}" alt="" srcset="" width="80px" height="100px">
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card-header">
                                <h3>Short Video</h3>
                            </div>
                            <div class="card-body">
                                <video controls width="320" height="240">
                                    <source src="{{ asset('uploads/'.$model->video) }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(User::isAdmin())
    <x-a-user-action :model="$model" attribute="state_id" :states="$model->getStateOptions()" />
    @endif
</div>
@endsection