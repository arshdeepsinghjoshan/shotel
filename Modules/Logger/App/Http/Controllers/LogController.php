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

namespace Modules\Logger\App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use DataTables;
use Illuminate\Support\Str;
use Modules\Logger\Models\Log;

class LogController extends Controller
{
    public $setFilteredRecords = 0;

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        try {
            $model = new Log();
            return view('logger::log.index', compact('model'));
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function list(Request $request)
    {

        $query  = Log::orderBy('id', 'DESC');
        return Datatables::of($query)
            ->addColumn('created_by', function ($data) {
                return !empty($data->createdBy && $data->createdBy->name) ? $data->createdBy->name : 'N/A';
            })
            ->addColumn('message', function ($data) {

                return !empty($data->message) ? (strlen($data->message) > 50 ? substr(ucfirst($data->message), 0, 50) . '...' : ucfirst($data->message)) : 'N/A';
            })

            ->addColumn('context', function ($data) {

                return !empty($data->context) ? (strlen($data->context) > 50 ? substr(ucfirst($data->context), 0, 50) . '...' : ucfirst($data->context)) : 'N/A';
            })
            ->addColumn('created_at', function ($data) {
                return (empty($data->updated_at)) ? 'N/A' : date('Y-m-d', strtotime($data->updated_at));
            })
            ->addColumn('status', function ($data) {
                return '<span class="' . $data->getStateBadgeOption() . '">' . $data->getState() . '</span>';
            })
            ->addColumn('action', function ($data) {
                $html =    '  <a class="btn btn-icon btn-primary mt-1" href="' . url('log/view/' . $data->id) . '"  ><i class="fa fa-eye
                "data-toggle="tooltip"  title="View"></i></a>';
                $html .=  '</div>';
                return $html;
            })->addColumn('customerClickAble', function ($data) {
                $html = 0;

                return $html;
            })
            ->rawColumns(['action', 'customerClickAble', 'status', 'created_by'])
            ->filter(function ($query) {
                $searchValue = request('search.value');
                if (!empty($searchValue)) {
                    $query->where(function ($q) use ($searchValue) {
                        $q->where('id', 'like', "%$searchValue%")
                            ->orWhere('message', 'like', "%$searchValue%")
                            ->orWhere('context', 'like', "%$searchValue%")
                            ->orWhere('level', 'like', "%$searchValue%")
                            ->orWhere('created_at', 'like', "%$searchValue%")
                            ->orWhereHas('createdBy', function ($query) use ($searchValue) {
                                $query->where('name', 'like', "%$searchValue%");
                            })
                            ->orWhere(function ($query) use ($searchValue) {
                                $query->searchState($searchValue);
                            });
                    });
                }
            })
            ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('logger::create');
    }


    public function view(Request $request, $id)
    {
        $model = Log::find($id);
        if (empty($model)) {
            return redirect()->back()->with('error', 'Log not found');
        }
        return view('logger::log.view', compact('model'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }


    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('logger::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('logger::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function finalDelete($id)
    {
        $model = Log::find($id);
        if ($model) {
            $model->delete();
            return redirect('/log')->with('success', 'Log has been deleted successfully!');
        } else {
            return redirect()->back()->with('error', 'Log not found');
        }
    }
}
