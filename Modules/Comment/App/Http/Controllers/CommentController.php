<?php

namespace Modules\Comment\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Comment\Models\Comment;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function index(Request $request)
    {

        $model  = new Comment();
        return view('comment::comment.index', compact('model'));
    }
    public function view(Request $request, $id)
    {
        try {
            $model = Comment::find($id);
            if ($model) {

                return view('comment::comment.view', compact('model'));
            } else {
                return redirect('/user')->with('error', 'User does not exist');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function getList(Request $request)
    {

        $query  = Comment::orderBy('id', 'DESC');

        if (User::isUser())
            $query->my();

        return Datatables::of($query)
            ->addIndexColumn()


            ->addColumn('status', function ($data) {

                if (User::isAdmin()) {
                    $select = '<select class="form-select state-change"  data-id="' . $data->id . '" data-modeltype="' . Comment::class . '" aria-label="Default select example">';
                    foreach ($data->getStateOptions() as $key => $option) {
                        $select .= '<option value="' . $key . '"' . ($data->state_id == $key ? ' selected' : '') . '>' . $option . '</option>';
                    }
                    $select .= '</select>';
                    return $select;
                } else {
                    return '<span class="badge badge-' . $data->getStateBadgeOption() . '">' . $data->getState() . '</span>';
                }
            })
            ->addColumn('created_by', function ($data) {
                return !empty($data->createdBy && $data->createdBy->name) ? $data->createdBy->name : 'N/A';
            })
            ->addColumn('created_at', function ($data) {
                return (empty($data->created_at)) ? 'N/A' : date('Y-m-d', strtotime($data->created_at));
            })

            ->addColumn('action', function ($data) {
                $html = '<div class="table-actions text-center">';
                $html .=    '  <a class="btn btn-icon btn-primary mt-1" href="' . url('comment/view/' . $data->id) . '"  ><i class="fa fa-eye
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

    protected static function validator(array $data, $id = null)
    {
        $rules = [
            "comment" => "required|string",
            "model_type" => "required",
            "model_id" => "required",
        ];

        return Validator::make($data, $rules);
    }

    public function add(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($this->validator($request->all())->fails()) {
                $message = $this->validator($request->all())->messages()->first();
                return redirect()->back()->withInput()->with('error', $message);
            }
            $model = new Comment();
            $model->fill($request->all());
            $model->created_by_id = Auth::id();
            if ($model->save()) {
                if ($request->file) {
                    $model->file = $this->imageUpload($request, "file", '/public/uploads');
                }
                DB::commit();
                return redirect()->back()->with('success', 'Comment created successfully!');
            } else {
                DB::rollBack();
                return redirect('')->back()->with('error', 'Unable to save the Comment!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
