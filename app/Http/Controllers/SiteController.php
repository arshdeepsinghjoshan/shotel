<?php

namespace App\Http\Controllers;

use App\Models\PinCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Wallet;
use App\Traits\Permission;
use Illuminate\Support\Str;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SiteController extends Controller
{
    public function index()
    {
        try {
            if (User::isAdmin()) {
                return redirect('login');
            }

            return view('site.index');
        } catch (\Exception $e) {
            return redirect('user')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
   
}
