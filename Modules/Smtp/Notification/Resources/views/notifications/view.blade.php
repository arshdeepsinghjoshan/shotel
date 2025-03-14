@extends('layouts.master')

@section('title', 'user')
@section('content')

<?php

use App\Models\User;
use Modules\Notification\Http\Models\Notification;

// use App\Models\Notification;

?>
<!-- push external head elements to head -->
@push('head')


<link rel="stylesheet" href="{{ asset('public/admin/assets/css/datatables.min.css') }}">
@endpush

<main id="main" class="main">

    <div class="pagetitle">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('/notification')}}">Notifications</a></li>
                <li class="breadcrumb-item">{{$notification->title}}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <div class="card">
        <div class="account-view">
            <div class="page-head">
                <h1>{{$notification->title}}</h1>
                <div class="head-content">
                    <span class="badge badge-{{$notification->state_id}}">{{ $notification->getState()}}</span>
                </div>
            </div>
        </div>
    </div>
    @include('include.message')

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <div class="mb-2">

                            <div class="text-right mb-2">
                                <a class="btn btn-primary " href="{{ url('/notification')}}"><i class="ri-arrow-go-back-fill"></i></a>
                                <a class="btn btn-danger custom-delete" href="{{ url('notification/delete/'.$notification->id)}}"><i class="ri-delete-bin-5-fill" data-toggle="tooltip" title="Delete"></i></a>
                            </div>
                        </div>
                        <div class="table-responsive">

                        <table id="email-queue-detail-view" class="table table-striped table-bordered detail-view">
                            <tbody>
                                <tr>
                                    <th>ID</th>
                                    <td colspan="1">{{!empty($notification->id) ? $notification->id : 'N/A' }}</td>
                                    <th>Title</th>
                                    <td colspan="1">{{!empty($notification->title) ? $notification->title : 'N/A'}}</td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td colspan="1">{{!empty($notification->description) ? $notification->description : 'N/A'}}</td>
                                    <th>User Email</th>
                                    <td colspan="1">{{!empty($user->email) ? $user->email : 'N/A'}}</td>
                                </tr>
                                <tr>
                                    <th>User Name</th>
                                    <td colspan="1">{{!empty($user->full_name) ? $user->full_name : 'N/A'}}</td>
                                    <th>User State</th>
                                    <td colspan="1">{{!empty($user) ? $user->getState() : 'N/A'}}</td>
                                </tr>
                                <tr>
                                    <th>State</th>
                                    <td colspan="1">{{ !empty($user) ? $notification->getState() : 'N/A'}}</td>
                                    <th>Sent On</th>
                                    <td colspan="1">{{!empty($notification->created_at) ? $notification->created_at : 'N/A'}}</td>
                                </tr>
                                <tr>
                                    <th>Created On</th>
                                    <td colspan="1">{{!empty($notification->created_at) ? $notification->created_at : 'N/A'}}</td>
                                    <th>Model</th>
                                    <td colspan="1"></td>
                                </tr>
                                <tr>
                                    <th>Model Type</th>
                                    <td colspan="1"></td>
                                    <th>IS Read</th>
                                    <td colspan="1">{{ !empty($notification->is_read) ? $notification->getIsRead() : 'N/A'}}</td>
                                </tr>

                                <tr></tr>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

</main><!-- End #main -->



@stop