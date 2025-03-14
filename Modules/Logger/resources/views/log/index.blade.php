@extends('layouts.master')
@section('title', 'User Index')
@section('content')
<x-a-breadcrumb :columns="[
        [
            'url' => '/',
            'label' => 'Home',
        ],
        [
             'url' => 'log',
            'label' => 'Logs',
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
                        <x-a-grid-view :id="'user_table'" :model="$model" :url="'log/list'" :columns="[
                                'id',
                                'message',
                                'level',
                                'channel',
                                'created_at',
                                'status',
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