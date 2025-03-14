<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Mail\NewUserRegistration;
use App\Traits\AActiveRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Modules\Smtp\Http\Controllers\MailController;
use Modules\Smtp\Http\Models\SmtpEmailQueue;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, AActiveRecord;
    const STATE_INACTIVE = 0;

    const STATE_ACTIVE = 1;
    const STATE_DELETE = 2;

    const ROLE_ADMIN = 0;

    const ROLE_USER = 1;

    const POSITION_LEFT = 0;

    const POSITION_RIGHT = 1;


    const EMAIL_VERIFIED = 1;

    const EMAIL_NOT_VERIFIED = 0;

    const POSITION_MID = 2;

    public function getEmail()
    {
        $list = self::getEmailOptions();
        return isset($list[$this->email_verified]) ? $list[$this->email_verified] : 'Not Defined';
    }
    public static function getEmailOptions($id = null)
    {
        $list = array(
            self::EMAIL_NOT_VERIFIED => "Not Verified",
            self::EMAIL_VERIFIED => "Verified",
        );
        if ($id === null)
            return $list;
        return isset($list[$id]) ? $list[$id] : 'Not Defined';
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [''];


    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'created_by_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function pendingOrders()
    {
        return $this->hasMany(Order::class, 'user_id')->where('order_payment_status', '!=', Order::STATE_COMPLETED)->sum('total_amount');
    }



    public function transactions()
    {
        return $this->hasManyThrough(WalletTransaction::class, Wallet::class, 'created_by_id', 'wallet_id');
    }
    public function pendingPayment()
    {
        return $this->pendingOrders() - $this->pendingInstallment()->sum('amount');
    }
    public function pendingInstallment()
    {
        return $this->hasManyThrough(Installment::class, Order::class, 'user_id', 'order_id')->where('orders.order_payment_status', '!=', 1);
    }
    public function subscribedPlan()
    {
        return $this->hasMany(SubscribedPlan::class, 'created_by_id');
    }

    public function getTotalSubscribedPlanAmount()
    {
        return $this->subscribedPlan->sum(function ($subscribedPlan) {
            return $subscribedPlan->subscriptionPlan->price;
        });
    }

    public function password()
    {
        return  $this->password = Hash::make($this->password);
    }

    public function todayProfit()
    {
        $today = Carbon::today();

        // Fetch subscribed plans for today
        $order = Order::whereDate('created_at', $today)
            ->get();

        // Calculate total sales for today
        $totalSales = $order->sum(function ($order) {
            return $order->total_amount;
        });


        return $totalSales;
        // return view('sales.todayProfit', compact('totalSales', 'totalProfit'));
    }

    public function generateReferralCode()
    {
        $randomString = strtoupper(Str::random(4));
        $timestamp = Carbon::now()->timestamp;
        $code = $randomString . $timestamp;
        $existingCode = User::where('referral_id', $code)->exists();
        if ($existingCode) {
            return $this->generateReferralCode();
        }
        return $this->referral_id = $code;
    }

    public function generateActivationkey()
    {
        $randomString = strtoupper(Str::random(25));
        $timestamp = Carbon::now()->timestamp;
        $code = $randomString . $timestamp;
        $existingCode = User::where('activation_key', $code)->exists();
        if ($existingCode) {
            return $this->generateReferralCode();
        }
        return $this->activation_key = $code;
    }
    public function generateEmailOtp($length = 6)
    {
        $otp = '';
        for ($i = 0; $i < $length; $i++) {
            $otp .= mt_rand(0, $length);
        }
        return $this->otp_email = $otp;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function getRoleOptions($id = null)
    {
        $list = array(
            self::ROLE_USER => "User",
        );
        if ($id === null)
            return $list;
        return isset($list[$id]) ? $list[$id] : 'Not Defined';
    }
    public function getRole()
    {
        $list = self::getRoleOptions();
        $list[self::ROLE_ADMIN] = 'Admin';
        return isset($list[$this->role_id]) ? $list[$this->role_id] : 'Not Defined';
    }


    public static function isGuest()
    {
        return Auth::guest();
    }


    public static function isAdmin()
    {
        $user = Auth::user();
        if ($user == null) {
            return false;
        }
        return ($user->isActive() &&  $user->role_id == User::ROLE_ADMIN);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function scopeSearchRole($query, $search)
    {
        $roleOptions = self::getRoleOptions();
        return $query->where(function ($query) use ($search, $roleOptions) {
            foreach ($roleOptions as $roleId => $roleName) {
                if (stripos($roleName, $search) !== false) {
                    $query->orWhere('role_id', $roleId);
                }
            }
        });
    }

    public function scopeSearchState($query, $search)
    {
        $roleOptions = self::getStateOptions();
        return $query->where(function ($query) use ($search, $roleOptions) {
            foreach ($roleOptions as $roleId => $roleName) {
                if (stripos($roleName, $search) !== false) {
                    $query->orWhere('state_id', $roleId);
                }
            }
        });
    }

    public function isActive()
    {
        return ($this->state_id == User::STATE_ACTIVE);
    }

    public static function isUser()
    {
        $user = Auth::user();
        if ($user == null) {
            return false;
        }
        return ($user->isActive() && $user->role_id == User::ROLE_USER);
    }
    public function getStateBadgeOption()
    {
        $list = [
            self::STATE_ACTIVE => "success",
            self::STATE_INACTIVE => "secondary",
            self::STATE_DELETE => "danger",
        ];
        return isset($list[$this->state_id]) ? 'badge bg-' . $list[$this->state_id] : 'Not Defined';
    }

    public function getStateButtonOption($state_id = null)
    {
        $list = [
            self::STATE_ACTIVE => "success",
            self::STATE_INACTIVE => "secondary",
            self::STATE_DELETE => "danger",
        ];
        return isset($list[$state_id]) ? 'btn btn-' . $list[$state_id] : 'Not Defined';
    }

    public function getState()
    {
        $list = self::getStateOptions();
        return isset($list[$this->state_id]) ? $list[$this->state_id] : 'Not Defined';
    }
    public static function getStateOptions($id = null)
    {
        $list = array(
            self::STATE_INACTIVE => "Inactive",
            self::STATE_ACTIVE => "Activated",
            self::STATE_DELETE => "Deleted",
        );
        if ($id === null)
            return $list;
        return isset($list[$id]) ? $list[$id] : 'Not Defined';
    }
    public function updateMenuItems($action, $model = null)
    {
        $menu = [];
        switch ($action) {
            case 'view':
                $menu['manage'] = [

                    'label' => 'fa fa-step-backward',
                    'color' => 'btn btn-icon btn-warning',
                    'title' => __('Manage'),
                    'text' => false,
                    'url' => url('user/'),

                ];
                $menu['login'] = [
                    'label' => 'fa fa-sign-in',
                    'color' => 'btn  btn-warning',
                    'title' => __(' Login'),
                    'text' => true,
                    'url' => url('user-login/' . ($model->id ?? 0) . '/' . ($model->slug ?? '')),
                    'visible' => ($model->role_id != User::ROLE_ADMIN  && $model->id != Auth::id())

                ];

                $menu['update'] = [
                    'label' => 'fa fa-edit',
                    'color' => 'btn btn-icon btn-warning',
                    'title' => __('Update'),
                    'url' => url('user/edit/' . ($model->id ?? 0) . '/' . ($model->slug ?? '')),

                ];
                break;
            case 'index':
                $menu['add'] = [
                    'label' => 'fa fa-plus',
                    'color' => 'btn btn-icon btn-primary',
                    'title' => __('Add'),
                    'url' => url('user/create'),
                    'visible' => true
                ];
        }
        return $menu;
    }

    public function profitSalesTransactions($type = null)
    {
        $previousTotalProfit = Auth::user()->wallet->balance ?? 0;
        $transactions = Auth::user()->transactions()->get();
        $totalProfit = $transactions->reduce(function ($carry, $transaction) {
            if ($transaction->getType() === 'Credit') {
                return $carry + $transaction->amount;
            } elseif ($transaction->getType() === 'Debit') {
                return $carry - $transaction->amount;
            }
            return $carry;
        });
        if ($previousTotalProfit !== null && $previousTotalProfit != 0) {
            $percentageChange = (($totalProfit - $previousTotalProfit) / $previousTotalProfit) * 100;
        } else {
            $percentageChange = 0;
        }

        switch ($type) {
            case "profit":
                return $totalProfit ?? 0;

            case "percentageChange":
                return $percentageChange;


            case "sales":
                $totalSales = Order::get()->sum(function ($order) {
                    return $order->total_amount;
                });
                return $totalSales;

            case "sales_percentage":
                $order = Order::get();

                $totalSales = $order->sum(function ($order) {
                    return $order->total_amount;
                });
                $salesWithPercentages = $order->map(function ($order) use ($totalSales) {
                    $price = $order->total_amount;
                    $percentage = $totalSales > 0 ? ($price / $totalSales) * 100 : 0;
                    return  $percentage;
                });
                return $salesWithPercentages;


            case "payments":
                echo 0;


            case "transactions":
                echo 0;


            default:
                echo 0;
        }
    }


    public function relationGridView($queryRelation, $request)
    {
        $dataTable = Datatables::of($queryRelation)
            ->addIndexColumn()

            ->addColumn('created_by', function ($data) {
                return !empty($data->createdBy && $data->createdBy->name) ? $data->createdBy->name : 'N/A';
            })
            ->addColumn('name', function ($data) {
                return !empty($data->name) ? (strlen($data->name) > 60 ? substr(ucfirst($data->name), 0, 60) . '...' : ucfirst($data->name)) : 'N/A';
            })
            ->addColumn('role_id', function ($data) {
                return  $data->getRole();
            })
            ->addColumn('status', function ($data) {
                return '<span class="' . $data->getStateBadgeOption() . '">' . $data->getState() . '</span>';
            })
            ->rawColumns(['created_by'])

            ->addColumn('created_at', function ($data) {
                return (empty($data->created_at)) ? 'N/A' : date('Y-m-d', strtotime($data->created_at));
            })
            ->addColumn('action', function ($data) {
                $html = '<div class="table-actions text-center">';
                $html .= ' <a class="btn btn-icon btn-primary mt-1" href="' . url('user/edit/' . $data->id) . '" ><i class="fa fa-edit"></i></a>';
                $html .=    '  <a class="btn btn-icon btn-primary mt-1" href="' . url('user/view/' . $data->id) . '"  ><i class="fa fa-eye
                "data-toggle="tooltip"  title="View"></i></a>';
                $html .=  '</div>';
                return $html;
            })->addColumn('customerClickAble', function ($data) {
                $html = 0;

                return $html;
            })
            ->rawColumns([
                'action',
                'created_at',
                'status',
                'customerClickAble'
            ]);
        if (!($queryRelation instanceof \Illuminate\Database\Query\Builder)) {
            $searchValue = $request->input('search.value');
            if ($searchValue) {
                $searchTerms = explode(' ', $searchValue);
                $collection = $queryRelation->filter(function ($item) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        if (
                            strpos($item->id, $term) !== false ||
                            strpos($item->name, $term) !== false ||
                            strpos($item->email, $term) !== false ||
                            strpos($item->created_at, $term) !== false ||
                            (isset($item->createdBy) && strpos($item->createdBy->name, $term) !== false) ||
                            $item->searchState($term)
                        ) {
                            return true;
                        }
                    }
                    return false;
                });
            }
        }

        return $dataTable->make(true);
    }




    public function sendRegistrationMailtoUser()
    {

        MailController::sendMail('emails.new_user', $this);
    }
}
