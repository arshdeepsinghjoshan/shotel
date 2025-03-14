@extends('layouts.master')
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
            'url' => 'email-queue/',
            'label' => 'Email Queue',
        ],
        $model->subject,
    ]" />

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-body">
                    <h5>{{ !empty($model->subject) ? (strlen($model->subject) > 100 ? substr($model->subject, 0, 100) . '...' : $model->subject) : 'N/A' }}
                        <span class="{{ $model->getStateBadgeOption() }}">{{ $model->getState() }}</span>
                    </h5>

                    <x-a-detail-view :model="$model" :type="'double'" :column="[
                            'id',
                            'subject',
                            'from',
                            'to',
                            'cc',
                            'bcc',
                            'content',
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
                    <iframe frameborder="0" srcdoc="{{ $model->content }}" width="100%" height="500px"></iframe>

                </div>
            </div>
        </div>
    </div>


    <x-a-user-action :model="$model" attribute="state_id" :states="$model->getStateOptions()" />




</div>
@endsection