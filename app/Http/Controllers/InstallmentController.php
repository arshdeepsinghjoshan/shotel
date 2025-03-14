<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Installment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use DataTables;
use Illuminate\Validation\Rule;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InstallmentController extends Controller
{
    public $setFilteredRecords = 0;

    public function index()
    {
        try {
            $model = new Installment();
            return view('installment.index', compact('model'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function getList(Request $request, $id = null)
    {
        if (User::isUser()) {
            $query = Installment::my()->with(['order', 'createdBy'])->orderBy('id', 'desc');
        } else {
            $query = Installment::with(['order', 'createdBy'])->orderBy('id', 'desc');
        }

        if (!empty($id))
            $query->where('id', $id);

        return Datatables::of($query)
            ->addIndexColumn()

            ->addColumn('created_by', function ($data) {
                return !empty($data->createdBy && $data->createdBy->name) ? $data->createdBy->name : 'N/A';
            })
            ->addColumn('order', function ($data) {
                return !empty($data->order && $data->order->order_number) ? $data->order->order_number : 'N/A';
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
            ->addColumn('updated_at', function ($data) {
                return (empty($data->updated_at)) ? 'N/A' : date('Y-m-d', strtotime($data->updated_at));
            })
            ->addColumn('department_id', function ($data) {
                return $data->getDepartment ?  $data->getDepartment->title : 'N/A';
            })
            ->addColumn('action', function ($data) {
                $html = '<div class="table-actions text-center">';
                // $html .= ' <a class="btn btn-icon btn-primary mt-1" href="' . url('support/edit/' . $data->id) . '" ><i class="fa fa-edit"></i></a>';
                $html .=    '  <a class="btn btn-icon btn-primary mt-1" href="' . url('installment/view/' . $data->id) . '"  ><i class="fa fa-eye
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
                'customerClickAble',
                'payment_status'
            ])

            ->filter(function ($query) {
                if (!empty(request('search')['value'])) {
                    $searchValue = request('search')['value'];
                    $searchTerms = explode(' ', $searchValue);
                    $query->where(function ($q) use ($searchTerms) {
                        foreach ($searchTerms as $term) {
                            $q->where('id', 'like', "%$term%")
                                ->orWhere('amount', 'like', "%$term%")
                                ->orWhere('created_at', 'like', "%$term%")
                                // ->orWhereHas('getDepartment', function ($query) use ($term) {
                                //     $query->where('title', 'like', "%$term%");
                                // })
                                ->orWhere(function ($query) use ($term) {
                                    $query->searchState($term);
                                })
                                ->orWhereHas('createdBy', function ($query) use ($term) {
                                    $query->where('name', 'like', "%$term%");
                                }) ->orWhereHas('order', function ($query) use ($term) {
                                    $query->where('order_number', 'like', "%$term%");
                                });;
                        }
                    });
                }
            })
            ->make(true);
    }


    public function store(Request $request)
    {
        try {

            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'amount' => 'required|numeric|min:1',
                'user_id' => 'required|exists:users,id',
                'payment_method' => 'nullable|string'
            ]);

            $order = Order::find($request->order_id);

            if ($order->order_payment_status == 1) {
                return response()->json(['message' => 'Already Paid Amount'], 400);
            }
            if ($order->remainingAmount() < $request->amount) {
                return response()->json(['message' => 'Payment exceeds the remaining amount'], 400);
            }

            $installment = Installment::create([
                'order_id' => $request->order_id,
                'amount' => $request->amount,
                'created_by_id' => $request->user_id,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Installment added successfully',
                'paid_amount' => $order->paidAmount(),
                'remaining_amount' => $order->remainingAmount(),
            ]);
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'status' => 422,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }



    public function view(Request $request)
    {
        try {
            $id = $request->id;
            $model  = Installment::find($id);
            if ($model) {

                return view('installment.view', compact('model'));
            } else {
                return redirect('/installment')->with('error', 'Product not found');
            }
        } catch (\Exception $e) {
            return redirect('/installment')->with('error', 'An error occurred: ' . $e->getMessage());
        }
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
