<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleService;
use App\Models\ServiceCategory;
use App\Models\ServiceSubCategory;
use Auth;
use Validator;
use Session;
use DB;
use Config;
class VehicleServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		die('here');
    }
	public function specific_vehicle_service($id){
        if(Auth::user()->can('viewMenu:VehicleService') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            $perPage = 15;
            $vehicles = Vehicle::find($id);
            $vehicle_services = VehicleService::where('vehicle_id','=',$id)->latest()->paginate($perPage);
            return view('vehicle_services.index',compact('vehicle_services','id','vehicles'));
        }
        else
        {
            return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
        }
	}
	public function get_service_subcategory(){
		
		extract($_REQUEST);
		$service_sub_categories = ServiceCategory::where('parent_category','=',$selected_id)->get()->toArray();
		$html = '<option value="">Select Expense Subcategory</option>';
		if(!empty($service_sub_categories)) {
			foreach($service_sub_categories as $value) {
				$slug = $value['slug'];
				$name = $value['name'];
				$selected_val = '';
				if($prev_sucat_id == $slug) {
					$selected_val = 'selected';
				}
				$html .= '<option value="'.$slug.'" '.$selected_val.'>'.$name.'</option>';
			}
		} else {
			$html = '<option value="">No data found</option>';
		}
		return $html;
	}
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        if(Auth::user()->can('viewMenu:ActionVehicleService') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			$added_service_categories = ServiceCategory::where('parent_category','0')->get();
			$vehicles = Vehicle::find($id);
			return view('vehicle_services.create',compact('vehicles','added_service_categories'));
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
        if(Auth::user()->can('viewMenu:ActionVehicleService') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            $vehicle_id = $request->vehicle_id;
            $vehicles = Vehicle::find($vehicle_id);
            $data  = $request->all();
            // validate
            $rules = [
                'on_date' => 'required',
            //  'on_km' => 'required',
                'service_type'  => 'required',
                'service_cat_id'  => 'required',
                'expense_amount'  => 'required|numeric',
            ];
            $message = [
                'on_date.required' => 'Date is required.',
                'on_km.required' => 'Km is required.',
                'service_cat_id.required' => 'Expense category is required.',
                'service_type.required' => 'SUb Category is required.',
                'expense_amount.numeric'  => 'Amount should be numeric',
                'expense_amount.required'  => 'Amount must ne added',
            ];
            $validator = Validator::make($data, $rules, $message);

            if ($validator->fails()) {
                return back()->withInput()
                    ->withErrors($validator);
            } else {
                $error_got = '';
                $service_type = $request->service_type;
				$service_subcat_categories = ServiceCategory::where('slug',$service_type)->get()->toArray();
				$service_subcat_id = $service_subcat_categories[0]['id'];
                $service_cat_id = $request->service_cat_id;
				$service_cat_arr = ServiceCategory::find($service_cat_id);
				$parent_service_type = $service_cat_arr->slug;
                $on_km = ($request->on_km > 0) ? $request->on_km : 0;
                if($service_type == 'regular_service' && $on_km == '') {
                    $error_got = 'yes';
                    $validator->getMessageBag()->add('on_km', 'Please add km');
                }
                if($error_got == 'yes') {
                    return back()->withInput()
                    ->withErrors($validator);
                }
                if($service_type == 'air_filter_change') {
                    $last_air_filter = $request->on_date;
                    $air_filter_after_days = $vehicles->air_filter_after_days;
                    $on_filterdate = date("Y-m-d H:i:s", strtotime($last_air_filter));
                    $due_air_filter_date = date('Y-m-d', strtotime($last_air_filter. ' + '.$air_filter_after_days.' days'));
                    $vehicles->last_air_filter_date = $on_filterdate;
                    $vehicles->due_air_filter_date = $due_air_filter_date;
                    $vehicles->save();
                }
                // store
                $on_date = date("Y-m-d", strtotime($request->on_date));
                $vehicle_service = new VehicleService;
                $vehicle_service->created_by = Auth::user()->id;
                $vehicle_service->vehicle_id = $vehicle_id;
                $vehicle_service->service_subcat_id = $service_subcat_id;
                $vehicle_service->parent_service_type = $parent_service_type;
                $vehicle_service->service_cat_id = $service_cat_id;
                $vehicle_service->on_date = $on_date;
                $vehicle_service->on_km = $on_km;
                $vehicle_service->service_type = $service_type;
                $vehicle_service->comment = $request->comment;
                $vehicle_service->expense_amount = ( null != $request->expense_amount) ? $request->expense_amount : 0.00;
                $vehicle_service->save();
                // redirect
                Session::flash('message', 'Successfully expense added!');
                return redirect("specific_vehicle_service/".$vehicle_id)->with('message', 'Successfully expense added!');
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
        if(Auth::user()->can('viewMenu:ActionVehicleService') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			$added_service_categories = ServiceCategory::where('parent_category','0')->get();
            $vehicle_services = VehicleService::find($id);
            $vehicle_id = $vehicle_services->vehicle_id;
            $vehicles = Vehicle::find($vehicle_id);
            
            return view('vehicle_services.edit',compact('vehicle_services','vehicles','added_service_categories'));
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
        if(Auth::user()->can('viewMenu:ActionVehicleService') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            $vehicle_id = $request->vehicle_id;
            $vehicles = Vehicle::find($vehicle_id);
            $data  = $request->all();
            // validate
            $rules = [
            'on_date' => 'required',
            // 'on_km' => 'required',
			'service_cat_id'  => 'required',
			'service_type'  => 'required',
			'expense_amount'  => 'required|numeric',
            ];
            $message = [
                'on_date.required' => 'Date is required.',
                'on_km.required' => 'Km is required.',
				'service_cat_id.required' => 'Expense category is required.',
                'service_type.required' => 'SUb Category is required.',
                'expense_amount.numeric'  => 'Amount should be numeric',
				'expense_amount.required'  => 'Amount must ne added',
            ];
            $validator = Validator::make($data, $rules, $message);
            if ($validator->fails()) {
                return back()->withInput()
                    ->withErrors($validator);
            } else {
                $error_got = '';
                $service_type = $request->service_type;
				$service_subcat_categories = ServiceCategory::where('slug',$service_type)->get()->toArray();
				$service_subcat_id = $service_subcat_categories[0]['id'];
                $service_cat_id = $request->service_cat_id;
				$service_cat_arr = ServiceCategory::find($service_cat_id);
				$parent_service_type = $service_cat_arr->slug;
                $on_km = ($request->on_km > 0) ? $request->on_km : 0;
                if($service_type == 'regular_service' && $on_km == '') {
                    $error_got = 'yes';
                    $validator->getMessageBag()->add('on_km', 'Please add km');
                }
                if($error_got == 'yes') {
                    return back()->withInput()
                    ->withErrors($validator);
                }
                if($service_type == 'air_filter_change') {
                    $last_air_filter = $request->on_date;
                    $air_filter_after_days = $vehicles->air_filter_after_days;
                    $on_filterdate = date("Y-m-d H:i:s", strtotime($last_air_filter));
                    $due_air_filter_date = date('Y-m-d', strtotime($last_air_filter. ' + '.$air_filter_after_days.' days'));
                    $vehicles->last_air_filter_date = $on_filterdate;
                    $vehicles->due_air_filter_date = $due_air_filter_date;
                    $vehicles->save();
                }
                $on_date = date("Y-m-d", strtotime($request->on_date));
                $vehicle_service = VehicleService::find($id);
                $vehicle_service->on_date = $on_date;
                $vehicle_service->on_km = $on_km;
                $vehicle_service->service_type = $service_type;
				$vehicle_service->service_subcat_id = $service_subcat_id;
                $vehicle_service->parent_service_type = $parent_service_type;
                $vehicle_service->service_cat_id = $service_cat_id;
                $vehicle_service->comment = $request->comment;
                $vehicle_service->expense_amount = ( null != $request->expense_amount) ? $request->expense_amount : 0.00;
                $vehicle_service->save();
                Session::flash('message', 'Successfully updated expense!');
                return redirect("specific_vehicle_service/".$vehicle_id)->with('message', 'Successfully updated expense!');
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
        if(Auth::user()->can('viewMenu:ActionVehicleService') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
        $service = VehicleService::find($id);
		$vehicle_id = $service->vehicle_id;
        $service->delete();
        // redirect
        Session::flash('message', 'Successfully deleted expense!');
		return redirect("specific_vehicle_service/".$vehicle_id)->with('message', 'Successfully deleted expense!');
        }
        else
        {
            return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
        }
    }
}