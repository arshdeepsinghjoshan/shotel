@php
    use App\Models\User;
    $segment1 = request()->segment(1);
    $segment2 = request()->segment(2);
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ url('/') }}" class="app-brand-link">
            <span class=" demo menu-text fw-bolder ms-2">
                <h2> {{ ucfirst(env('APP_NAME')) }} </h2>
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ $segment1 != 'dashboard' ? '' : 'active open' }}">
            <a href="{{ url('/') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase"><span class="menu-header-text">Customer Management</span></li>
        <li class="menu-item {{ $segment1 != 'user' ? '' : 'active' }}">
            <a href="{{ url('user') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Basic">Customer </div>
            </a>
        </li>


        {{-- 

        <li class="menu-header small text-uppercase"><span class="menu-header-text">Order Management</span></li>
        @if (User::isAdmin())
            <!-- Order Managment -->
            <li class="menu-item {{ $segment1 == 'order' && $segment2 != 'category' ? 'active' : '' }}">
                <a href="{{ url('order') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-collection"></i>
                    <div data-i18n="Basic">Orders</div>
                </a>
            </li>
        @endif --}}




        @if (User::isAdmin())
            <li class="menu-item {{ $segment1 != 'email-queue' ? '' : 'active open' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-box"></i>
                    <div data-i18n="Outgoing Emails">Outgoing Emails</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ $segment1 == 'email-queue' && $segment2 == 'account' ? 'active' : '' }}">
                        <a href="{{ url('email-queue/account') }}" class="menu-link">
                            <div data-i18n="Account">Accounts</div>
                        </a>
                    </li>
                </ul>

                <ul class="menu-sub">
                    <li class="menu-item {{ $segment1 == 'email-queue' && $segment2 != 'account' ? 'active' : '' }}">
                        <a href="{{ url('email-queue') }}" class="menu-link">
                            <div data-i18n="Account">Emails In Queue</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endif


        <li class="menu-item {{ $segment1 != 'room' ? '' : 'active open' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div data-i18n="Room Management">Room Management</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ $segment1 == 'room' ? 'active' : '' }}">
                    <a href="{{ url('rooms') }}" class="menu-link">
                        <div data-i18n="Account">Rooms</div>
                    </a>
                </li>
            </ul>

        </li>

        <li class="menu-item {{ $segment1 != 'product' ? '' : 'active open' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bxl-product-hunt"></i>
                <div data-i18n="Product Management">Product Manage..</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ $segment1 == 'product' ? 'active' : '' }}">
                    <a href="{{ url('product') }}" class="menu-link">
                        <div data-i18n="Account">Products</div>
                    </a>
                </li>
            </ul>

        </li>
        <li class="menu-item {{ $segment1 != 'order' ? '' : 'active open' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div data-i18n="Order Management">Order Manage..</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ $segment1 == 'order' ? 'active' : '' }}">
                    <a href="{{ url('order') }}" class="menu-link">
                        <div data-i18n="Account">Orders</div>
                    </a>
                </li>
            </ul>

        </li>


        <li class="menu-item {{ $segment1 != 'table' ? '' : 'active open' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-table"></i>
                <div data-i18n="Outgoing Emails">Table Management</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ $segment1 == 'table' ? 'active' : '' }}">
                    <a href="{{ url('table') }}" class="menu-link">
                        <div data-i18n="Account">Tables</div>
                    </a>
                </li>
            </ul>

        </li>



        <li class="menu-item {{ $segment1 != 'booking' ? '' : 'active open' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bxs-book-content"></i>
                <div data-i18n="Booking Management">Booking Manage..</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ $segment1 == 'booking' ? 'active' : '' }}">
                    <a href="{{ url('booking/account') }}" class="menu-link">
                        <div data-i18n="Account">Bookings</div>
                    </a>
                </li>
            </ul>


        </li>

        <li class="menu-item {{ $segment1 != 'reservation' ? '' : 'active open' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-restaurant"></i>
                <div data-i18n="Reservation Mange..">Reservation Mange..</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ $segment1 == 'reservation' ? 'active' : '' }}">
                    <a href="{{ url('reservation') }}" class="menu-link">
                        <div data-i18n="Account">Reservations</div>
                    </a>
                </li>
            </ul>

        </li>





        <!-- <li class="menu-header small text-uppercase"><span class="menu-header-text">Support Management</span></li> -->
        <!-- @if (User::isAdmin())
-->
        <!-- Support Managment -->
        <!-- <li class="menu-item {{ $segment1 == 'support' && $segment2 == 'department' ? 'active' : '' }}">
            <a href="{{ url('support/department') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Department</div>
            </a>
        </li>
@endif
        <li class="menu-item {{ $segment1 == 'support' && $segment2 != 'department' ? 'active' : '' }}">
            <a href="{{ url('support') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Basic">Supports</div>
            </a>
        </li> -->

        <!--End Support Managment -->



    </ul>
</aside>
