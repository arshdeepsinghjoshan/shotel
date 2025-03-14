<?php

namespace App\Models;

use App\Traits\AActiveRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DataTables;

class OrderItem extends Model
{
    use HasFactory;

    const STATE_INACTIVE = 0;
    const STATE_ACTIVE = 1;
    const STATE_DELETE = 2;
    use AActiveRecord;
    protected $guarded = ['id'];
    public static function getStateOptions()
    {
        return [
            self::STATE_INACTIVE => "Inactive",
            self::STATE_ACTIVE => "Active",
            self::STATE_DELETE => "Delete",
        ];
    }

    public static function getStateOptionsBadge($stateValue)
    {
        $list = [
            self::STATE_ACTIVE => "success",
            self::STATE_INACTIVE => "secondary",
            self::STATE_DELETE => "danger",

        ];
        return isset($stateValue) ? $list[$stateValue] : 'Not Defined';
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
    public function getStateBadgeOption()
    {
        $list = [
            self::STATE_ACTIVE => "success",
            self::STATE_INACTIVE => "secondary",
            self::STATE_DELETE => "danger",
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
                    'url' => url('order/view/'. $model->order_id),

                ];
                break;
            case 'index':
                $menu['add'] = [
                    'label' => 'fa fa-plus',
                    'color' => 'btn btn-primary',
                    'title' => __('Add'),
                    'url' => url('order/item/create'),
                    'visible' => User::isAdmin()
                ];
                $menu['import'] = [
                    'label' => 'fas fa-file-import',
                    'color' => 'btn btn-primary',
                    'title' => __('File Import'),
                    'url' => url('order/item/import'),
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



    public function relationGridView($queryRelation, $request)
    {
        $dataTable = Datatables::of($queryRelation)
        ->addIndexColumn()

        ->addColumn('product_name', function ($data) {
            $productModelJson = json_decode($data->product_json);
            
            return !empty($productModelJson && $productModelJson->name) ? $productModelJson->name : 'N/A';
        })

        ->addColumn('total_amount', function ($data) {
            return !empty($data->total_amount) ?  number_format($data->total_amount, 2)  : 'N/A';
        })
        ->addColumn('unit_amount', function ($data) {
            return !empty($data->unit_amount) ?  number_format($data->unit_amount, 2) : 'N/A';
        })

        ->addColumn('quantity', function ($data) {
            return !empty($data->quantity) ? $data->quantity: 'N/A';
        })



        ->addColumn('created_by', function ($data) {
            return !empty($data->createdBy && $data->createdBy->name) ? $data->createdBy->name : 'N/A';
        })

        ->addColumn('name', function ($data) {
            return !empty($data->name) ? (strlen($data->name) > 60 ? substr(ucfirst($data->name), 0, 60) . '...' : ucfirst($data->name)) : 'N/A';
        })
        ->addColumn('status', function ($data) {
            return '<span class="' . $data->getStateBadgeOption() . '">' . $data->getState() . '</span>';
        })
      
        ->rawColumns(['created_by'])

        ->addColumn('created_at', function ($data) {
            return (empty($data->updated_at)) ? 'N/A' : date('Y-m-d', strtotime($data->updated_at));
        })
        ->addColumn('action', function ($data) {
            $html = '<div class="table-actions text-center">';
            // $html .= ' <a class="btn btn-icon btn-primary mt-1" href="' . url('subscription/plan/edit/' . $data->id) . '" ><i class="fa fa-edit"></i></a>';
            $html .=    '  <a class="btn btn-icon btn-primary mt-1" href="' . url('order/item/view/' . $data->id) . '"  ><i class="fa fa-eye
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
}
