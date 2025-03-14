<?php

namespace Modules\Smtp\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Smtp\Http\Models\MailConfiguration;

class MailConfigurationController extends Controller
{
    public function index(Request $request)
    {

        $model  = new MailConfiguration();

        return view('smtp::account.index', compact('model'));
    }
    public function view(Request $request, $id)
    {
        try {
            $model = MailConfiguration::find($id);
            if ($model) {

                return view('smtp::account.view', compact('model'));
            } else {
                return redirect('/user')->with('error', 'User does not exist');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function getSmtpAccountList(Request $request)
    {

        $query  = MailConfiguration::orderBy('id', 'DESC');
        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('status', function ($data) {
                $select = '<select class="form-select state-change"  data-id="' . $data->id . '" data-modeltype="' . MailConfiguration::class . '" aria-label="Default select example">';
                foreach ($data->getStateOptions() as $key => $option) {
                    $select .= '<option value="' . $key . '"' . ($data->state_id == $key ? ' selected' : '') . '>' . $option . '</option>';
                }
                $select .= '</select>';
                return $select;
            })
            ->addColumn('created_by', function ($data) {
                return !empty($data->createdBy && $data->createdBy->name) ? $data->createdBy->name : 'N/A';
            })
            ->addColumn('created_at', function ($data) {
                return (empty($data->created_at)) ? 'N/A' : date('Y-m-d', strtotime($data->created_at));
            })

            ->addColumn('action', function ($data) {
                $html = '<div class="table-actions text-center">';
                $html .= ' <a class="btn btn-icon btn-primary mt-1" href="' . url('email-queue/account/edit/' . $data->id) . '" ><i class="fa fa-edit"></i></a>';
                $html .=    '  <a class="btn btn-icon btn-primary mt-1" href="' . url('email-queue/account/view/' . $data->id) . '"  ><i class="fa fa-eye
                        "data-toggle="tooltip"  title="View"></i></a>';
                $html .=  '</div>';
                return $html;
            })->addColumn('customerClickAble', function ($data) {
                $html = 0;

                return $html;
            })
            ->rawColumns(['action', 'customerClickAble', 'status'])
            ->filter(function ($query) {
                $searchValue = request('search.value');
                if (!empty($searchValue)) {
                    $query->where(function ($q) use ($searchValue) {
                        $q->where('id', 'like', "%$searchValue%")
                            ->orWhere('mailer', 'like', "%$searchValue%")
                            ->orWhere('host', 'like', "%$searchValue%")
                            ->orWhere('port', 'like', "%$searchValue%")
                            ->orWhere('username', 'like', "%$searchValue%")
                            ->orWhere('encryption', 'like', "%$searchValue%")
                            ->orWhere('from_address', 'like', "%$searchValue%")
                            ->orWhere('created_at', 'like', "%$searchValue%")
                            ->orWhere(function ($query) use ($searchValue) {
                                $query->searchState($searchValue);
                            });
                    });
                }
            })
            ->make(true);
    }

    public function add(Request $request)
    {
        $model  = new MailConfiguration();
        return view('smtp::account.add', compact('model'));
    }

    public function edit(Request $request, $id)
    {
        try {
            $model = MailConfiguration::find($id);
            if ($model) {
                return view('smtp::account.update', compact('model'));
            } else {
                return redirect('/email-queue/account')->with('error', 'Account does not exist');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }

        $model  = MailConfiguration::find($id);
        return view('smtp::account.update', compact('model'));
    }


    protected static function validator(array $data)
    {
        return Validator::make($data, [
            'mailer' => ['required'],
            'host' => ['required'],
            'port' => 'required|digits:3',
            'username' => ['required'],
            'password' => ['required'],
            'encryption' => ['required'],
            'from_address' => ['required']
        ]);
    }


    public function store(Request $request)
    {
        if ($this->validator($request->all())->fails()) {
            $message = $this->validator($request->all())->messages()->first();
            return redirect()->back()->withInput()->with('error', $message);
        }
        $model = new MailConfiguration();
        $model->fill($request->all());
        $model->state_id =  MailConfiguration::STATE_INACTIVE;
        $model->created_by_id =  Auth::id();
        $model->from_name =  env('APP_NAME', false);
        if ($model->save()) {
            return redirect('/email-queue/account')->with('success', 'Your account has been created successfully!');
        }
    }

    protected function update(Request $request)
    {
        $model = MailConfiguration::Find($request->id);

        if ($this->validator($request->all())->fails()) {
            $message = $this->validator($request->all())->messages()->first();
            return redirect()->back()->withInput()->with('error', $message);
        }
        if (empty($model)) {
            return redirect('/email-queue/account')->with('error', 'SMTP account not found!');
        }
        $model->fill($request->all());
        if ($model->save()) {
            return redirect('/email-queue/account')->with('success', 'Your account has been updated successfully!');
        }
    }

}
