<?php

namespace App\Models;

use App\Traits\AActiveRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    const STATE_INACTIVE = 0;

    const STATE_OPEN = 1;

    const STATE_BOOKED = 2;

    const TYPE_SINGLE = 0;

    const TYPE_DOUBLE = 1;

    const TYPE_VILA = 2;

    const TYPE_DELUX = 3;

    const TYPE_SUPER_DELUX = 4;

    const MEAL_NONE = 0;

    const MEAL_ALL = 1;

    const MEAL_DINNER = 2;

    const MEAL_BREAKFAST = 3;

    const MEAL_LUNCH = 4;

    const NON_AC_TYPE = 0;

    const AC_TYPE = 1;

    use AActiveRecord;

    protected $guarded = ['id'];

    public function cart()
    {
        return $this->hasOne(Cart::class, 'product_id');
    }
    public static function getStateOptions()
    {
        return [
            self::STATE_INACTIVE => "Inactive",
            self::STATE_OPEN => "open",
            self::STATE_BOOKED => "Booked",
        ];
    }


    public static function getTypeOptions()
    {
        return [
            self::TYPE_SINGLE => "Single",
            self::TYPE_DELUX => "Delux",
            self::TYPE_SUPER_DELUX => "Super Dulex",
            self::TYPE_VILA => "Vila",
            self::TYPE_DOUBLE => "Double",
        ];
    }
    public function getType()
    {
        $list = self::getTypeOptions();
        return isset($list[$this->type_id]) ? $list[$this->type_id] : 'Not Defined';
    }


    public static function getAcTypeOptions()
    {
        return [
            self::NON_AC_TYPE => "Non AC",
            self::AC_TYPE => "AC",
        ];
    }
    public function getAcType()
    {
        $list = self::getAcTypeOptions();
        return isset($list[$this->ac_type]) ? $list[$this->ac_type] : 'Not Defined';
    }


    public static function getMealTypeOptions()
    {
        return [
            self::MEAL_NONE => "None",
            self::MEAL_ALL => "All",
            self::MEAL_BREAKFAST => "Breakfast",
            self::MEAL_DINNER => "Dinner",
            self::MEAL_LUNCH => "Lunch",
        ];
    }
    public function getMealType()
    {
        $list = self::getMealTypeOptions();
        return isset($list[$this->ac_type]) ? $list[$this->ac_type] : 'Not Defined';
    }




    public static function getStateOptionsBadge($stateValue)
    {
        $list = [
            self::STATE_OPEN => "success",
            self::STATE_INACTIVE => "secondary",
            self::STATE_BOOKED => "danger",

        ];
        return isset($stateValue) ? $list[$stateValue] : 'Not Defined';
    }
    public function getStateButtonOption($state_id = null)
    {
        $list = [
            self::STATE_OPEN => "success",
            self::STATE_INACTIVE => "secondary",
            self::STATE_BOOKED => "danger",

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
            self::STATE_OPEN => "success",
            self::STATE_INACTIVE => "secondary",
            self::STATE_BOOKED => "danger",
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


    public function getCategoryOption()
    {
        return ProductCategory::where('state_id', ProductCategory::STATE_OPEN)->get();
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
                    'url' => url('room'),

                ];
                $menu['update'] = [
                    'label' => 'fa fa-edit',
                    'color' => 'btn btn-primary',
                    'title' => __('Update'),
                    'url' => url('room/edit/' . $model->id),

                ];
                break;
            case 'index':
                $menu['add'] = [
                    'label' => 'fa fa-plus',
                    'color' => 'btn btn-primary',
                    'title' => __('Add'),
                    'url' => url('room/create'),
                    'visible' => User::isAdmin()
                ];
                $menu['import'] = [
                    'label' => 'fas fa-file-import',
                    'color' => 'btn btn-primary',
                    'title' => __('File Import'),
                    'url' => url('room/import'),
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



    public function scopeSearchType($query, $search)
    {
        $typeOptions = self::getTypeOptions();
        return $query->where(function ($query) use ($search, $typeOptions) {
            foreach ($typeOptions as $typeId => $typeName) {
                if (stripos($typeName, $search) !== false) {
                    $query->orWhere('type_id', $typeId);
                }
            }
        });
    }


    public function scopeSearchMeal($query, $search)
    {
        $mealOptions = self::getMealTypeOptions();
        return $query->where(function ($query) use ($search, $mealOptions) {
            foreach ($mealOptions as $mealId => $mealName) {
                if (stripos($mealName, $search) !== false) {
                    $query->orWhere('meal_type', $mealId);
                }
            }
        });
    }


    public function scopeSearchACType($query, $search)
    {
        $acTypeOptions = self::getAcTypeOptions();
        return $query->where(function ($query) use ($search, $acTypeOptions) {
            foreach ($acTypeOptions as $acTypeId => $acTypeName) {
                if (stripos($acTypeName, $search) !== false) {
                    $query->orWhere('ac_type', $acTypeId);
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
