@extends('layouts.master')
@section('title', 'Accounts')

@section('content')



<x-a-breadcrumb :columns="[
        [
            'url' => '/',
            'label' => 'Home',
        ],
        [
            'url' => 'email-queue/account',
            'label' => 'Accounts',
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
                        <x-a-grid-view :id="'accounts_table'" :model="$model" :url="'smtp-accout/get-list'" :columns="[
                                'id',
                                'mailer',
                                'host',
                                'port',
                                'username',
                                'encryption',
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