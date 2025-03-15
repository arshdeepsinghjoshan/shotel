<?php

namespace App\Models;

use App\Traits\AActiveRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    const TYPE_GRIND = 0;

    const TYPE_PRODUCT = 1;

    const STATE_DELETE = 2;


    const STATE_PENDING = 0;

    const STATE_CONFIRMED = 1;

    const STATE_CHECKED_IN = 2;

    const STATE_CHECKED_OUT = 3;

    const STATE_CANCELED = 4;

    const IS_PAID = 1;

    const NOT_PAID = 0;

    use AActiveRecord;

    protected $guarded = ['id'];

    public function cart()
    {
        return $this->hasOne(Cart::class, 'booking_id');
    }
    public static function getStateOptions()
    {
        return [
            self::STATE_PENDING => 'Pending',
            self::STATE_CONFIRMED => 'Confirmed',
            self::STATE_CHECKED_IN => 'Checked In',
            self::STATE_CHECKED_OUT => 'Checked Out',
            self::STATE_CANCELED => 'Canceled',
        ];
    }


    public static function getIsPaidOptions()
    {
        return [
            self::NOT_PAID => "Not Paid",
            self::IS_PAID => "Paid",
        ];
    }
    public function getIsPaid()
    {
        $list = self::getIsPaidOptions();
        return isset($list[$this->is_paid]) ? $list[$this->is_paid] : 'Not Defined';
    }



    public static function getTypeOptions()
    {
        return [
            self::TYPE_GRIND => "Grind",
            self::TYPE_PRODUCT => "Product",
        ];
    }
    public function getType()
    {
        $list = self::getTypeOptions();
        return isset($list[$this->type_id]) ? $list[$this->type_id] : 'Not Defined';
    }
    public static function getStateOptionsBadge($stateValue)
    {
        $list = [
        
            self::STATE_PENDING => 'secondary',
            self::STATE_CONFIRMED => 'success',
            self::STATE_CHECKED_IN => 'success',
            self::STATE_CHECKED_OUT => 'success',
            self::STATE_CANCELED => 'danger',

        ];
        return isset($stateValue) ? $list[$stateValue] : 'Not Defined';
    }
    public function getStateButtonOption($state_id = null)
    {
        $list = [
        
            self::STATE_PENDING => 'secondary',
            self::STATE_CONFIRMED => 'success',
            self::STATE_CHECKED_IN => 'success',
            self::STATE_CHECKED_OUT => 'success',
            self::STATE_CANCELED => 'danger',

        ];
        return isset($list[$state_id]) ? 'btn btn-' . $list[$state_id] : 'Not Defined';
    }
    public function getState()
    {
        $list = self::getStateOptions();
        return isset($list[$this->state_id]) ? $list[$this->state_id] : 'Not Defined';
    }
    public function getStateBadgeOption()
    {
        $list = [
         
            self::STATE_PENDING => 'secondary',
            self::STATE_CONFIRMED => 'success',
            self::STATE_CHECKED_IN => 'success',
            self::STATE_CHECKED_OUT => 'success',
            self::STATE_CANCELED => 'danger',
        ];
        return isset($list[$this->state_id]) ? 'badge bg-' . $list[$this->state_id] : 'Not Defined';
    }
    const PRIORITY_LOW = 0;
    const PRIORITY_MEDIUM = 1;
    const PRIORITY_HIGH = 2;

    public static function getPriorityOptions()
    {
        return [
            self::PRIORITY_LOW => "Low",
            self::PRIORITY_MEDIUM => "Medium",
            self::PRIORITY_HIGH => "High",
        ];
    }

    public function getPriority()
    {
        $list = self::getPriorityOptions();
        return isset($list[$this->priority_id]) ? $list[$this->priority_id] : 'Not Defined';
    }


    public function getUserOption()
    {
        return User::where('state_id', User::STATE_ACTIVE)->get();
    }


    public function getRoomOption()
    {
        return Room::where('state_id', Room::STATE_OPEN)->get();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function getCategoryOption()
    {
        return ProductCategory::where('state_id', ProductCategory::STATE_ACTIVE)->get();
    }


    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function getCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }



    public function updateMenuItems($action, $model = null)
    {
        $menu = [];
        switch ($action) {
            case 'view':
                $menu['manage'] = [
                    'label' => 'fa fa-step-backward',
                    'color' => 'btn btn-primary',
                    'title' => __('Manage'),
                    'url' => url('booking'),

                ];
                $menu['update'] = [
                    'label' => 'fa fa-edit',
                    'color' => 'btn btn-primary',
                    'title' => __('Update'),
                    'url' => url('booking/edit/' . $model->id),

                ];
                break;
            case 'index':
                $menu['add'] = [
                    'label' => 'fa fa-plus',
                    'color' => 'btn btn-primary',
                    'title' => __('Add'),
                    'url' => url('booking/create'),
                    'visible' => User::isAdmin()
                ];
                $menu['import'] = [
                    'label' => 'fas fa-file-import',
                    'color' => 'btn btn-primary',
                    'title' => __('File Import'),
                    'url' => url('booking/import'),
                    'visible' => false
                ];
        }
        return $menu;
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




    public function scopeSearchPriority($query, $search)
    {
        $stateOptions = self::getPriorityOptions();
        return $query->where(function ($query) use ($search, $stateOptions) {
            foreach ($stateOptions as $stateId => $stateName) {
                if (stripos($stateName, $search) !== false) {
                    $query->orWhere('priority_id', $stateId);
                }
            }
        });
    }
}
