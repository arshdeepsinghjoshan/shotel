<?php

namespace App\Models;

use App\Traits\AActiveRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    const STATUS_PENDING = 0;

    const STATUS_CONFIRMED = 1;

    const STATUS_COMPLETED = 2;

    const STATUS_CANCELED = 3;


    const TYPE_GRIND = 0;

    const TYPE_PRODUCT = 1;



    use AActiveRecord;

    protected $guarded = ['id'];

    public function cart()
    {
        return $this->hasOne(Cart::class, 'product_id');
    }
    public static function getStateOptions()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELED => 'Canceled',
        ];
    }
    public static function isTableAvailable($table_id, $reservation_time, $exclude_booking_id = null)
    {
        return !self::where('table_id', $table_id)
            ->where('reservation_time', $reservation_time)
            ->whereNotIn('state_id', [self::STATUS_CANCELED, self::STATUS_COMPLETED])
            ->when($exclude_booking_id, function ($query) use ($exclude_booking_id) {
                $query->where('id', '!=', $exclude_booking_id);
            })
            ->exists();
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }

    public static function getStateOptionsBadge($stateValue)
    {
        $list = [
            self::STATUS_PENDING => 'secondary',
            self::STATUS_CONFIRMED => 'success',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELED => 'danger',

        ];
        return isset($stateValue) ? $list[$stateValue] : 'Not Defined';
    }
    public function getStateButtonOption($state_id = null)
    {
        $list = [
            self::STATUS_PENDING => 'secondary',
            self::STATUS_CONFIRMED => 'success',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELED => 'danger',

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
            self::STATUS_PENDING => 'secondary',
            self::STATUS_CONFIRMED => 'success',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELED => 'danger',
        ];
        return isset($list[$this->state_id]) ? 'badge bg-' . $list[$this->state_id] : 'Not Defined';
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


    public function getUserOption()
    {
        return User::where('state_id', User::STATE_ACTIVE)->get();
    }

    public function getTableOption()
    {
        return Table::where('state_id', Table::STATE_ACTIVE)->get();
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
                    'url' => url('reservation'),

                ];
                $menu['update'] = [
                    'label' => 'fa fa-edit',
                    'color' => 'btn btn-primary',
                    'title' => __('Update'),
                    'url' => url('reservation/edit/' . $model->id),

                ];
                break;
            case 'index':
                $menu['add'] = [
                    'label' => 'fa fa-plus',
                    'color' => 'btn btn-primary',
                    'title' => __('Add'),
                    'url' => url('reservation/create'),
                    'visible' => User::isAdmin()
                ];
                $menu['import'] = [
                    'label' => 'fas fa-file-import',
                    'color' => 'btn btn-primary',
                    'title' => __('File Import'),
                    'url' => url('reservation/import'),
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
}
