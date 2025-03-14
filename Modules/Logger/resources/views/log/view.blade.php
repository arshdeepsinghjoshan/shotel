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
            'url' => 'log',
            'label' => 'Logs',
        ],
       !empty($model->message ) ? (strlen($model->message ) > 100 ? substr($model->message , 0, 100) . '...' : $model->message ) : 'N/A' ,
    ]" />

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-body">
                    <h5>{{ !empty($model->message ) ? (strlen($model->message ) > 100 ? substr($model->message , 0, 100) . '...' : $model->message ) : 'N/A' }}
                        <span class="{{ $model->getStateBadgeOption() }}">{{ $model->getState() }}</span>
                    </h5>
                    <div class="row">

                        <div class="col-md-12 col-lg-12">
                            <x-a-detail-view :model="$model" :type="'single'" :column="[
                                    'id',
                                    'message',
                                    'user_agent',
                                    'level_name',
                                    'link',
                                    'channel',
                                
                                    [
                                        'attribute' => 'updated_at',
                                        'label' => 'Updated at',
                                        'value' => empty($model->updated_at)
                                            ? 'N/A'
                                            : date('Y-m-d h:i:s A', strtotime($model->updated_at)),
                                        'visible' => true,
                                    ],
                                    [
                                        'attribute' => 'created_at',
                                        'label' => 'Created at',
                                        'value' => empty($model->created_at)
                                            ? 'N/A'
                                            : date('Y-m-d h:i:s A', strtotime($model->created_at)),
                                    ],
                                    [
                                        'attribute' => 'created_by_id',
                                        'label' => 'Created By',
                                        'value' => !empty($model->createdBy && $model->createdBy->name)
                                            ? $model->createdBy->name
                                            : 'N/A',
                                    ],
                                ]" />
                                 <h5 class="mt-2">Context</h5>
                                <p>{{ !empty($model->context) ? $model->context : 'N/A' }}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>



</div>
@endsection