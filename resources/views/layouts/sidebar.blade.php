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


        @if (User::isAdmin())
        <!--Wallet Managment -->
        {{-- <li class="menu-item {{ $segment1 != 'wallet' ? '' : 'active open' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div data-i18n="Wallet Management">Wallet Management</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ $segment1 == 'wallet' && $segment2 != 'wallet-transaction' ? 'active' : '' }}">
                    <a href="{{ url('wallet') }}" class="menu-link">
                        <div data-i18n="Account">Wallets</div>
                    </a>
                </li>
            </ul>

            <ul class="menu-sub">
                <li class="menu-item {{ $segment1 == 'wallet' && $segment2 == 'wallet-transaction' ? 'active' : '' }}">
                    <a href="{{ url('wallet/wallet-transaction') }}" class="menu-link">
                        <div data-i18n="Account">Wallet Transactions</div>
                    </a>
                </li>
            </ul>
        </li> --}}
        <!--End Wallet Managment -->


        <li class="menu-item {{ $segment1 != 'email-queue' ? '' : 'active open' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
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


        <li class="menu-header small text-uppercase"><span class="menu-header-text">Customer Management</span></li>
        <li class="menu-item {{ $segment1 != 'user' ? '' : 'active' }}">
            <a href="{{ url('user') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Basic">Customer </div>
            </a>
        </li>



        <li class="menu-header small text-uppercase"><span class="menu-header-text">Product Management</span></li>
    
        <li class="menu-item {{ $segment1 == 'product' && $segment2 != 'category' ? 'active' : '' }}">
            <a href="{{ url('product') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Basic">Products</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase"><span class="menu-header-text">Order Management</span></li>
        @if (User::isAdmin())
        <!-- Order Managment -->
        <li class="menu-item {{ $segment1 == 'order' && $segment2 != 'category' ? 'active' : '' }}">
            <a href="{{ url('order') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Basic">Orders</div>
            </a>
        </li>
        @endif
        

        <!-- <li class="menu-header small text-uppercase"><span class="menu-header-text">Support Management</span></li> -->
        <!-- @if (User::isAdmin()) -->
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