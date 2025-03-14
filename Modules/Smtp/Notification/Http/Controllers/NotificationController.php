<?php

namespace Modules\Notification\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Notification\Http\Models\Notification;

class NotificationController extends Controller
{

    public function index()
    {
        return view('notification::notifications.index');
    }
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'title' => ['required', 'string', 'max:255'],
            'model_id' => ['required', 'string', 'max:255'],
            'model_type' => ['required', 'string', 'max:255']

        ]);
    }

    public function getNotificationList(Request $request)
    {
        $query  = Notification::orderBy('id', 'DESC');
        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('is_read', function ($data) {
                return $data->getIsRead();
            })
            ->rawColumns(['is_read'])
            ->addColumn('status', function ($data) {
                return $data->getState();
            })
            ->rawColumns(['status'])
            ->addColumn('action', function ($data) {
                $html = '<div class="table-actions text-center">';
                $html .=    '  <a class="btn btn-primary " href="' . url('notification/view/' . $data->id) . '"  ><i class="  ri-eye-line
                    "data-toggle="tooltip"  title="View"></i></a> ';
                $html .= '<a class="btn btn-danger custom-delete" href="' . url('notification/delete/' . $data->id) . '"><i class="ri-delete-bin-5-fill" data-toggle="tooltip" title="Delete"></i></a>';
                $html .=  '</div>';
                return $html;
            })->addColumn('customerClickAble', function ($data) {
                $html = 0;
                return $html;
            })
            ->rawColumns(['action', 'customerClickAble'])
            ->filter(function ($query) {
                $searchValue = request('search.value');
                if (!empty($searchValue)) {
                    $query->where(function ($q) use ($searchValue) {
                        $q->where('id', 'like', "%$searchValue%")
                            ->orWhere('title', 'like', "%$searchValue%")
                            ->orWhere('description', 'like', "%$searchValue%")
                            ->orWhere('created_at', 'like', "%$searchValue%")
                            ->orWhereHas('createdBy', function ($query) use ($searchValue) {
                                $query->where('full_name', 'like', "%$searchValue%");
                            })
                            ->orWhere(function ($query) use ($searchValue) {
                                $query->isRead($searchValue);
                            })
                            ->orWhere(function ($query) use ($searchValue) {
                                $query->searchState($searchValue);
                            });
                    });
                }
            })
            ->make(true);
    }

    public function view(Request $request, $id)
    {
        $notification = Notification::find($id);
        $notification->update([
            'state_id' => Notification::STATE_ACTIVE,
            'is_read' => Notification::IS_READ

        ]);
        $user = User::find($notification->model_id);
        return view('notification::notifications.view', compact('user', 'notification'));
    }
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected static function create(array $data, $model = null)
    {
        return Notification::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'model_id' => $data['model_id'],
            'model_type' => $data['model_type'],
            'to_user_id' => $data['model_id'],
            'is_read' => Notification::STATE_INACTIVE,
        ]);
    }

    public static function store($request, $model = null)
    {
        $data = self::create($request, $model);
        $data->sendNotificationOnApp();
        return true;
    }

    public function finalDelete($id)
    {
        $notification = Notification::find($id);
        if ($notification) {
            $notification->delete();
            return redirect('/notification')->with('success', 'Notification has been deleted successfully!');
        } else {
            return redirect('404');
        }
    }
}
