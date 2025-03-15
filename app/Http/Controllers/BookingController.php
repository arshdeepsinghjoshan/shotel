<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use DataTables;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public $setFilteredRecords = 0;

    public function index()
    {
        try {
            $model = new Booking();
            return view('booking.index', compact('model'));
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
                        empty($productData['name']) ||
                        empty($productData['price'])
                    ) {
                        continue; // Skip
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
                Booking::insert($productsToInsert);

                return redirect()->back()->with('success', 'File imported successfully! Products added: ' . count($productsToInsert));
            }

            // For GET request
            $model = new Booking();
            return view('booking.import', compact('model'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function edit(Request $request)
    {
        try {
            $id = $request->id;
            $model  = Booking::find($id);
            if ($model) {

                return view('booking.update', compact('model'));
            } else {
                return redirect()->back()->with('error', 'Booking not found');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }



    public function create(Request $request)
    {
        try {

            $model  = new Booking();
            if ($model) {

                return view('booking.add', compact('model'));
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
            $model  = Booking::find($id);
            if ($model) {

                return view('booking.view', compact('model'));
            } else {
                return redirect('/product')->with('error', 'Booking not found');
            }
        } catch (\Exception $e) {
            return redirect('/product')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function update(Request $request)
    {
        if ($this->validator($request->all())->fails()) {
            $message = $this->validator($request->all())->messages()->first();
            return redirect()->back()->withInput()->with('error', $message);
        }
        try {
            $model = Booking::find($request->id);
            if (!$model) {
                return redirect()->back()->with('error', 'Booking not found');
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
                return redirect()->back()->with('success', 'Booking updated successfully!');
            } else {
                return redirect()->back()->with('error', 'Booking not updated');
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
            $query = Booking::my()->orderBy('id', 'desc');
        } else {
            $query = Booking::orderBy('id', 'desc');
        }

        if (!empty($id))
            $query->where('id', $id);

        $state_id = request('state_id');
        if ($state_id) {
            $query->where('state_id', $state_id);
        }

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('select', function ($data) {
                $checked = $data->cart ? 'checked' : ''; // If the relationship exists, mark as checked
                return '<input class="form-check-input select-product" data-product_id="' . $data->id . '" type="checkbox" ' . $checked . ' value="" >';
            })
            ->addColumn('created_by', function ($data) {
                return !empty($data->createdBy && $data->createdBy->name) ? $data->createdBy->name : 'N/A';
            })
            ->addColumn('title', function ($data) {
                return !empty($data->title) ? (strlen($data->title) > 60 ? substr(ucfirst($data->title), 0, 60) . '...' : ucfirst($data->title)) : 'N/A';
            })
            ->addColumn('price', function ($data) {
                return number_format($data->price, 2);
            })
            ->addColumn('status', function ($data) {
                return '<span class="' . $data->getStateBadgeOption() . '">' . $data->getState() . '</span>';
            })
            ->rawColumns(['created_by'])

            ->addColumn('created_at', function ($data) {
                return (empty($data->created_at)) ? 'N/A' : date('Y-m-d', strtotime($data->created_at));
            })
            ->addColumn('status', function ($data) {
                $select = '<select class="form-select state-change"  data-id="' . $data->id . '" data-modeltype="' . Booking::class . '" aria-label="Default select example">';
                foreach ($data->getStateOptions() as $key => $option) {
                    $select .= '<option value="' . $key . '"' . ($data->state_id == $key ? ' selected' : '') . '>' . $option . '</option>';
                }
                $select .= '</select>';
                return $select;
            })

            ->addColumn('action', function ($data) {
                $html = '<div class="table-actions text-center">';
                $html .=    '  <a class="btn btn-icon btn-primary mt-1" href="' . url('product/view/' . $data->id) . '"  ><i class="fa fa-eye
                "data-toggle="tooltip"  title="View"></i></a>';
                $html .= ' <a class="btn btn-icon btn-primary mt-1" href="' . url('product/edit/' . $data->id) . '" ><i class="fa fa-edit"></i></a>';
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
                'select'
            ])

            ->filter(function ($query) {
                if (!empty(request('search')['value'])) {

                    $searchValue = request('search')['value'];
                    $searchTerms = explode(' ', $searchValue);
                    $query->where(function ($q) use ($searchTerms) {
                        foreach ($searchTerms as $term) {
                            $q->where('id', 'like', "%$term%")
                                ->orWhere('name', 'like', "%$term%")
                                ->orWhere('price', 'like', "%$term%")
                                ->orWhere('quantity_in_stock', 'like', "%$term%")
                                ->orWhere('remaining_quantity', 'like', "%$term%")
                                ->orWhere('created_at', 'like', "%$term%")
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
                'description' => 'string|max:255',
                'remaining_quantity' => ['integer', 'max:' . $data['quantity_in_stock']],
            ]
        );
    }

    public function add(Request $request)
    {
        try {
            // Validation
            if ($this->validator($request->all())->fails()) {
                $message = $this->validator($request->all())->messages()->first();
                return redirect()->back()->withInput()->with('error', $message);
            }

            // Initialize the images array
            $all_images = [];
            $ticket_images = $request->file('images');

            // Check if images were uploaded
            if ($request->hasFile('images')) {
                foreach ($ticket_images as $image) {
                    if ($image->isValid()) {
                        $imageName = rand(1, 100000) . time() . '_' . $image->getClientOriginalName();
                        $image->move(public_path('products'), $imageName);
                        $all_images[] = $imageName;
                    }
                }
            }

            // Create a new product model
            $model = new Booking();
            $model->fill($request->all());
            $model->state_id = Booking::STATE_ACTIVE;
            $model->images = !empty($all_images) ? json_encode($all_images) : null;  // Ensure it's a JSON string
            $model->created_by_id = Auth::user()->id;

            // Save the model
            if ($model->save()) {
                return redirect('/product')->with('success', 'Booking created successfully!');
            } else {
                return redirect('/product/create')->with('error', 'Unable to save the Product!');
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->withInput()->with('error', $bug);
        }
    }



    public function stateChange($id, $state)
    {
        try {
            $model = Booking::find($id);
            if ($model) {
                $update = $model->update([
                    'state_id' => $state,
                ]);
                return redirect()->back()->with('success', 'Booking has been ' . (($model->getState() != "New") ? $model->getState() . 'd!' : $model->getState()));
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
            $model = Booking::find($id);
            if ($model) {
                $model->delete();
                return redirect('support')->with('success', 'Booking has been deleted successfully!');
            } else {
                return redirect('404');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
