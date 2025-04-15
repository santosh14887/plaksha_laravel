<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use App\Models\ServiceSubCategory;
use Auth;
use Validator;
use Session;
use DB;
use Config;
class ServiceCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(Auth::user()->can('viewMenu:ExpenseCategory') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
             $perPage = 15;
             $keyword = $request->get('search');
			if (!empty($keyword)) {
				$service_categories = ServiceCategory::where('name', 'LIKE', "%$keyword%")->latest()->paginate($perPage);
			} else {
				$service_categories = ServiceCategory::where('parent_category','0')->latest()->paginate($perPage);
			}
            return view('service_categories.index',compact('service_categories'));
        }
		else
		{
			return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
		}
    }
	public function specific_service_sub_category($id){
        if(Auth::user()->can('viewMenu:ExpenseCategory') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            $service_sub_categories = ServiceCategory::where('parent_category','=',$id)->get();
            return view('service_categories.sub_cat_list',compact('service_sub_categories','id'));
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
        if(Auth::user()->can('viewMenu:ActionExpenseCategory') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			$added_service_categories = ServiceCategory::where('parent_category','0')->get();
            return view('service_categories.create',compact('added_service_categories'));
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
        if(Auth::user()->can('viewMenu:ActionExpenseCategory') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            $data  = $request->all();
            // validate
            $rules = [
            'name'  => "required|unique:service_categories",
			];
			$message = [
				'name.required' => 'Name must be added',
				'name.unique' => 'The name has already been taken.',
			];
            $validator = Validator::make($data, $rules, $message);

            if ($validator->fails()) {
                return back()->withInput()
                    ->withErrors($validator);
            } else {
                // store
                $name = $request->name;
                $parent_category = $request->parent_category;
                $comment = $request->comment;
                $slug = strtolower(str_replace(' ','_',$name));
				$service_categories = new ServiceCategory;
				$service_categories->name = $name;
				$service_categories->slug = $slug;
				$service_categories->parent_category = $parent_category;
				$service_categories->comment = $comment;
				$service_categories->created_by = Auth::user()->id;
				$service_categories->save();

                // redirect
                Session::flash('message', 'Successfully created service categories!');
                return redirect()->route("service_categories.index");
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
        if(Auth::user()->can('viewMenu:ActionExpenseCategory') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            $service_categories = ServiceCategory::find($id);
			$added_service_categories = ServiceCategory::where('parent_category','0')->get();
            return view('service_categories.edit',compact('service_categories','added_service_categories'));
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
        if(Auth::user()->can('viewMenu:ActionExpenseCategory') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
        $data  = $request->all();
        // validate
        $rules = [
            'name'  => "required|unique:service_categories,name,$id",
        ];
		$message = [
            'name.required' => 'Name must be added',
            'name.unique' => 'The name has already been taken.',
        ];
        $validator = Validator::make($data, $rules, $message);
        if ($validator->fails()) {
            return back()->withInput()
                ->withErrors($validator);
        } else {
            // store
			$name = $request->name;
			$parent_category = $request->parent_category;
			$comment = $request->comment;
			$slug = strtolower(str_replace(' ','_',$name));
            $service_categories = ServiceCategory::find($id);
			$service_categories->name = $name;
			$service_categories->slug = $slug;
			$service_categories->parent_category = $parent_category;
			$service_categories->comment = $comment;
			$service_categories->updated_by = Auth::user()->id;
			$service_categories->save();

            // redirect
            Session::flash('message', 'Successfully updated service categories!');
			return redirect()->route("service_categories.index");
        }
        }
        else
        {
            return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
        }
    }
	 public function show($id)
    {
        if(Auth::user()->can('viewMenu:ExpenseCategory') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            $service_categories = ServiceCategory::find($id);
            if(null !== $service_categories) {
                return view('service_categories.show', compact('service_categories'));
            } else {
                Session::flash('message', 'ServiceCategory does not exist!');
                return redirect()->route("service_categories.index");
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
        if(Auth::user()->can('viewMenu:ActionExpenseCategory') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
        $service_categories = ServiceCategory::find($id);
        $service_categories->delete();
        // redirect
        Session::flash('message', 'Successfully deleted service categories!');
			return redirect()->route("service_categories.index");
        }
        else
        {
            return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
        }
    }
}