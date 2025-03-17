<?php

/**
 *@copyright : ASk. < http://arshresume.epizy.com/ >
 *@author	 : Arshdeep Singh < arshdeepsinghjoshan84@gmail.com >
 *
 * All Rights Reserved.
 * Proprietary and confidential :  All information contained herein is, and remains
 * the property of ASK. and its partners.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 *
 */

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Modules\Notification\Http\Models\Notification;
use Modules\Smtp\Http\Models\SmtpEmailQueue;

class DashboardController extends Controller
{



    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show the customer page
     *
     */
    public function index()
    {
        $data = [
            'total_bookings' => Reservation::count(),
            'total_customers' => User::count(),
            'total_active_customers' => User::findActive()->count(),
            'total_open_rooms' => Room::where('state_id', Room::STATE_OPEN)->count(),
            'total_booked_rooms' => Room::where('state_id', Room::STATE_BOOKED)->count(),
            'total_inactive_rooms' => Room::where('state_id', Room::STATE_INACTIVE)->count(),
            'total_available_tables' => Table::where('state_id', Table::STATE_ACTIVE)->count(),
            'total_booked_tables' => Table::whereNotIn('state_id', [Reservation::STATUS_CANCELED, Reservation::STATUS_COMPLETED])->count(),
        ];

        return view('dashboard.index', compact('data'));
    }

    public function getChartData()
    {

        $customersData = User::Where(['role_id' => User::ROLE_USER])->pluck('id')->toArray();

        $chartData = [
            'customers' => $customersData,
        ];

        return response()->json($chartData);
    }
}
