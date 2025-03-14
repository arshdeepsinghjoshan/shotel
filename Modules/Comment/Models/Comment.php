<?php

namespace Modules\Comment\Models;

use App\Models\User;
use App\Traits\AActiveRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory, AActiveRecord;

    protected $guarded = [''];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function getStateBadgeOption()
    {
        $list = [
            self::STATE_INACTIVE => "secondary",
            self::STATE_REJECTED => "secondary",
            self::STATE_ACTIVE => "success",
        ];
        return isset($list[$this->state_id]) ? 'badge bg-' . $list[$this->state_id] : 'Not Defined';
    }
    public function getStateButtonOption($state_id = null)
    {
        $list = [
            self::STATE_INACTIVE => "secondary",
            self::STATE_REJECTED => "secondary",
            self::STATE_ACTIVE => "success",
        ];
        return isset($list[$state_id]) ? 'btn btn-' . $list[$state_id] : 'Not Defined';
    }


    const STATE_INACTIVE = 0;

    const STATE_ACTIVE = 1;

    const STATE_REJECTED = 2;


    public function getEncryption()
    {
        $list = self::getEncryptionOptions();
        return isset($list[$this->encryption_type]) ? $list[$this->encryption_type] : 'Not Defined';
    }

    public static function getStateOptions()
    {
        return [
            self::STATE_INACTIVE => "Pending",
            self::STATE_ACTIVE => "Approved",
            self::STATE_REJECTED => "Rejected",
        ];
    }

    public function getState()
    {
        $list = self::getStateOptions();
        return isset($list[$this->state_id]) ? $list[$this->state_id] : 'Not Defined';
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
                    'url' => url('kyc'),

                ];


                $menu['update'] = [
                    'label' => 'fa fa-edit',
                    'color' => 'btn btn-icon btn-warning',
                    'title' => __('Update'),
                    'url' => url('kyc/edit/' . ($model->id ?? 0) . '/' . ($model->slug ?? '')),
                    'visible' => false

                ];
                break;
            case 'index':
                $menu['add'] = [
                    'label' => 'fa fa-plus',
                    'color' => 'btn btn-icon btn-primary',
                    'title' => __('Add'),
                    'url' => url('kyc/create'),
                    'visible' => false
                ];
        }
        return $menu;
    }
}
