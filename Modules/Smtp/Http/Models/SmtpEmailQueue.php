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

namespace Modules\Smtp\Http\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmtpEmailQueue extends Model
{


    use HasFactory;

    protected $guarded = [''];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'model_id');
    }
    const STATE_PENDING = 0;

    const STATE_SENT = 1;

    const STATE_DELETED = 2;

    const STATE_SEEN = 3;

    const TYPE_DELETE_AFTER_SEND = 0;

    const TYPE_KEEP_AFTER_SEND = 1;

    public static function getStateOptions()
    {
        return [
            self::STATE_PENDING => "Pending",
            self::STATE_SENT => "Sent",
            self::STATE_DELETED => "Discarded",
            self::STATE_SEEN => "Seen"
        ];
    }

    public function getStateBadgeOption()
    {
        $list = [
            self::STATE_SENT => "success",
            self::STATE_PENDING => "secondary",
            self::STATE_DELETED => "secondary",
            self::STATE_SEEN => "secondary"
        ];
        return isset($list[$this->state_id]) ? 'badge bg-' . $list[$this->state_id] : 'Not Defined';
    }
    public function getStateButtonOption($state_id = null)
    {
        $list = [
            self::STATE_SENT => "success",
            self::STATE_PENDING => "secondary",
            self::STATE_DELETED => "secondary",
            self::STATE_SEEN => "secondary"
        ];
        return isset($list[$state_id]) ? 'btn btn-' . $list[$state_id] : 'Not Defined';
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

    public function getState()
    {
        $list = self::getStateOptions();
        return isset($list[$this->state_id]) ? $list[$this->state_id] : 'Not Defined';
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
                    'url' => url('email-queue/'),

                ];


                $menu['update'] = [
                    'label' => 'fa fa-edit',
                    'color' => 'btn btn-icon btn-warning',
                    'title' => __('Update'),
                    'url' => url('email-queue/edit/' . ($model->id ?? 0) . '/' . ($model->slug ?? '')),
                    'visible' =>false

                ];
                break;
            case 'index':
                $menu['add'] = [
                    'label' => 'fa fa-plus',
                    'color' => 'btn btn-icon btn-primary',
                    'title' => __('Add'),
                    'url' => url('email-queue/create'),
                    'visible' => false
                ];
        }
        return $menu;
    }
}
