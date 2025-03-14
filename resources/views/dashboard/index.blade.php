@extends('layouts.master')

@section('title', 'Admin Dashboard')
@section('content')
    <script src="https://code.highcharts.com/highcharts.js"></script>

    <?php
    
    use App\Models\WalletTransaction;
    use App\Models\SubscribedPlan;
    use App\Models\User;
    ?>

    <!-- Content -->
    <x-a-breadcrumb :columns="[
        [
            'url' => '/',
            'label' => 'Home',
        ],
        'Dashboard',
    ]" />
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">




            <div class="col-lg-12 col-md-12 order-1">
                <div class="row">

                    <div class="col-lg-3 col-md-12 col-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ url('/assets/img/icons/unicons/wallet-info.png') }}" alt="Credit Card"
                                            class="rounded" />
                                    </div>

                                </div>
                                <span>Total Booking</span>
                                <h3 class="card-title text-nowrap mb-1">
                                    ${{ number_format((new App\Models\User())->profitSalesTransactions('sales')) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-12 col-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ url('/assets/img/icons/unicons/wallet-info.png') }}" alt="Credit Card"
                                            class="rounded" />
                                    </div>

                                </div>
                                <span>Total Customer</span>
                                <h3 class="card-title text-nowrap mb-1">
                                    ${{ number_format((new App\Models\User())->profitSalesTransactions('sales')) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-12 col-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ url('/assets/img/icons/unicons/wallet-info.png') }}" alt="Credit Card"
                                            class="rounded" />
                                    </div>

                                </div>
                                <span>Total Open Room</span>
                                <h3 class="card-title text-nowrap mb-1">
                                    ${{ number_format((new App\Models\User())->profitSalesTransactions('sales')) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-12 col-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ url('/assets/img/icons/unicons/wallet-info.png') }}" alt="Credit Card"
                                            class="rounded" />
                                    </div>

                                </div>
                                <span>Total Booked Room</span>
                                <h3 class="card-title text-nowrap mb-1">
                                    ${{ number_format((new App\Models\User())->profitSalesTransactions('sales')) }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-12 col-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ url('/assets/img/icons/unicons/wallet-info.png') }}" alt="Credit Card"
                                            class="rounded" />
                                    </div>

                                </div>
                                <span>Total Incative Room</span>
                                <h3 class="card-title text-nowrap mb-1">
                                    ${{ number_format((new App\Models\User())->profitSalesTransactions('sales')) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-12 col-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ url('/assets/img/icons/unicons/wallet-info.png') }}" alt="Credit Card"
                                            class="rounded" />
                                    </div>

                                </div>
                                <span>Total Available Table</span>
                                <h3 class="card-title text-nowrap mb-1">
                                    ${{ number_format((new App\Models\User())->profitSalesTransactions('sales')) }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-12 col-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ url('/assets/img/icons/unicons/wallet-info.png') }}" alt="Credit Card"
                                            class="rounded" />
                                    </div>

                                </div>
                                <span>Total Booked Table</span>
                                <h3 class="card-title text-nowrap mb-1">
                                    ${{ number_format((new App\Models\User())->profitSalesTransactions('sales')) }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-12 col-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ url('/assets/img/icons/unicons/wallet-info.png') }}" alt="Credit Card"
                                            class="rounded" />
                                    </div>

                                </div>
                                <span>Total Booked Room</span>
                                <h3 class="card-title text-nowrap mb-1">
                                    ${{ number_format((new App\Models\User())->profitSalesTransactions('sales')) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                @if (User::isUser())
                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img src="{{ url('/assets/img/icons/unicons/wallet-info.png') }}" alt="Credit Card"
                                            class="rounded" />
                                    </div>

                                </div>
                                <span>Total Investment</span>
                                <h3 class="card-title text-nowrap mb-1">${{ Auth::user()->getTotalSubscribedPlanAmount() }}
                                </h3>
                            </div>
                        </div>
                    </div>
            </div>
            @endif
        </div>
        <!-- Total Revenue -->
       
    </div>






    @php
        $transactions = Auth::user()->transactions()->latest()->paginate(4);
    @endphp
    <div class="col-md-12 mt-2">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <h5 class="card-header">{{ __('Current Booking') }}</h5>
                <div class="card-body">
                    <div class="table-responsive">
                        <x-a-grid-view :id="'booking_table'" :model="''" :url="'order/get-list/'" :columns="['id', 'order_number', 'total_amount', 'created_at', 'created_by', 'action']" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 mt-2">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <h5 class="card-header">{{ __('Orders') }}</h5>
                <div class="card-body">
                    <div class="table-responsive">
                        <x-a-grid-view :id="'order_table'" :model="''" :url="'order/get-list/'" :columns="['id', 'order_number', 'total_amount', 'created_at', 'created_by', 'action']" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- / Content -->

    <!-- Footer -->


    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        function fetchTransactions(page) {
            $.ajax({
                url: "{{ url('/transactions?page=') }}" + page,
                success: function(data) {
                    $('#transactions').html(data);
                }
            });
        }

        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            let page = $(this).attr('href').split('page=')[1];
            fetchTransactions(page);
        });
    </script>

    <script>
        function fetchData(dataType) {
            $.ajax({
                url: '{{ route('order.totatSale') }}',
                method: 'GET',
                data: {
                    type: dataType
                },
                success: function(response) {
                    renderLineChart(response);
                    renderPieChart(response);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        fetchData('dailyData');


        function renderLineChart(data) {
            Highcharts.chart('salesChart', {
                title: {
                    text: 'Sales Report'
                },
                xAxis: {
                    type: 'datetime',
                    title: {
                        text: 'Date'
                    }
                },
                yAxis: {
                    title: {
                        text: 'Total Sales'
                    }
                },
                series: [{
                    name: 'Sales',
                    data: data.map(function(item) {
                        return [Date.parse(item.date), item.totalSales];
                    })
                }]
            });

        }

        function renderPieChart(data) {
            var totalSales = data.reduce((acc, cur) => acc + cur.totalSales, 0);
            var pieData = data.map(item => ({
                name: item.date,
                y: item.totalSales
            }));

            Highcharts.chart('salesPieChart', {
                title: {
                    text: 'Sales Distribution'
                },
                series: [{
                    type: 'pie',
                    data: pieData
                }]
            });


        }
    </script>

@stop
