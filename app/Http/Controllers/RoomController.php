<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Product;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use DataTables;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    public $setFilteredRecords = 0;

    public function index()
    {
        try {
            $model = new Room();
            return view('room.index', compact('model'));
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
                Room::insert($productsToInsert);

                return redirect()->back()->with('success', 'File imported successfully! Products added: ' . count($productsToInsert));
            }

            // For GET request
            $model = new Room();
            return view('room.import', compact('model'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function edit(Request $request)
    {
        try {
            $id = $request->id;
            $model  = Room::find($id);
            if ($model) {

                return view('room.update', compact('model'));
            } else {
                return redirect()->back()->with('error', 'Room not found');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }



    public function create(Request $request)
    {
        try {

            $model  = new Room();
            if ($model) {

                return view('room.add', compact('model'));
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
            $model  = Room::find($id);
            if ($model) {

                return view('room.view', compact('model'));
            } else {
                return redirect('/room')->with('error', 'Room not found');
            }
        } catch (\Exception $e) {
            return redirect('/room')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function update(Request $request)
    {
        if ($this->validator($request->all(), $request->id)->fails()) {
            $message = $this->validator($request->all(), $request->id)->messages()->first();
            return redirect()->back()->withInput()->with('error', $message);
        }
        try {
            $model = Room::find($request->id);
            if (!$model) {
                return redirect()->back()->with('error', 'Room not found');
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
                return redirect()->back()->with('success', 'Room updated successfully!');
            } else {
                return redirect()->back()->with('error', 'Room not updated');
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
            $query = Room::my()->orderBy('id', 'desc');
        } else {
            $query = Room::orderBy('id', 'desc');
        }

        if (!empty($id))
            $query->where('id', $id);

        $state_id = request('state_id');
        if ($state_id) {
            $query->where('state_id', $state_id);
        }

        return Datatables::of($query)
            ->addIndexColumn()
         
            ->addColumn('created_by', function ($data) {
                return !empty($data->createdBy && $data->createdBy->name) ? $data->createdBy->name : 'N/A';
            })
            ->addColumn('note', function ($data) {
                return !empty($data->note) ? (strlen($data->note) > 60 ? substr(ucfirst($data->note), 0, 60) . '...' : ucfirst($data->note)) : 'N/A';
            })
            ->addColumn('price', function ($data) {
                return number_format($data->price, 2);
            })
            ->addColumn('status', function ($data) {
                return '<span class="' . $data->getStateBadgeOption() . '">' . $data->getState() . '</span>';
            })

            ->addColumn('type', function ($data) {
                return  $data->getType();
            })


            ->addColumn('ac_type', function ($data) {
                return $data->getAcType();
            })


            ->addColumn('meal', function ($data) {
                return $data->getMealType();
            })

            ->rawColumns(['created_by'])

            ->addColumn('created_at', function ($data) {
                return (empty($data->created_at)) ? 'N/A' : date('Y-m-d', strtotime($data->created_at));
            })
            ->addColumn('status', function ($data) {
                $select = '<select class="form-select state-change"  data-id="' . $data->id . '" data-modeltype="' . Room::class . '" aria-label="Default select example">';
                foreach ($data->getStateOptions() as $key => $option) {
                    $select .= '<option value="' . $key . '"' . ($data->state_id == $key ? ' selected' : '') . '>' . $option . '</option>';
                }
                $select .= '</select>';
                return $select;
            })

            ->addColumn('action', function ($data) {
                $html = '<div class="table-actions text-center">';
                $html .=    '  <a class="btn btn-icon btn-primary mt-1" href="' . url('room/view/' . $data->id) . '"  ><i class="fa fa-eye
                "data-toggle="tooltip"  title="View"></i></a>';
                $html .= ' <a class="btn btn-icon btn-primary mt-1" href="' . url('room/edit/' . $data->id) . '" ><i class="fa fa-edit"></i></a>';
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
                                ->orWhere('room_number', 'like', "%$term%")
                                ->orWhere('note', 'like', "%$term%")
                                ->orWhere('capacity', 'like', "%$term%")
                                ->orWhere('price', 'like', "%$term%")
                                ->orWhere('created_at', 'like', "%$term%")
                                ->orWhere(function ($query) use ($term) {
                                    $query->searchState($term);
                                })
                                ->orWhere(function ($query) use ($term) {
                                    $query->searchType($term);
                                })
                                ->orWhere(function ($query) use ($term) {
                                    $query->searchMeal($term);
                                })
                                ->orWhere(function ($query) use ($term) {
                                    $query->searchACType($term);
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
                'room_number' => 'required|string|max:50|unique:rooms,room_number,' . $id,
                'price' => 'required|numeric|min:0',

                // 0=>Single, 1=> Double, 2=> Villa, 3=> Deluxe, 4=> Super Deluxe
                'type_id' => 'required|integer|in:0,1,2,3,4',

                // Meal type: 0=>None, 1=>Breakfast, 2=>Half Board, 3=>Full Board
                'meal_type' => ['required', 'integer', 'in:0,1,2,3'],

                // AC Type: 0=>Non-AC, 1=>AC, 2=>Central AC, 3=>Split AC
                'ac_type' => ['required', 'integer', 'in:0,1,2,3'],

                // State: 0=>Available, 1=>Booked, 2=>Under Maintenance, 3=>Out of Order
                'state_id' => ['required', 'integer', 'in:0,1,2,3'],

                // Capacity should be a positive integer (e.g., max 10 people)
                'capacity' => ['required', 'integer', 'min:1', 'max:10'],

                // Room images (optional but must be an image if provided)
                'images' => ['nullable', 'array'],
                'images.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // max 2MB per image

                // Room description (optional)
                'description' => ['nullable', 'string', 'max:1000'],
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

            // Create a new Room model
            $model = new Room();
            $model->fill($request->all());
            $model->images = !empty($all_images) ? json_encode($all_images) : null;  // Ensure it's a JSON string
            $model->created_by_id = Auth::user()->id;

            // Save the model
            if ($model->save()) {
                return redirect('/room')->with('success', 'Room created successfully!');
            } else {
                return redirect('/room/create')->with('error', 'Unable to save the Room!');
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->withInput()->with('error', $bug);
        }
    }



    public function stateChange($id, $state)
    {
        try {
            $model = Room::find($id);
            if ($model) {
                $update = $model->update([
                    'state_id' => $state,
                ]);
                return redirect()->back()->with('success', 'Room has been ' . (($model->getState() != "New") ? $model->getState() . 'd!' : $model->getState()));
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
            $model = Room::find($id);
            if ($model) {
                $model->delete();
                return redirect('support')->with('success', 'Room has been deleted successfully!');
            } else {
                return redirect('404');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
