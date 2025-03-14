<?php

namespace App\Models;

use App\Traits\AActiveRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DataTables;
use Illuminate\Support\Facades\Crypt;

class Order extends Model
{
    use HasFactory;

    use AActiveRecord;

    protected $guarded = ['id'];

    const STATE_PENDING = 0;
    const STATE_COMPLETED = 1;
    const STATE_CANCEL = 2;
    const STATE_REJECTED = 3;

    const PAYMENT_PENDING = 0;
    const PAYMENT_PAID = 1;
    const PAYMENT_INPROGESS = 2;
    const PAYMENT_REJECTED = 4;
    const PAYMENT_CANCEL = 3;



    public static function getStateOptions()
    {
        return [
            self::STATE_PENDING => "Pending",
            self::STATE_COMPLETED => "Completed",
            self::STATE_CANCEL => "Cancel",
            self::STATE_REJECTED => "Rejected",
        ];
    }

    public static function getPaymentOptions()
    {
        return [
            self::PAYMENT_PENDING => "Pending",
            self::PAYMENT_PAID => "Paid",
            self::PAYMENT_INPROGESS => "In Progress",
            self::PAYMENT_REJECTED => "Rejected",
            self::PAYMENT_CANCEL => "Cancel",
        ];
    }


    public function getShippingMethod()
    {
        $list = self::getShippingMethodOptions();
        return isset($list[$this->shipping_method]) ? $list[$this->shipping_method] : 'Not Defined';
    }

    public function getState()
    {
        $list = self::getStateOptions();
        return isset($list[$this->state_id]) ? $list[$this->state_id] : 'Not Defined';
    }
    public function getPayment()
    {
        $list = self::getPaymentOptions();
        return isset($list[$this->order_payment_status]) ? $list[$this->order_payment_status] : 'Not Defined';
    }

    public function getOrderStatus()
    {
        $list = self::getOrderStatusOptions();
        return isset($list[$this->order_status]) ? $list[$this->order_status] : 'Not Defined';
    }
    public function scopeSearchState($query, $search)
    {
        $stateOptions = self::getStateOptions();
        return $query->where(function ($query) use ($search, $stateOptions) {
            foreach ($stateOptions as $stateId => $stateName) {
                if (stripos($stateName, $search) !== false) {
                    $query->orWhere('state_id', $stateId);
                }
            }
        });
    }

    public function scopeSearchOrderState($query, $search)
    {
        $orderStateOptions = self::getOrderStatusOptions();
        return $query->where(function ($query) use ($search, $orderStateOptions) {
            foreach ($orderStateOptions as $stateId => $stateName) {
                if (stripos($stateName, $search) !== false) {
                    $query->orWhere('order_status', $stateId);
                }
            }
        });
    }
    public function getStateBadge()
    {
        $list = [
            self::STATE_PENDING => "Banned",
            self::STATE_COMPLETED => "Active",
            self::STATE_CANCEL => "Reject",
            self::STATE_REJECTED => "Reject",
        ];
        return isset($list[$this->status]) ?  'badge badge-' . $list[$this->status] : 'Not Defined';
    }
    public function getStateButtonOption($state_id = null)
    {
        $list = [
            self::STATE_PENDING => "secondary",
            self::STATE_COMPLETED => "success",
            self::STATE_CANCEL => "danger",
            self::STATE_REJECTED => "danger",
            self::PAYMENT_REJECTED => "danger",


        ];
        return isset($list[$state_id]) ? 'btn btn-' . $list[$state_id] : 'Not Defined';
    }

    public function getPaymentBadgeOption()
    {
        $list = [
            self::PAYMENT_PENDING => "secondary",
            self::PAYMENT_PAID => "success",
            self::PAYMENT_INPROGESS => "secondary",
            self::PAYMENT_REJECTED => "danger",
            self::PAYMENT_CANCEL => "danger",
        ];
        return isset($list[$this->order_payment_status]) ?  'btn btn-' . $list[$this->order_payment_status] : 'Not Defined';
    }

