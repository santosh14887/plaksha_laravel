<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IssueCategory;
use App\Models\UserIssue;
use Auth;
use Validator;
use Session;
use DB;
use Config;
class IssueCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(Auth::user()->can('viewMenu:IssueCategory') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
        $perPage = 15;
        $keyword = $request->get('search');
			if (!empty($keyword)) {
				$issue_categories = IssueCategory::where('title', 'LIKE', "%$keyword%")
                ->orWhere('description', 'LIKE', "%$keyword%")
					->latest()->paginate($perPage);
			} else {
				$issue_categories = IssueCategory::latest()->paginate($perPage);
			}
            return view('issue_categories.index',compact('issue_categories'));
        }
        else
        {
            return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
        }
    }
	public function user_issues(Request $request,$id)
    {
        if(Auth::user()->can('viewMenu:UserIssue') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            $perPage = 15;
            $issue_categories = IssueCategory::find($id);
            if(null !== $issue_categories) {
                $keyword = $request->get('search');
                if (!empty($keyword)) {
                $query =  UserIssue::query()->with('getUser');
                    $user_issues = $query->where('issue_category', '=', $id)
                    ->where('title', 'LIKE', "%$keyword%")
                    ->orWhereHas('getUser',function ($query)use($keyword)
                {
                    $query->where('users.name','Like','%'.$keyword.'%');
                })->orWhere('description', 'LIKE', "%$keyword%")
                        ->latest()->paginate($perPage);
                    
                    
                } else {
                    $user_issues = UserIssue::where('issue_category','=',$id)->latest()->paginate($perPage);
                }
                return view('issue_categories.user_issues',compact('issue_categories','user_issues','id'));
            } else {
                return redirect()->route("issue_categories.index");
            } 
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
        if(Auth::user()->can('viewMenu:ActionIssueCategory') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
             return view('issue_categories.create');
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
        if(Auth::user()->can('viewMenu:ActionIssueCategory') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            $data  = $request->all();
            // validate
            $rules = [
                'title'  => 'required|unique:issue_categories',
            ];
            $message = [
                'title.required' => 'Issue category title must be added',
                'description.required' => 'The Address must be added',
            ];
            $validator = Validator::make($data, $rules, $message);

            if ($validator->fails()) {
                return back()->withInput()
                    ->withErrors($validator);
            } else {
                // store
                $issue_categories = new IssueCategory;
                $issue_categories->title = $request->title;
                $issue_categories->description = $request->description;
                $issue_categories->save();

                // redirect
                Session::flash('message', 'Issue category created successfully!');
                return redirect()->route("issue_categories.index");
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(Auth::user()->can('viewMenu:ActionIssueCategory') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            $issue_categories = IssueCategory::find($id);
            return view('issue_categories.edit',compact('issue_categories'));
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
        if(Auth::user()->can('viewMenu:ActionIssueCategory') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            $data  = $request->all();
            // validate
            $rules = [
                'title'  => "required|unique:issue_categories,title,$id",
            ];
            $message = [
                'title.required' => 'Issue category title must be added',
                'description.required' => 'The Address must be added',
            ];
            $validator = Validator::make($data, $rules, $message);
            if ($validator->fails()) {
                return back()->withInput()
                    ->withErrors($validator);
            } else {
                // store
                $issue_categories = IssueCategory::find($id);
                $issue_categories->title = $request->title;
                $issue_categories->description = $request->description;
                $issue_categories->save();

                // redirect
                Session::flash('message', 'Issue category updated successfully!');
                return redirect()->route("issue_categories.index");
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
        if(Auth::user()->can('viewMenu:ActionIssueCategory') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            $issue_categories = IssueCategory::find($id);
            $issue_categories->delete();
            // redirect
            Session::flash('message', 'issue deleted successfully!');
			return redirect()->route("issue_categories.index");
        }
        else
        {
            return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
        }
    }
}