@extends('layouts.master')

@section('title', 'user')
@section('content')

<!-- push external head elements to head -->
@push('head')
<style>


</style>
<!-- <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"> -->
<link rel="stylesheet" href="{{ asset('public/admin/assets/css/datatables.min.css') }}">
@endpush

<main id="main" class="main">

    <div class="pagetitle">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/')}}">Home</a></li>
                <li class="breadcrumb-item">Notifications</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->


    @include('include.message')

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                <div class="card-header">
                        <h3>{{ __('Index') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">

                            <table id="email_queues" class="table cell-border table-striped table-bordered mt-2">
                                <thead>
                                    <tr>

                                        <th>{{ __('id') }}</th>
                                        <th>{{ __('Title') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th>{{ __('Is Read') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Created On') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <!-- End Table with stripped rows -->
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

</main><!-- End #main -->

@push('script')

<!--server side customers table script-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="{{ asset('public/admin/assets/js/datatables.min.js') }}"></script>
<script src="{{ Module :: asset ('notification:js/notification.js') }}"></script>

@endpush

@stop