    public function getStateBadgeOption()
    {
        $list = [
            self::STATE_PENDING => "secondary",
            self::STATE_COMPLETED => "success",
            self::STATE_REJECTED => "danger",
            self::STATE_CANCEL => "danger",
        ];
        return isset($list[$this->state_id]) ? 'btn btn-' . $list[$this->state_id] : 'Not Defined';
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
    public function getCreatedAt()
    {
        return (empty($this->created_at)) ? 'N/A' : date('Y-m-d h:i:s A', strtotime($this->created_at));
    }

    public function getSale()
    {

        if ($this->sale) {
            return $this->sale->saleItems;
        }
        return [];
    }


    public function comment()
    {
        return $this->hasOne(Comment::class, 'model_id')
            ->where('model_type', self::class);
    }


    public function getUpdatedAt()
    {
        return (empty($this->updated_at)) ? 'N/A' : date('Y-m-d h:i:s A', strtotime($this->updated_at));
    }

    public function getProductOption()
    {
        return Product::findActive()->get();
    }


    public function installments()
    {
        return $this->hasMany(Installment::class);
    }

    public function paidAmount()
    {
        return $this->installments()->sum('amount');
    }

    public function remainingAmount()
    {
        return $this->total_amount - $this->paidAmount();
    }

    public function updateMenuItems($action, $model = null)
    {
        $menu = [];
        switch ($action) {
            case 'view':
                $menu['manage'] = [
                    'label' => 'fa fa-step-backward',
                    'color' => 'btn btn-primary',
                    'title' => __('Order'),
                    'url' => url('/order'),

                ];
                $menu['payment'] = [
                    'label' => 'fa fa-credit-card',
                    'color' => 'btn btn-primary open-pending-payment-modal',
                    'title' => __('Order'),

                ];
                $menu['download-pdf'] = [
                    'label' => 'fa fa-file-pdf',
                    'color' => 'btn btn-primary',
                    'title' => __('Order'),
                    'url' => url('/order/generate-pdf/' . Crypt::encryptString($model->id)),

                ];

                $menu['download'] = [
                    'label' => 'fa fa-download',
                    'color' => 'btn btn-primary',
                    'title' => __('Invoice download'),
                    'url' => url('/order/download/' . $model->id),
                    'visible' => false

                ];
                break;
            case 'index':
                $menu['add'] = [
                    'label' => 'fa fa-plus',
                    'color' => 'btn btn-primary',
                    'title' => __('Create new Sale'),
                    'url' => url('/order/create'),
                    'text' => false,
                ];
        }
        return $menu;
    }


    public function generateOrderNumber()
    {
        $randomString = strtoupper(Str::random(4));
        $timestamp = Carbon::now()->timestamp;
        $code = 'order_' .  $randomString . $timestamp;
        $existingCode = Order::where('order_number', $code)->exists();
        if ($existingCode) {
            return $this->generateOrderNumber();
        }
        return $this->order_number = $code;
    }

    public static function getUniqueId($id)
    {
        $user_data = User::find($id);
        return $user_data->unique_id ?? 'N/A';
    }

    public function relationGridView($queryRelation, $request)
    {
        $dataTable = Datatables::of($queryRelation)
            ->addIndexColumn()

            ->addColumn('created_by', function ($data) {
                return !empty($data->createdBy && $data->createdBy->name) ? $data->createdBy->name : 'N/A';
            })
            ->addColumn('title', function ($data) {
                return !empty($data->title) ? (strlen($data->title) > 60 ? substr(ucfirst($data->title), 0, 60) . '...' : ucfirst($data->title)) : 'N/A';
            })
            ->addColumn('total_amount', function ($data) {
                return number_format($data->total_amount, 2);
            })
            ->addColumn('status', function ($data) {
                return '<span class="' . $data->getStateBadgeOption() . '">' . $data->getState() . '</span>';
            })

            ->addColumn('payment_status', function ($data) {
                return '<span class="' . $data->getPaymentBadgeOption() . '">' . $data->getPayment() . '</span>';
            })
            ->rawColumns(['created_by'])

            ->addColumn('created_at', function ($data) {
                return (empty($data->created_at)) ? 'N/A' : date('Y-m-d', strtotime($data->created_at));
            })
            ->addColumn('updated_at', function ($data) {
                return (empty($data->updated_at)) ? 'N/A' : date('Y-m-d', strtotime($data->updated_at));
            })
            ->addColumn('action', function ($data) {
                $html = '<div class="table-actions text-center">';
                // $html .= ' <a class="btn btn-icon btn-primary mt-1" href="' . url('support/edit/' . $data->id) . '" ><i class="fa fa-edit"></i></a>';
                $html .=    '  <a class="btn btn-icon btn-primary mt-1" href="' . url('order/view/' . $data->id) . '"  ><i class="fa fa-eye
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
                'payment_status',
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
                            strpos($item->order_number, $term) !== false ||
                            strpos($item->total_amount, $term) !== false ||
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
}
