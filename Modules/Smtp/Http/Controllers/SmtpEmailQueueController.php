<?php

/**
 *@copyright : ASk. < http://arshresume.epizy.com/ >
 *@author	 : Arshdeep Singh < arshdeepsinghjoshan84@gmail.com >
 *
 * All Rights Reserved.
 * Proprietary and confidential :  All information contained herein is, and remains
 * the property of ASK. and its partners.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 *
 */

namespace Modules\Smtp\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;
use Illuminate\Support\Str;
use Modules\Smtp\Http\Models\MailConfiguration;
use Modules\Smtp\Http\Models\SmtpEmailQueue;

class SmtpEmailQueueController extends Controller
{
    public function index()
    {
        $model = new  SmtpEmailQueue();
        return view('smtp::email_queues.index', compact('model'));
    }

    public function getEmailQueuesList(Request $request)
    {

        $query  = SmtpEmailQueue::orderBy('id', 'DESC');
        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('status', function ($data) {
                return $data->getState();
            })->addColumn('created_by', function ($data) {
                return !empty($data->createdBy && $data->createdBy->name) ? $data->createdBy->name : 'N/A';
            })
            ->addColumn('created_at', function ($data) {
                return (empty($data->created_at)) ? 'N/A' : date('Y-m-d', strtotime($data->created_at));
            })


            ->addColumn('action', function ($data) {
                $html = '<div class="table-actions text-center">';
                $html .=    '  <a class="btn btn-icon btn-primary mt-1" href="' . url('email-queue/view/' . $data->id) . '"  ><i class="fa fa-eye
                            "data-toggle="tooltip"  title="View"></i></a>';
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
                            ->orWhere('subject', 'like', "%$searchValue%")
                            ->orWhere('from', 'like', "%$searchValue%")
                            ->orWhere('to', 'like', "%$searchValue%")
                            ->orWhere('created_at', 'like', "%$searchValue%")
                            ->orWhere(function ($query) use ($searchValue) {
                                $query->searchState($searchValue);
                            });
                    });
                }
            })
            ->make(true);
    }


    protected static function validator(array $data)
    {
        return Validator::make($data, [
            'subject' => ['required', 'string', 'max:255'],
            'from' => ['required', 'string', 'email',],
            'to' => ['required', 'string', 'email',]
        ]);
    }


    protected  static function create(array $data)
    {
        return SmtpEmailQueue::create([
            'subject' => $data['subject'],
            'from' => $data['from'],
            'to' => $data['to'],
            'cc' => $data['cc'],
            'bcc' => $data['bcc'],
            'content' => $data['content'],
            'type_id' => $data['type_id'],
            'state_id' => SmtpEmailQueue::STATE_PENDING,
            'model_id' => $data['model_id']
        ]);
    }

    public static function store($request)
    {
        // self::validator($request)->validate();
        $data = self::create($request);
        return $data;
    }

    public function view(Request $request, $id)
    {
        $model = SmtpEmailQueue::find($id);
        if (empty($model)) {
            return redirect('/email-queue')->with('error', 'User does not exist');
        }
        return view('smtp::email_queues.view', compact('model'));
    }

    public function edit(SmtpEmailQueue $smtpEmailQueue)
    {
        return view('smtp.email_queues.edit', compact('smtpEmailQueue'));
    }

    public function emailVerification(Request $request, $id)
    {
        $model = User::find($id);
        if (empty($model)) {
            return redirect('/email-queue')->with('error', 'User does not exist');
        }
        return view('emails.new_user', compact('model'));
    }

    public function update(Request $request)
    {
        $mailConfig  = MailConfiguration::latest()->first();
        return view('smtp::account.update', compact('mailConfig'));
    }


    public function finalDelete($id)
    {
        $mailConfig = SmtpEmailQueue::find($id);
        if ($mailConfig) {
            $mailConfig->delete();
            return redirect('/email-queue')->with('success', 'SMTP email queue has been deleted!');
        } else {
            return redirect('404');
        }
    }
}
