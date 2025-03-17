<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use DataTables;
use Illuminate\Validation\Rule;

class ReservationController extends Controller
{
    public $setFilteredRecords = 0;

    public function index()
    {
        try {
            $model = new Reservation();
            return view('reservation.index', compact('model'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }



    public function edit(Request $request)
    {
        try {
            $id = $request->id;
            $model  = Reservation::find($id);
            if ($model) {

                return view('reservation.update', compact('model'));
            } else {
                return redirect()->back()->with('error', 'Reservation not found');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }



    public function create(Request $request)
    {
        try {

            $model  = new Reservation();
            if ($model) {

                return view('reservation.add', compact('model'));
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
            $model  = Reservation::find($id);
            if ($model) {

                return view('reservation.view', compact('model'));
            } else {
                return redirect('/reservation')->with('error', 'Reservation not found');
            }
        } catch (\Exception $e) {
            return redirect('/reservation')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function update(Request $request)
    {
        if ($this->validator($request->all())->fails()) {
            $message = $this->validator($request->all())->messages()->first();
            return redirect()->back()->withInput()->with('error', $message);
        }

        try {
            $model = Reservation::find($request->id);
            if (!$model) {
                return redirect()->back()->with('error', 'Reservation not found');
            }

            // ✅ Check availability only if table or reservation time has changed
            if (($request->table_id != $model->table_id || $request->reservation_time != $model->reservation_time)
                && !Reservation::isTableAvailable($request->table_id, $request->reservation_time)
            ) {
                return redirect()->back()->withInput()->with('error', 'This table is already booked at this time.');
            }

            $model->fill($request->all());

            if ($model->save()) {
                return redirect()->back()->with('success', 'Reservation updated successfully!');
            } else {
                return redirect()->back()->with('error', 'Reservation not updated');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public $s_no = 1;





    public function getList(Request $request, $id = null)
    {
        $query = Reservation::with(['user', 'table'])->orderBy('id', 'desc');
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
            ->addColumn('user', function ($data) {
                return !empty($data->user && $data->user->name) ? $data->user->name : 'N/A';
            })

            ->addColumn('table', function ($data) {
                return !empty($data->table && $data->table->table_number) ? $data->table->table_number : 'N/A';
            })
            ->addColumn('status', function ($data) {
                return '<span class="' . $data->getStateBadgeOption() . '">' . $data->getState() . '</span>';
            })
            ->rawColumns(['created_by'])
            ->addColumn('reservation_time', function ($data) {
                return (empty($data->reservation_time)) ? 'N/A' : date('Y-m-d  h:i:s A', strtotime($data->reservation_time));
            })
            ->addColumn('created_at', function ($data) {
                return (empty($data->created_at)) ? 'N/A' : date('Y-m-d', strtotime($data->created_at));
            })
            ->addColumn('status', function ($data) {
                $select = '<select class="form-select state-change"  data-id="' . $data->id . '" data-modeltype="' . Reservation::class . '" aria-label="Default select example">';
                foreach ($data->getStateOptions() as $key => $option) {
                    $select .= '<option value="' . $key . '"' . ($data->state_id == $key ? ' selected' : '') . '>' . $option . '</option>';
                }
                $select .= '</select>';
                return $select;
            })

            ->addColumn('action', function ($data) {
                $html = '<div class="table-actions text-center">';
                $html .=    '  <a class="btn btn-icon btn-primary mt-1" href="' . url('reservation/view/' . $data->id) . '"  ><i class="fa fa-eye
                "data-toggle="tooltip"  title="View"></i></a>';
                $html .= ' <a class="btn btn-icon btn-primary mt-1" href="' . url('reservation/edit/' . $data->id) . '" ><i class="fa fa-edit"></i></a>';
                $html .=  '</div>';
                return $html;
            })
            ->rawColumns([
                'action',
                'created_at',
                'status'
            ])

            ->filter(function ($query) {
                if (!empty(request('search')['value'])) {

                    $searchValue = request('search')['value'];
                    $searchTerms = explode(' ', $searchValue);
                    $query->where(function ($q) use ($searchTerms) {
                        foreach ($searchTerms as $term) {
                            $q->where('id', 'like', "%$term%")
                                ->orWhere('created_at', 'like', "%$term%")
                                ->orWhere(function ($query) use ($term) {
                                    $query->searchState($term);
                                })
                                ->orWhereHas('createdBy', function ($query) use ($term) {
                                    $query->where('name', 'like', "%$term%");
                                })
                                ->orWhereHas('user', function ($query) use ($term) {
                                    $query->where('name', 'like', "%$term%");
                                })
                                ->orWhereHas('table', function ($query) use ($term) {
                                    $query->where('table_number', 'like', "%$term%");
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
                'user_id' => 'required|exists:users,id',
                'table_id' => 'required|exists:tables,id',
                'reservation_time' => 'required|date|after:now',
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
            if (!Reservation::isTableAvailable($request->table_id, $request->reservation_time)) {
                return redirect()->back()->withInput()->with('error', 'This table is already booked at this times.');
            }
            $model = new Reservation();
            $model->fill($request->all());
            $model->created_by_id = Auth::user()->id;
            // Save the model
            if ($model->save()) {
                return redirect('/reservation')->with('success', 'Reservation created successfully!');
            } else {
                return redirect('/reservation/create')->with('error', 'Unable to save the Reservation!');
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->withInput()->with('error', $bug);
        }
    }



    public function stateChange($id, $state)
    {
        try {
            $model = Reservation::find($id);
            if ($model) {
                $update = $model->update([
                    'state_id' => $state,
                ]);
                return redirect()->back()->with('success', 'Reservation has been ' . (($model->getState() != "New") ? $model->getState() . 'd!' : $model->getState()));
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
            $model = Reservation::find($id);
            if ($model) {
                $model->delete();
                return redirect('support')->with('success', 'Reservation has been deleted successfully!');
            } else {
                return redirect('404');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
