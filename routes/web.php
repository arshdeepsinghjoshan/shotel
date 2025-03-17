<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SubscribedPlanController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\SupportDepartmentController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\WalletTransactionController;
use App\Models\ProductCategory;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', [SiteController::class, 'index']);
Route::get('/', [AuthController::class, 'login'])->name('login');
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::get('forget-password', [UserController::class, 'forgetPassword']);
Route::post('forget-password', [UserController::class, 'forgetPasswordCheck'])->name('forget.password');
Route::post('/login/authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/registration', [AuthController::class, 'registration'])->name('add.registration');
Route::get('/user/confirm-email/{activation_key?}', [UserController::class, 'confirmEmail']);
Route::post('/user/confirm-email/{activation_key}', [UserController::class, 'EmailConfirm'])->name('confirm.email');
Route::group(['middleware' => ['prevent-back-history']], function () {
    Route::get('/logout', [UserController::class, 'logout'])->name('logout');
});
Route::group(['middleware' => ['prevent-back-history', 'admin']], function () {

    Route::get('/user/list', [UserController::class, 'list']);

    Route::group(['middleware' => ['auth', 'active']], function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/user/change-password', [UserController::class, 'changePassword']);
        Route::post('/user/update-password', [UserController::class, 'updatePassword'])->name('password.update');;

        Route::get('user/create', [UserController::class, 'create']);
        Route::get('user/{role_id?}', [UserController::class, 'index']);
        Route::post('user/add', [UserController::class, 'add'])->name('user.add');
        Route::get('/user/get-list/{id?}', [UserController::class, 'getUserList']);
        Route::get('/users/get-wallet-transaction/{id?}', [UserController::class, 'getWalletTransaction']);
        Route::get('/users/get-wallet/{id?}', [UserController::class, 'getWallet']);
        Route::get('/user/edit/{id}', [UserController::class, 'edit']);
        Route::get('/user/view/{id}', [UserController::class, 'view']);
        Route::post('user/update/{id}', [UserController::class, 'update'])->name('user.update');
        Route::post('state-change', [UserController::class, 'stateChange']);
        Route::post('/admin-serach', [UserController::class, 'search'])->name('admin.serach');
        Route::get('/serach-user/{id}', [UserController::class, 'searchUser'])->name('serach.user');
        Route::get('/user-login/{id}', [UserController::class, 'userLogin']);





        Route::get('wallet/create', [WalletController::class, 'create']);
        Route::get('wallet/', [WalletController::class, 'index']);
        Route::post('wallet/add', [WalletController::class, 'add'])->name('wallet.add');
        Route::get('/wallet/get-list/{role_id?}', [WalletController::class, 'getWalletList']);
        Route::get('/wallet/edit/{id}', [WalletController::class, 'edit']);
        Route::get('/wallet/view/{id}', [WalletController::class, 'view']);
        Route::post('wallet/update/{id}', [WalletController::class, 'update'])->name('wallet.update');
        Route::get('relation/get-list', [UserController::class, 'getrelationData']);


        Route::get('wallet/wallet-transaction', [WalletTransactionController::class, 'index']);
        Route::get('/wallet/wallet-transaction/get-list/{id?}', [WalletTransactionController::class, 'getWalletTransactionList']);
        Route::get('/wallet/wallet-transaction/view/{id}', [WalletTransactionController::class, 'view']);


        Route::get('subscription/plan/create', [SubscriptionPlanController::class, 'create']);
        Route::get('subscription/plan/', [SubscriptionPlanController::class, 'index']);
        Route::post('subscription/plan/add', [SubscriptionPlanController::class, 'add'])->name('subscriptionPlan.add');
        Route::get('/subscription/plan/get-list/{role_id?}', [SubscriptionPlanController::class, 'getSubscriptionPlanList']);
        Route::get('/subscription/plan/edit/{id}', [SubscriptionPlanController::class, 'edit']);
        Route::get('/subscription/plan/view/{id}', [SubscriptionPlanController::class, 'view']);
        Route::post('subscription/plan/update/{id}', [SubscriptionPlanController::class, 'update'])->name('subscriptionPlan.update');

        Route::get('subscription/subscribed-plan/', [SubscribedPlanController::class, 'index']);
        Route::get('/subscription/subscribed-plan/get-list/{id?}', [SubscribedPlanController::class, 'getSubscribedPlanList']);
        Route::get('/subscription/subscribed-plan/view/{id}', [SubscribedPlanController::class, 'view']);
        Route::get('subscription/subscribed-plan/{id}', [SubscribedPlanController::class, 'add']);
        Route::get('subscription/testing/', [SubscribedPlanController::class, 'testing']);

        Route::get('/subscription/totat-sale', [SubscribedPlanController::class, 'getSalesData'])->name('subscribed.totatSale');
        Route::get('/wallet/fetch-transaction', [WalletTransactionController::class, 'fetchTransaction'])->name('wallet.fetchTransaction');
        Route::get('/transactions', [WalletTransactionController::class, 'getTransactions'])->name('transactions.get');




        Route::get('support', [SupportController::class, 'index']);
        Route::get('support/create', [SupportController::class, 'create']);
        Route::post('support/add', [SupportController::class, 'add'])->name('support.add');
        Route::get('/support/get-list', [SupportController::class, 'getSupportList']);
        Route::get('/support/edit/{id}', [SupportController::class, 'edit']);
        Route::get('/support/view/{id}', [SupportController::class, 'view']);
        Route::post('support/update', [SupportController::class, 'update'])->name('support.update');


        Route::get('support/department', [SupportDepartmentController::class, 'index']);
        Route::post('support/department/add', [SupportDepartmentController::class, 'store'])->name('supportDepartment.add');
        Route::get('/support/department/get-list', [SupportDepartmentController::class, 'getDepartmenttList']);
        Route::get('/support/department/edit/{id}', [SupportDepartmentController::class, 'edit']);
        Route::get('/support/department/view/{id}', [SupportDepartmentController::class, 'view']);
        Route::post('support/department/update/{id}', [SupportDepartmentController::class, 'update'])->name('supportDepartment.update');
        Route::get('/support/department/stateChange/{id}/{state_id}', [SupportDepartmentController::class, 'stateChange']);
        Route::get('/support/department/delete/{id}', [SupportDepartmentController::class, 'finalDelete']);




        Route::get('product/category', [ProductCategoryController::class, 'index']);
        Route::post('product/category/add', [ProductCategoryController::class, 'store'])->name('productCategory.add');
        Route::get('/product/category/get-list', [ProductCategoryController::class, 'getDepartmenttList']);
        Route::get('/product/category/edit/{id}', [ProductCategoryController::class, 'edit']);
        Route::get('/product/category/view/{id}', [ProductCategoryController::class, 'view']);
        Route::post('product/category/update/{id}', [ProductCategoryController::class, 'update'])->name('productCategory.update');
        Route::get('/product/category/stateChange/{id}/{state_id}', [ProductCategoryController::class, 'stateChange']);
        Route::get('/product/category/delete/{id}', [ProductCategoryController::class, 'finalDelete']);


        Route::get('product', [ProductController::class, 'index']);
        Route::get('product/create', [ProductController::class, 'create']);
        Route::post('product/import', [ProductController::class, 'import'])->name('product.import');
        Route::post('product/add', [ProductController::class, 'add'])->name('product.add');
        Route::get('/product/get-list', [ProductController::class, 'getList']);
        Route::get('/product/edit/{id}', [ProductController::class, 'edit']);
        Route::get('/product/view/{id}', [ProductController::class, 'view']);
        Route::post('product/update', [ProductController::class, 'update'])->name('product.update');



        Route::get('order', [OrderController::class, 'index'])->name('order');
        Route::get('order/create', [OrderController::class, 'create']);
        Route::post('order/import', [OrderController::class, 'import'])->name('order.import');
        Route::post('order/add', [OrderController::class, 'add'])->name('order.add');
        Route::get('/order/get-list', [OrderController::class, 'getList']);
        Route::get('/order/edit/{id}', [OrderController::class, 'edit']);
        Route::get('/order/download/{id}', [OrderController::class, 'orderInvoice']);
        Route::get('/order/totat-sale', [OrderController::class, 'getSalesData'])->name('order.totatSale');
        Route::get('/order/generate-pdf/{id}', [OrderController::class, 'generatePDF']);

        Route::get('/order/view/{id}', [OrderController::class, 'view']);
        Route::post('order/update', [OrderController::class, 'update'])->name('order.update');

        Route::get('order/item', [OrderItemController::class, 'index']);
        Route::get('order/item/create', [OrderItemController::class, 'create']);
        Route::get('/order/item/get-list', [OrderItemController::class, 'getList']);
        Route::get('/order/item/view/{id}', [OrderItemController::class, 'view']);


        Route::get('cart', [CartController::class, 'index']);
        Route::get('cart/create', [CartController::class, 'create']);
        Route::post('cart/add', [CartController::class, 'add'])->name('cart.add');
        Route::post('cart/change-quantity', [CartController::class, 'changeQuantity'])->name('cart.change_quantity');
        Route::post('cart/update-quantity', [CartController::class, 'updateQuantity'])->name('cart.update_quantity');
        Route::get('/cart/get-list/{id?}', [CartController::class, 'getList']);
        Route::get('/cart/get-list-checkout', [CartController::class, 'getListCheckout']);
        Route::get('/cart/edit/{id}', [CartController::class, 'edit']);
        Route::get('/cart/view/{id}', [CartController::class, 'view']);
        Route::post('cart/update', [CartController::class, 'update'])->name('cart.update');
        Route::post('cart/delete-cart-item', [CartController::class, 'deleteCartItem']);
        Route::post('cart/update-grind-price', [CartController::class, 'updateGrindPrice']);

        Route::post('cart/custom-product', [CartController::class, 'customProduct']);

        Route::post('installment/store', [InstallmentController::class, 'store']);
        Route::get('installment', [InstallmentController::class, 'index']);
        Route::get('installment/create', [InstallmentController::class, 'create']);
        Route::get('/installment/get-list', [InstallmentController::class, 'getList']);
        Route::get('/installment/view/{id}', [InstallmentController::class, 'view']);


        Route::post('room/store', [RoomController::class, 'store']);
        Route::get('room', [RoomController::class, 'index']);
        Route::get('room/create', [RoomController::class, 'create']);
        Route::get('/room/get-list', [RoomController::class, 'getList']);
        Route::get('/room/view/{id}', [RoomController::class, 'view']);
        Route::post('room/update', [RoomController::class, 'update'])->name('room.update');
        Route::post('room/add', [RoomController::class, 'add'])->name('room.add');
        Route::get('/room/edit/{id}', [RoomController::class, 'edit']);


        Route::post('table/store', [TableController::class, 'store']);
        Route::get('table', [TableController::class, 'index']);
        Route::get('table/create', [TableController::class, 'create']);
        Route::get('/table/get-list', [TableController::class, 'getList']);
        Route::get('/table/view/{id}', [TableController::class, 'view']);
        Route::post('table/update', [TableController::class, 'update'])->name('table.update');
        Route::post('table/add', [TableController::class, 'add'])->name('table.add');
        Route::get('/table/edit/{id}', [TableController::class, 'edit']);


        Route::post('booking/store', [BookingController::class, 'store']);
        Route::get('booking', [BookingController::class, 'index']);
        Route::get('booking/create', [BookingController::class, 'create']);
        Route::get('/booking/get-list', [BookingController::class, 'getList']);
        Route::get('/booking/view/{id}', [BookingController::class, 'view']);
        Route::post('booking/update', [BookingController::class, 'update'])->name('booking.update');
        Route::post('booking/add', [BookingController::class, 'add'])->name('booking.add');
        Route::get('/booking/edit/{id}', [BookingController::class, 'edit']);



        Route::post('reservation/store', [ReservationController::class, 'store']);
        Route::get('reservation', [ReservationController::class, 'index']);
        Route::get('reservation/create', [ReservationController::class, 'create']);
        Route::get('/reservation/get-list', [ReservationController::class, 'getList']);
        Route::get('/reservation/view/{id}', [ReservationController::class, 'view']);
        Route::post('reservation/update', [ReservationController::class, 'update'])->name('reservation.update');
        Route::post('reservation/add', [ReservationController::class, 'add'])->name('reservation.add');
        Route::get('/reservation/edit/{id}', [ReservationController::class, 'edit']);
    });
});
