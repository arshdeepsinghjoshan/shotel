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
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class OrderController extends Controller
{
    public $setFilteredRecords = 0;

    public function generatePDF(Request $request, $encodedId)
    {
        try {
            $id = Crypt::decryptString($encodedId);
        } catch (DecryptException $e) {
            return redirect()->route('order')->with('error', 'Invalid request. Unable to decrypt the ID.');
        } catch (\Exception $e) {
            return redirect()->route('order')->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
        $model = Order::find($id);
        if (!$model) {
            return redirect()->route('order')->with('error', 'Order not found.');
        }
        try {
            // Generate the PDF
            $pdf = PDF::loadView('pdf.order_invoice', compact('model'));
            return $pdf->stream('invoice.pdf');
        } catch (\Exception $e) {
            return redirect()->route('order')->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }


    public function index()
    {
        try {
            $model = new Order();
            return view('order.index', compact('model'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function import(Request $request)
    {
        try {
            if ($request->isMethod('post')) {
                // Validate the file input
                $request->validate([
                    'file' => 'required|mimes:xlsx,xls,csv|max:2048', // Ensure it's an Excel or CSV file
                ]);

                // Handle file upload
                $file = $request->file('file');
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray();

                // Validate headers
                $requiredColumns = ['name', 'product_code', 'hsn_code', 'price'];
                $headers = $data[0]; // First row as headers
                $missingColumns = array_diff($requiredColumns, $headers);

                if (!empty($missingColumns)) {
                    return redirect()->back()->with('error', 'Missing columns: ' . implode(', ', $missingColumns));
                }

                // Process rows
                $rows = array_slice($data, 1); // Skip header row
                $productsToInsert = [];

                foreach ($rows as $row) {
                    $productData = array_combine($headers, $row); // Map headers to row values

                    // Validate required fields in rows
                    if (
                        empty($productData['name']) || empty($productData['product_code']) ||
                        empty($productData['hsn_code']) || empty($productData['price'])
                    ) {
                        return redirect()->back()->with('error', 'Missing required fields in one or more rows.');
                    }
                    $now = now();
                    $productsToInsert[] = [
                        'name' => $productData['name'],
                        'product_code' => $productData['product_code'] ?? '',
                        'hsn_code' => $productData['hsn_code'],
                        'description' => $productData['description'] ?? '', // Default empty string
                        'salt' => $productData['salt'] ?? '', // Default empty string
                        'tax_id' => $productData['tax_id'] ?? '', // Default empty string
                        'batch_no' => $productData['batch_no'] ?? '', // Default empty string
                        'agency_name' => $productData['agency_name'] ?? '', // Default empty string
                        'price' => (float) $productData['price'],
                        'category_id' => $productData['category_id'] ?? null,
                        'created_by_id' => Auth::id() ?? null,
                        'expiry_date' => $productData['expiry_date'] ?? null,
                        'bill_date' => $productData['bill_date'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // Insert into database
                Product::insert($productsToInsert);

                return redirect()->back()->with('success', 'File imported successfully! Products added: ' . count($productsToInsert));
            }

            // For GET request
            $model = new Product();
            return view('order.import', compact('model'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function orderInvoice($id)
    {
        // try {

        $data = Order::where('id', $id)->first();
        // $this->checkOwner($data, 'user_id');
        if ($data) {
            $pdf = PDf::loadView('invoice.order', compact('data'));
            return $pdf->stream('sas.pdf');
        } else {
            return redirect('404');
        }
        // } catch (\Exception $e) {
        //     return redirect()->back()->with('error', 'An error occurred while generating the invoice. ' . $e->getMessage());
        // }
    }

    public function getSalesData(Request $request)
    {
        $model = Order::findActive('order_payment_status')->get();

        // Calculate total sales for each day
        $totalSalesData = $model->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('Y-m-d');
        })->map(function ($day) {
            return $day->sum(function ($model) {
                return $model->total_amount;
            });
        });

        $salesData = $totalSalesData->map(function ($totalSales, $date) {
            return [
                'date' => $date,
                'totalSales' => $totalSales
            ];
        })->values()->toArray();

        return response()->json($salesData);
    }
    public function edit(Request $request)
    {
        try {
            $id = $request->id;
            $model  = Product::find($id);
            if ($model) {

                return view('order.update', compact('model'));
            } else {
                return redirect()->back()->with('error', 'Product not found');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }



    public function create(Request $request)
    {
        try {

            $model  = new Order();
            if ($model) {
                return view('order.add', compact('model'));
            } else {
                return redirect('404');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function view(Request $request)
    {
        try {
            $id = $request->id;
            $model  = Order::find($id);
            if ($model) {

                return view('order.view', compact('model'));
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
            $query = Order::my()->orderBy('id', 'desc');
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
                                ->orWhere('order_number', 'like', "%$term%")
                                ->orWhere('total_amount', 'like', "%$term%")
                                ->orWhere('created_at', 'like', "%$term%")
                                // ->orWhereHas('getDepartment', function ($query) use ($term) {
                                //     $query->where('title', 'like', "%$term%");
                                // })
                                ->orWhere(function ($query) use ($term) {
                                    $query->searchState($term);
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











    protected static function validator(array $data, $id = null)
    {
        return Validator::make(
            $data,
            [
                'name' => 'required|string|max:255',
                'price' => 'required',
                'hsn_code' => 'required',
                'batch_no' => 'required|string|max:255'
            ],
            [
                'title.required' => 'The subject field is required.',
                'title.max' => 'The subject field must not exceed 255 characters.',
                'department_id.required' => 'The department field is required.',
                'priority_id.required' => 'The priority field is required.',
                'message.required' => 'The message field is required.',
                'message.max' => 'The message field must not exceed 255 characters.'
            ]
        );
    }


    protected static function orderPlaceValidator(array $data, $id = null)
    {
        return Validator::make(
            $data,
            [
                'user_id' => 'required|exists:users,id',
            ]
        );
    }
    public function add(Request $request)
    {
        $buy_user_id = $request->user_id;
        $validator = $this->orderPlaceValidator($request->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->messages()->first(),
            ]);
        }

        try {
            $userId = Auth::id();
            DB::beginTransaction();

            // Fetch cart items for the authenticated user
            $cartItems = Cart::where('created_by_id', $userId)->with('product')->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'No items in the cart to place an order!',
                ]);
            }

            // Calculate total order price
            $totalPrice = $cartItems->sum(function ($item) {
                return $item->quantity * $item->unit_price;
            });

            // Create a new order
            $order = new Order();
            $order->created_by_id = $userId;
            $order->user_id = $buy_user_id;
            $order->state_id = Order::STATE_PENDING;
            $order->total_amount = $totalPrice;
            $order->created_by_id = $userId;
            $order->generateOrderNumber();

            if ($order->save()) {
                foreach ($cartItems as $cartItem) {
                    $product = Product::find($cartItem->product_id);

                    if (!$product) {
                        if (!$cartItem->custom_product) {
                            DB::rollBack();
                            return response()->json([
                                'status' => 422,
                                'message' => 'Product not found for cart item!',
                            ]);
                        } else {
                            $product = new Product();
                            $product->name = $cartItem->custom_product;
                        }
                    }
                    if ($cartItem->product_id != 0) {
                        // Check if there is enough stock before placing the order
                        if ($product->remaining_quantity < $cartItem->quantity) {
                            DB::rollBack();
                            return response()->json([
                                'status' => 422,
                                'message' => 'Insufficient stock for product: ' . $product->name,
                            ]);
                        }

                        // Reduce stock quantity
                        $product->decrement('remaining_quantity', $cartItem->quantity);
                    }
                    // Create order item
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $cartItem->product_id ?? 0,
                        'product_json' => json_encode($product),
                        'quantity' => $cartItem->quantity,
                        'total_amount' => $cartItem->total_price,
                        'unit_amount' => $cartItem->unit_price,
                        'user_id' => $order->user_id,
                        'created_by_id' => $userId,
                    ]);
                }

                // Clear the user's cart
                Cart::where('created_by_id', $userId)->delete();
                DB::commit();

                return response()->json([
                    'status' => 200,
                    'message' => 'Order placed successfully!',
                    'order_id' => $order->id,
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'status' => 422,
                    'message' => 'Something went wrong while placing the order!',
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ]);
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
