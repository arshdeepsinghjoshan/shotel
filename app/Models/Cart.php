<?php

namespace App\Models;

use App\Traits\AActiveRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Cart extends Model
{
    use HasFactory;

    use AActiveRecord;

    protected $guarded = ['id'];

    const STATE_INITIATED = 0;

    const STATE_PAID = 1;

    const STATE_FAILED = 2;

    const STATE_PENDING = 3;


    const ORDER_STATE_PLACED = 0;

    const ORDER_STATE_PARTIAL_SHIPMENT = 6;

    const ORDER_STATE_RECEIVED = 5;


    const ORDER_STATE_PREPARING = 4;

    const ORDER_STATE_CANCEL = 2;

    const ORDER_STATE_READ_TO_DELIVER = 3;

    const ORDER_STATE_DELIVERED = 1;




    const SHIPPING_METHOD_PICKUP = 0;

    const SHIPPING_METHOD_COURIER = 1;


    public static function getStateOptions()
    {
        return [
            self::STATE_INITIATED => "Initiated",
            self::STATE_PAID => "Paid",
            self::STATE_FAILED => "Failed",
            self::STATE_PENDING => "Pending",
        ];
    }

    public function getTotalPriceSum()
    {
        $query =  Cart::my();
        return $query->sum('total_price');
    }

    public function getTotalQuantitySum()
    {
        $query =  Cart::my()->whereNull('type_id');
        
        return $query->sum('quantity');
    }
    public function getGrindPrice()
    {
        $model = Cart::my()->where('type_id', 1)->first();
        
        return $model->unit_price ?? 2;
    }
    public static function getOrderStatusOptions()
    {
        return [
            self::ORDER_STATE_PLACED => "Placed",
            self::ORDER_STATE_PARTIAL_SHIPMENT => "Partial Shipment",
            self::ORDER_STATE_RECEIVED => "Received",
            self::ORDER_STATE_PREPARING => "Preparing",
            self::ORDER_STATE_READ_TO_DELIVER => "Ready to deliver",
            self::ORDER_STATE_DELIVERED => "Delivered",
            self::ORDER_STATE_CANCEL => "Cancel",
        ];
    }


    public function getShippingMethodOptions($shippingMethod = null)
    {
        $list = [
            self::SHIPPING_METHOD_COURIER => "Courier",
            self::SHIPPING_METHOD_PICKUP => "Pick-up",
        ];
        return isset($list[$shippingMethod]) ? $list[$shippingMethod] : $list;
    }


    public function getShippingMethod()
    {
        $list = self::getShippingMethodOptions();
        return isset($list[$this->shipping_method]) ? $list[$this->shipping_method] : 'Not Defined';
    }

    public function getState()
    {
        $list = self::getStateOptions();
        return isset($list[$this->status]) ? $list[$this->status] : 'Not Defined';
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
            self::STATE_INITIATED => "New",
            self::STATE_PAID => "Active",
            self::STATE_PENDING => "Banned",
            self::STATE_FAILED => "Reject",
        ];
        return isset($list[$this->status]) ?  'badge badge-' . $list[$this->status] : 'Not Defined';
    }

    public function getOrderStatusBadge()
    {
        $list = [
            self::ORDER_STATE_RECEIVED => "New",
            self::ORDER_STATE_PLACED => "New",
            self::ORDER_STATE_PARTIAL_SHIPMENT => "New",
            self::ORDER_STATE_PREPARING => "New",
            self::ORDER_STATE_READ_TO_DELIVER => "New",
            self::ORDER_STATE_DELIVERED => "Active",
            self::ORDER_STATE_CANCEL => "Reject",
        ];
        return isset($list[$this->order_status]) ?  'badge badge-' . $list[$this->order_status] : 'New';
    }

    public function stateChange()
    {
        try {
            if ($this->order_status == Self::ORDER_STATE_PARTIAL_SHIPMENT) {
                DB::beginTransaction();
                $partialShipment = PartialShipment::firstOrNew(['order_id' => $this->id]);
                $partialShipment->warehouse_id = $this->warehouse_id;
                $partialShipment->state_id = PartialShipment::STATE_PENDING;
                $partialShipment->created_by_id = Auth::id();
                if (!$partialShipment->save()) {
                    DB::rollBack();
                    return true;
                }
                foreach ($this->saleItems as $saleItem) {
                    $warehouseInventoryModel = WarehouseInventory::findActive()
                        ->where([
                            'product_id' => $saleItem->product_id,
                            'warehouse_id' => $saleItem->warehouse_id
                        ])
                        ->select('remaining_quantity')
                        ->first();
                    $partialShipmentItem = PartialShipmentItem::firstOrNew([
                        'partial_shipment_id' => $partialShipment->id,
                        'product_id' => $saleItem->product_id,
                        'order_id' => $this->id
                    ]);
                    $partialShipmentItem->warehouse_id = $this->warehouse_id;
                    $partialShipmentItem->total_quantity = $saleItem->quantity;
                    $partialShipmentItem->created_by_id = Auth::id();
                    $partialShipmentItem->state_id = PartialShipmentItem::STATE_ACTIVE;
                    if ($warehouseInventoryModel && $warehouseInventoryModel->remaining_quantity < $saleItem->quantity) {
                        $partialShipmentItem->pending_quantity = abs($warehouseInventoryModel->remaining_quantity - $saleItem->quantity);
                    } else {
                        $partialShipmentItem->pending_quantity = $saleItem->quantity;
                    }
                    if (!$partialShipmentItem->save()) {
                        DB::rollBack();
                        return true;
                    }
                }
                DB::commit();
            }
            $this->save();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function getStateBadgeOption($state_id)
    {
        $list = [
            self::STATE_INITIATED => "secondary",
            self::STATE_PENDING => "secondary",
            self::STATE_PAID => "success",
            self::STATE_FAILED => "danger",
            self::ORDER_STATE_PREPARING => "secondary",
            self::ORDER_STATE_PARTIAL_SHIPMENT => "secondary",
            self::ORDER_STATE_RECEIVED => "secondary",
        ];
        return isset($list[$state_id]) ? 'btn btn-' . $list[$state_id] : 'Not Defined';
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
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

                $menu['download'] = [
                    'label' => 'fa fa-download',
                    'color' => 'btn btn-primary',
                    'title' => __('Invoice download'),
                    'url' => url('/order/download/' . $model->id),

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
        $code = $randomString . $timestamp;
        $existingCode = User::where('referral_id', $code)->exists();
        if ($existingCode) {
            return $this->generateOrderNumber();
        }
        return $this->order_id = 'order_admin_' . $code;
    }

    public static function getUniqueId($id)
    {
        $user_data = User::find($id);
        return $user_data->unique_id ?? 'N/A';
    }
}
