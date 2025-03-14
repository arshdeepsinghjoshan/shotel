<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use DataTables;
use Illuminate\Validation\Rule;

class OrderItemController extends Controller
{
    public $setFilteredRecords = 0;

    public function index()
    {
        try {
            $model = new OrderItem();
            return view('order.order_item.index', compact('model'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
  
    public function view(Request $request)
    {
        try {
            $id = $request->id;
            $model  = OrderItem::find($id);
            if ($model) {

                return view('order.order_item.view', compact('model'));
            } else {
                return redirect('/order')->with('error', 'Product not found');
            }
        } catch (\Exception $e) {
            return redirect('/order')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function update(Request $request)
    {
        if ($this->validator($request->all())->fails()) {
            $message = $this->validator($request->all())->messages()->first();
            return redirect()->back()->withInput()->with('error', $message);
        }
        try {
            $model = Product::find($request->id);
            if (!$model) {
                return redirect()->back()->with('error', 'Product not found');
            }
            $all_images = null;

            if ($request->hasFile('image')) {
                $ticket_images = $request->file('image');
                foreach ($ticket_images as $image) {
                    if ($image->isValid()) {
                        $imageName = rand(1, 100000) . time() . '_' . $image->getClientOriginalName();
                        $image->move(public_path('support_module/ticket_images'), $imageName);
                        $all_images[] =  $imageName;
                    }
                }
                $all_images = json_encode($all_images);
            } else {
                $all_images = $model->image;
            }
            $model->fill($request->all());
            $model->image = $all_images;
            if ($model->save()) {
                return redirect()->back()->with('success', 'Product updated successfully!');
            } else {
                return redirect()->back()->with('error', 'Product not updated');
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    public $s_no = 1;





    public function getList(Request $request, $id = null)
    {
        if (User::isUser()) {
            $query = OrderItem::my()->orderBy('id', 'desc');
        } else {
            $query = Order::orderBy('id', 'desc');
        }

        if (!empty($id))
            $query->where('id', $id);

        return Datatables::of($query)
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
            ->rawColumns(['created_by'])

            ->addColumn('created_at', function ($data) {
                return (empty($data->created_at)) ? 'N/A' : date('Y-m-d', strtotime($data->created_at));
            })
            ->addColumn('department_id', function ($data) {
                return $data->getDepartment ?  $data->getDepartment->title : 'N/A';
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
                'customerClickAble'
            ])

            ->filter(function ($query) {
                if (!empty(request('search')['value'])) {
                    $searchValue = request('search')['value'];
                    $searchTerms = explode(' ', $searchValue);
                    $query->where(function ($q) use ($searchTerms) {
                        foreach ($searchTerms as $term) {
                            $q->where('id', 'like', "%$term%")
                                ->orWhere('name', 'like', "%$term%")
                                ->orWhere('description', 'like', "%$term%")
                                ->orWhere('price', 'like', "%$term%")
                                ->orWhere('hsn_code', 'like', "%$term%")
                                ->orWhere('batch_no', 'like', "%$term%")
                                ->orWhere('agency_name', 'like', "%$term%")
                                ->orWhere('bill_date', 'like', "%$term%")
                                ->orWhere('product_code', 'like', "%$term%")
                                ->orWhere('expiry_date', 'like', "%$term%")
                                ->orWhere('salt', 'like', "%$term%")
                                ->orWhere('created_at', 'like', "%$term%")
                                // ->orWhereHas('getDepartment', function ($query) use ($term) {
                                //     $query->where('title', 'like', "%$term%");
                                // })
                                ->orWhere(function ($query) use ($term) {
                                    $query->searchState($term);
                                })
                                ->orWhere(function ($query) use ($term) {
                                    $query->searchPriority($term);
                                })
                                ->orWhereHas('createdBy', function ($query) use ($term) {
                                    $query->where('name', 'like', "%$term%");
                                });
                        }
                    });
                }
            })
            ->make(true);
    }













    public function stateChange($id, $state)
    {
        try {
            $model = Product::find($id);
            if ($model) {
                $update = $model->update([
                    'state_id' => $state,
                ]);
                return redirect()->back()->with('success', 'Product has been ' . (($model->getState() != "New") ? $model->getState() . 'd!' : $model->getState()));
            } else {
                return redirect('404');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function finalDelete($id)
    {
        try {
            $model = Product::find($id);
            if ($model) {
                $model->delete();
                return redirect('support')->with('success', 'Product has been deleted successfully!');
            } else {
                return redirect('404');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
