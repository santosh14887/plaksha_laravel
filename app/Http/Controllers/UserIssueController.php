<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserIssue;
use App\Models\IssueCategory;
use App\Models\User;
use Auth;
use Validator;
use Session;
use DB;
use Config;
class UserIssueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(Auth::user()->can('viewMenu:UserIssue') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
        $perPage = 15;
		$start_time = $till_date = '';
        $keyword = $request->get('search');
		$issue_date = $request->get('issue_date');
		$status = $request->get('status');
		if($issue_date != '') {
			$date_arr = explode("-",$issue_date);
			$start_time = $date_arr[0];
			$till_date = $date_arr[1];
		} else{}
		$query =  UserIssue::query()->with('getUser')->with('getIssueCatgory');
			if (!empty($keyword) || $start_time != '' || $status != '') {
					if(!empty($keyword)) {
					$keyword = ($keyword == 'on hold' || $keyword == 'onhold' || $keyword == 'on_hold') ? 'on_hold' : $keyword;
					$user_issues = $query->where('start_time', 'LIKE', "%$keyword%")
					->orWhereHas('getUser',function ($query)use($keyword)
				  {
					  $query->where('users.name','Like','%'.$keyword.'%');
				  }) ->orWhereHas('getIssueCatgory',function ($query)use($keyword)
				  {
					  $query->where('issue_categories.title','Like','%'.$keyword.'%');
				  })->orWhere('title', 'LIKE', "%$keyword%")
				  ->orWhere('status', 'LIKE', "%$keyword%");
				}
				if($status != ''){
					$query->where('status','=',$keyword);
				}
				if($start_time != '') {
					$date_greter_then = date("Y-m-d", strtotime($start_time)); 
					$date_less_then = date("Y-m-d", strtotime($till_date));
					$query->where(function ($query) use($date_greter_then,$date_less_then){
						$query->whereDate('updated_at','>=',$date_greter_then)
							  ->whereDate('updated_at','<=',$date_less_then);
					});
					$query->orwhere(function ($query) use($date_greter_then,$date_less_then) {
						$query->whereDate('created_at','>=',$date_greter_then)
							  ->whereDate('created_at','<=',$date_less_then);
					});
				}
					$user_issues = $query->latest()->paginate($perPage);
			} else {
				$user_issues = UserIssue::orderBy('status_no', 'asc')->paginate($perPage);
			}
            return view('user_issues.index',compact('user_issues'));
        }
        else
        {
            return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->can('viewMenu:ActionUserIssue') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            $user_arr = User::where('type','=','employee')->get();
            $issue_category_arr = IssueCategory::get();
            return view('user_issues.create',compact('user_arr','issue_category_arr'));
        }
        else
        {
            return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Auth::user()->can('viewMenu:ActionUserIssue') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            $data  = $request->all();
            // validate
            $rules = [
                //'title'  => 'required|unique:user_issues',
                'title'  => 'required',
                'user_id'  => 'required',
                'description'  => 'required',
                'issue_category'  => 'required',
                'start_time'  => 'required',
            ];
            $message = [
                'title.required' => 'The issue title must be added',
                'description.required' => 'The issue description must be added',
                'user_id.required' => 'User must be added',
                'issue_category.required' => 'Category must be added',
                'start_time.required' => 'date must be added',
            ];
            $validator = Validator::make($data, $rules, $message);

            if ($validator->fails()) {
                return back()->withInput()
                    ->withErrors($validator);
            } else {
                // store
                $status_no = 1;
                $status = $request->status;
                switch($status) {
                    case "on_hold":
                    $status_no = 2;
                    break;
                    case "rejected":
                    $status_no = 3;
                    break;
                    case "resolved":
                    $status_no = 4;
                    break;
                    default:
                    $status_no = 1;
                    break;	
                }
                $start_time = date("Y-m-d H:i:s", strtotime($request->start_time));
                $user_issues = new UserIssue;
                $user_issues->title = $request->title;
                $user_issues->user_id = $request->user_id;
                $user_issues->issue_category = $request->issue_category;
                $user_issues->description = $request->description;
                $user_issues->start_time = $request->start_time;
                $user_issues->status = $status;
                $user_issues->status_no = $status_no;
                $user_issues->save();

                // redirect
                Session::flash('message', 'User Issue created successfully!');
                return redirect()->route("user_issues.index");
            }
        }
        else
        {
            return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(Auth::user()->can('viewMenu:UserIssue') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            $user_issues = UserIssue::find($id);
            if(null !== $user_issues) {
                return view('user_issues.show', compact('user_issues'));
            } else {
                Session::flash('message', 'Issue does not exist!');
                return redirect()->route("user_issues.index");
            }
        }
        else
        {
            return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
        }
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(Auth::user()->can('viewMenu:ActionUserIssue') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            $user_issues = UserIssue::find($id);
            $user_arr = User::where('type','=','employee')->get();
            $issue_category_arr = IssueCategory::get();
            return view('user_issues.edit',compact('user_issues','user_arr','issue_category_arr'));
        }
        else
        {
            return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(Auth::user()->can('viewMenu:ActionUserIssue') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            $data  = $request->all();
            // validate
            $rules = [
                //'title'  => "required|unique:user_issues,title,$id",
                'title'  => "required",
                'user_id'  => 'required',
                'description'  => 'required',
                'issue_category'  => 'required',
                'start_time'  => 'required',
            ];
            $message = [
                'title.required' => 'The issue title must be added',
                'description.required' => 'The issue description must be added',
                'user_id.required' => 'User must be added',
                'issue_category.required' => 'Category must be added',
                'start_time.required' => 'Date must be added',
            ];
            $validator = Validator::make($data, $rules, $message);
            if ($validator->fails()) {
                return back()->withInput()
                    ->withErrors($validator);
            } else {
                // store
                $status_no = 1;
                $status = $request->status;
                switch($status) {
                    case "on_hold":
                    $status_no = 2;
                    break;
                    case "rejected":
                    $status_no = 3;
                    break;
                    case "resolved":
                    $status_no = 4;
                    break;
                    default:
                    $status_no = 1;
                    break;	
                }
                $start_time = date("Y-m-d H:i:s", strtotime($request->start_time));
                $user_issues = UserIssue::find($id);
                $user_issues->title = $request->title;
                $user_issues->user_id = $request->user_id;
                $user_issues->issue_category = $request->issue_category;
                $user_issues->description = $request->description;
                $user_issues->status = $status;
                $user_issues->status_no = $status_no;
                $user_issues->start_time = $start_time;
                $user_issues->save();

                // redirect
                Session::flash('message', 'User Issue updated successfully!');
                return redirect()->route("user_issues.index");
            }
        }
        else
        {
            return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Auth::user()->can('viewMenu:ActionUserIssue') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            $user_issues = UserIssue::find($id);
            $user_issues->delete();
            // redirect
            Session::flash('message', 'issue deleted successfully!');
			return redirect()->route("user_issues.index");
        }
        else
        {
            return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
        }
    }
}