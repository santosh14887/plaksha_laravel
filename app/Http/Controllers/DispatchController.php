<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dispatch;
use App\Models\AssignDispatch;
use App\Models\DispatchTicket;
use App\Models\VehicleAssignmentHistory;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\User;
use Auth;
use Validator;
use Session;
use DB;
use Config;
class DispatchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(Auth::user()->can('viewMenu:Dispatch') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
        $perPage = 15;
        $start_time = $till_date = '';
        $keyword = $request->get('search');
        $dispatch_date = $request->get('dispatch_date');
        $status = $request->get('status');
        $customer = $request->get('customer');
        $start_location = $request->get('start_location');
			$end_location = $request->get('end_location');
        if($dispatch_date != '') {
            $date_arr = explode("-",$dispatch_date);
            $start_time = $date_arr[0];
            $till_date = $date_arr[1];
        } else{}
        if (!empty($keyword) || $start_time != '' || $status != '' || $customer != '' || $start_location != ''|| $end_location != '')  {
                $query =  Dispatch::query()->with('getCustomer');
                if($status != ''){
                    $query->where('status','=',$status);
                }
                if($start_location != ''){
                    $query->where('start_location','Like','%'.$start_location.'%');
                }
				if($end_location != ''){
					$query->where('dump_location','Like','%'.$end_location.'%');
                }
                if($customer != ''){
                    $query->where('customer_company_name','Like','%'.$customer.'%');
                }
                if($start_time != '') {
                    $date_greter_then = date("Y-m-d", strtotime($start_time)); 
                    $date_less_then = date("Y-m-d", strtotime($till_date));
                    $query->whereDate('start_time','>=',$date_greter_then)
                        ->whereDate('start_time','<=',$date_less_then);
                }
                if(!empty($keyword)) {
                    // $query->where('start_time', 'LIKE', "%$keyword%")
                    // ->orWhereHas('getCustomer',function ($query)use($keyword)
                    // {
                    // $query->where('customers.company_name','Like','%'.$keyword.'%');
                    // })->orWhere('start_location', 'LIKE', "%$keyword%")
                    // ->orWhere('dump_location', 'LIKE', "%$keyword%")
                    // ->orWhere('job_rate', 'LIKE', "%$keyword%")
                    // ->orWhere('job_type', 'LIKE', "%$keyword%")
                    // ->orWhere('supervisor_name', 'LIKE', "%$keyword%")
                    // ->orWhere('supervisor_contact', 'LIKE', "%$keyword%")
                    // ->orWhere('required_unit', 'LIKE', "%$keyword%")
                    // ->orWhere('status', 'LIKE', "%$keyword%");
                }
                 $dispatches = $query->latest()->paginate($perPage); 
            } else {
                $dispatches = Dispatch::latest()->paginate($perPage);
            }
            return view('dispatches.index',compact('dispatches'));
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
    public function vehicle_assignment(Request $request)
    {
		if(Auth::user()->can('viewMenu:Dispatch') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            $search_assignment_date = $request->get('assignment_date');
            $status = $request->get('status');
            if(!empty($search_assignment_date)) {
                $today_date = $search_assignment_date;
            } else {
                $today_date = date('Y-m-d');
            }
            $all_data = $assigned_arr = array();
            $db_query = "SELECT vehicles.id,users.name,vehicles.vehicle_number,vehicles.vin_number,dispatches.start_time,customers.company_name,assign_dispatches.status FROM `vehicles` inner join assign_dispatches on assign_dispatches.vehicle_id = vehicles.id inner join dispatches on dispatches.id = assign_dispatches.dispatch_id inner join users on users.vehicle_id = vehicles.id inner join customers on customers.id = dispatches.customer_id where date(dispatches.start_time) = '".$today_date."' and assign_dispatches.user_type = 'employee'";
            $assignment_qry = DB::select( DB::raw($db_query));
            $vehicle_data = $assignment_qry_arr = array();
            if(null != $assignment_qry && !empty($assignment_qry)) {
                foreach($assignment_qry as $key=> $data) {
                    $data = (array)$data;
                    $vehicler_id = $data['id'];
                    $assigned_arr[] = $vehicler_id;
                    $assignment_qry_arr[$vehicler_id] = $data;
                }   
            } else {}
            if(!empty($status) && $status == 'assigned') {
                $all_data = $assignment_qry_arr;
            } else {
                    $vehicles = Vehicle::whereNotIn('id', $assigned_arr)->with('getOwner')->get()->toArray();
                    if(!empty($status) && $status == 'unassigned') {
                        $all_data = $vehicles;  
                }  else {
                    $all_data = array_merge($assignment_qry_arr,$vehicles);
                }
            }
            return view('dispatches.vehicle_assignment',compact('all_data'));
        }
		else
		{
			return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
		} 
        
    }
    public function create()
    {
        if(Auth::user()->can('viewMenu:ActionDispatch') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            $customer = Customer::get()->pluck('company_name','id');
            return view('dispatches.create',compact('customer'));
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
        if(Auth::user()->can('viewMenu:ActionDispatch') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            $data  = $request->all();
            // validate
            $rules = [
                'customer_id'  => 'required',
                'start_time'  => 'required',
                'start_location'  => 'required',
                'dump_location'  => 'required',
                'job_type'  => 'required',
                'required_unit'  => 'required|numeric',
                'job_rate'  => 'required|numeric',
                'employee_rate'  => 'sometimes|nullable|numeric',
            ];
            $message = [
                'customer_id.required' => 'The Customer name must be added',
                'start_time.required' => 'The Start time must be added',
                'start_location.required' => 'The Start Location must be added',
                'job_type.required' => 'The Job type must be added',
                'required_unit.required' => 'The number of unit must be added',
            ];
            $validator = Validator::make($data, $rules, $message);

            if ($validator->fails()) {
                return back()->withInput()
                    ->withErrors($validator);
            } else {
                // store
                $error_got = '';
                /*** If job type is load emplyee rate  should be required ***/
                $job_type = $request->job_type;
                $employee_rate = $request->employee_rate;
                if($job_type == 'load' && $employee_rate == '') {
                    $error_got = 'yes';
                    $validator->getMessageBag()->add('employee_rate', 'Employee rate must be added');
                }
                if($error_got == 'yes') {
                    return back()->withInput()
                    ->withErrors($validator);
                }
                /*** end If job type is load emplyee rate  should be required ***/
                $default_dispatch_number = 'JPGO'.date('Ymdhis');
                $startDate = date("Y-m-d H:i:s", strtotime($request->start_time));
                $dispatch = new Dispatch;
                $customer_id = $request->customer_id;
                $customer_arr = Customer::where('id',$customer_id)->get()->toArray();
                $customer_arr = $customer_arr['0'];
                $dispatch->customer_company_name = $customer_arr['company_name'];
                $dispatch->customer_address = $customer_arr['address'];
                $dispatch->customer_customer_hst = $customer_arr['customer_hst'];
                $dispatch->customer_id = $customer_id;
                $dispatch->start_time = $startDate;
                $dispatch->start_location = $request->start_location;
                $dispatch->dump_location = $request->dump_location;
                $dispatch->job_rate = $request->job_rate;
                $dispatch->job_type = $request->job_type;
                $dispatch->employee_rate = ($request->employee_rate == '' || $request->employee_rate == null) ? 0 : $request->employee_rate;
                $dispatch->required_unit = $request->required_unit;
                $dispatch->supervisor_name = $request->supervisor_name;
                $dispatch->supervisor_contact = $request->supervisor_contact;
                $dispatch->comment = $request->comment;
                $dispatch->default_dispatch_number = $default_dispatch_number;
                
                $dispatch->save();

                // redirect
                Session::flash('message', 'Successfully created order!');
                return redirect()->route("dispatches.index");
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
        if(Auth::user()->can('viewMenu:Dispatch') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            $dispatches = Dispatch::find($id);
            if(null !== $dispatches) {
                return view('dispatches.show', compact('dispatches'));
            } else {
                Session::flash('message', 'Dispatch does not exist!');
                return redirect()->route("dispatches.index");
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
        if(Auth::user()->can('viewMenu:ActionDispatch') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            $customer = Customer::get()->pluck('company_name','id');
            $dispatches = Dispatch::find($id);
            if($dispatches->status == 'pending') {
                return view('dispatches.edit',compact('dispatches','customer'));
            } else {
                Session::flash('message', "Dispatch is comepleted.Can't update!");
                return redirect()->route("dispatches.index");
            }
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
        if(Auth::user()->can('viewMenu:ActionDispatch') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            $data  = $request->all();
            // validate
            $rules = [
                'customer_id'  => 'required',
                'start_time'  => 'required',
                'start_location'  => 'required',
                'dump_location'  => 'required',
                'job_type'  => 'required',
                'required_unit'  => 'required|numeric',
                'job_rate'  => 'required|numeric',
                'employee_rate'  => 'sometimes|nullable|numeric',
            ];
            $message = [
                'customer_id.required' => 'The Customer name must be added',
                'start_time.required' => 'The Start time must be added',
                'start_location.required' => 'The Start Location must be added',
                'job_type.required' => 'The Job type must be added',
                'required_unit.required' => 'The number of unit must be added',
            ];
            $validator = Validator::make($data, $rules, $message);
            if ($validator->fails()) {
                return back()->withInput()
                    ->withErrors($validator);
            } else {
                // store
                $error_got = '';
                /*** If job type is load emplyee rate  should be required ***/
                $job_type = $request->job_type;
                $employee_rate = $request->employee_rate;
                if($job_type == 'load' && $employee_rate == '') {
                    $error_got = 'yes';
                    $validator->getMessageBag()->add('employee_rate', 'Employee rate must be added');
                }
                if($error_got == 'yes') {
                    return back()->withInput()
                    ->withErrors($validator);
                }
                /*** end If job type is load emplyee rate  should be required ***/
                $dispatch = Dispatch::find($id);
                $startDate = date("Y-m-d H:i:s", strtotime($request->start_time));
                $customer_id = $request->customer_id;
                $customer_arr = Customer::where('id',$customer_id)->get()->toArray();
                $customer_arr = $customer_arr['0'];
                $dispatch->customer_company_name = $customer_arr['company_name'];
                $dispatch->customer_address = $customer_arr['address'];
                $dispatch->customer_customer_hst = $customer_arr['customer_hst'];
                $dispatch->customer_id = $customer_id;
                $dispatch->start_time = $startDate;
                $dispatch->start_location = $request->start_location;
                $dispatch->dump_location = $request->dump_location;
                $dispatch->job_type = $request->job_type;
                $dispatch->job_rate = $request->job_rate;
                $dispatch->employee_rate = ($request->employee_rate == '' || $request->employee_rate == null) ? 0 : $request->employee_rate;
                $dispatch->required_unit = $request->required_unit;
                $dispatch->supervisor_name = $request->supervisor_name;
                $dispatch->supervisor_contact = $request->supervisor_contact;
                $dispatch->comment = $request->comment;
                $dispatch->save();

                // redirect
                Session::flash('message', 'Successfully updated order!');
                return redirect()->route("dispatches.index");
            }
        }
		else
		{
			return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
		}
    }
    public function update_prev_cusomer_on_dispatch(Request $request) {
        $dispatch_arr = Dispatch::where('customer_company_name','=','')->orWhere('customer_company_name','=',null)->get()->toArray();
        
        if(isset($dispatch_arr) && !empty($dispatch_arr)) {
            foreach($dispatch_arr as $vals) {
                $id = $vals['id'];
                $dispatch = Dispatch::find($id);
                $customer_id = $vals['customer_id'];
                $customer_arr = Customer::where('id',$customer_id)->get()->toArray();
                $customer_arr = $customer_arr['0'];
                $dispatch->customer_company_name = $customer_arr['company_name'];
                $dispatch->customer_address = $customer_arr['address'];
                $dispatch->customer_customer_hst = $customer_arr['customer_hst'];
                $dispatch->save();
            }
            
        } else {
            echo 'all updated';
            
        }
        die('');
    }
    public function update_prev_user_on_assign_dispatch(Request $request) {
        $dispatch_arr = AssignDispatch::where('user_name','=','')->orWhere('user_name','=',null)->get()->toArray();
        
        if(isset($dispatch_arr) && !empty($dispatch_arr)) {
            foreach($dispatch_arr as $vals) {
                $id = $vals['id'];
                $assign_user_id = $vals['user_id'];
                $users_data_qry = User::where('id','=', $assign_user_id)->with('getVehicle')->get()->toArray();
				$user_data = $users_data_qry[0];
                $assign_dispatch = AssignDispatch::find($id);
                $assign_dispatch->user_name = $user_data['name'];
				$assign_dispatch->contact_number = $user_data['country_code'].' '.$user_data['phone'];
				/*******  get vehicle number if employee selected */
				// if(isset($user_data['get_vehicle']) && !empty($user_data['get_vehicle'])) {
				// 	$assign_dispatch->vehicle_number = $user_data['get_vehicle']['vehicle_number'];	
                // } else {}
                /******* end get vehicle number if employee selected */
                $assign_dispatch->save();
            }
            
        } else {
            echo 'all updated';
            
        }
        die('');
    }
    public function update_prev_vehicle_number_on_assign_vehicle(Request $request) {
        $dispatch_arr = AssignDispatch::where('vehicle_number','=','')->orWhere('vehicle_number','=',null)->get()->toArray();
        
        if(isset($dispatch_arr) && !empty($dispatch_arr)) {
            foreach($dispatch_arr as $vals) {
                $id = $vals['id'];
                $vehicle_id = $vals['vehicle_id'];
                $vehicle_arr = Vehicle::where('id', $vehicle_id)->get()->toArray();
                if(isset($vehicle_arr) && !empty($vehicle_arr)) {
                    $vehicle_data = $vehicle_arr[0]; 
                    $assign_dispatch = AssignDispatch::find($id);
                    $assign_dispatch->vehicle_number = $vehicle_data['vehicle_number'];
                    $assign_dispatch->save();
                }  
            }
            
        } else {
            echo 'all updated';
            
        }
        die('');
    }
    public function update_driver_from_assign_to_ticket(Request $request) {
        $dispatch_arr = DispatchTicket::where('driver_name','=','')->orWhere('driver_name','=',null)->get()->toArray();
        if(isset($dispatch_arr) && !empty($dispatch_arr)) {
            foreach($dispatch_arr as $vals) {
                $id = $vals['id'];
                $assign_dispatch_id = $vals['assign_dispatch_id'];
                $assign_dispatch = AssignDispatch::find($assign_dispatch_id);
                $dispatch_ticket = DispatchTicket::find($id);
                $dispatch_ticket->driver_name = $assign_dispatch->user_name;
				$dispatch_ticket->unit_vehicle_number = $assign_dispatch->vehicle_number;
				$dispatch_ticket->contact_number = $assign_dispatch->contact_number;
                $dispatch_ticket->save();
            }
            
        } else {
            echo 'all updated';
            
        }
        die('');
    }
    public function update_prev_emp_vehicle_assignment(Request $request) {
        $start_time = "2023-01-15 12:00:00";
        $user_arr = User::where('type','=','employee')->with('getVehicle')->get()->toArray();
        if(isset($user_arr) && !empty($user_arr)) {
            foreach($user_arr as $user_data) {
                $vehicle_number = $licence_plate = $vin_number = '';
                $user_id = $user_data['id'];
                $user_name = $user_data['name'];
                $user_type = $user_data['type'];
                $user_email = $user_data['email'];
				$user_phone = $user_data['country_code'].' '.$user_data['phone'];
				/*******  get vehicle number if employee selected */
				if(isset($user_data['get_vehicle']) && !empty($user_data['get_vehicle'])) {
					$vehicle_id = $user_data['get_vehicle']['id'];	
					$vehicle_number = $user_data['get_vehicle']['vehicle_number'];	
					$licence_plate = $user_data['get_vehicle']['licence_plate'];	
					$vin_number = $user_data['get_vehicle']['vin_number'];	
                    $vehicle_assignment = new VehicleAssignmentHistory;
                    $vehicle_assignment->created_by = Auth::user()->id;
                    $vehicle_assignment->vehicle_id = $vehicle_id;
                    $vehicle_assignment->vehicle_number = $vehicle_number;
                    $vehicle_assignment->licence_plate = $licence_plate;
                    $vehicle_assignment->vin_number = $vin_number;
                    $vehicle_assignment->start_time = $start_time;
                    $vehicle_assignment->user_id = $user_id;
                    $vehicle_assignment->user_name = $user_name;
                    $vehicle_assignment->user_type = $user_type;
                    $vehicle_assignment->user_email = $user_email;
                    $vehicle_assignment->user_phone = $user_phone;
                    $vehicle_assignment->user_vehicle_type = 'permanent';
                    $vehicle_ass_veh = VehicleAssignmentHistory::where('user_id','=',$user_id)->get()->toArray();
                    if(empty($vehicle_ass_veh)) {
                        $vehicle_assignment->save();
                    }
                } else {}
                /******* end get vehicle number if employee selected */
            }
            
        } else {
            echo 'all updated';
            
        }
        die('');
    }
    public function update_prev_vehicle_airfilter_service_date(Request $request) {
        $vehicle_arr = Vehicle::where('due_air_filter_date',null)->get()->toArray();
        if(isset($vehicle_arr) && !empty($vehicle_arr)) {
            foreach($vehicle_arr as $user_data) {
                $id = $user_data['id'];
                $last_air_filter = $user_data['last_air_filter_date'];
                if($last_air_filter != '' && $last_air_filter != null) {
                    $air_filter_after_days = $user_data['air_filter_after_days'];
                     $last_air_filter = date("Y-m-d H:i:s", strtotime($last_air_filter));
                    $due_air_filter_date = date('Y-m-d', strtotime($last_air_filter. ' + '.$air_filter_after_days.' days'));
                    $vehicle = Vehicle::find($id);
                    $vehicle->due_air_filter_date = $due_air_filter_date;
                    $vehicle->save();
                }
                
				
            }
            
        } else {
            echo 'all updated';
            
        }
        die('');
    }
    public function update_prev_emp_brok_on_ticket(Request $request) {
        $dispatch_arr = DispatchTicket::where('emp_brok_name','=','')->orWhere('emp_brok_name','=',null)->get()->toArray();
        if(isset($dispatch_arr) && !empty($dispatch_arr)) {
            foreach($dispatch_arr as $vals) {
                $id = $vals['id'];
                $assign_user_id = $vals['user_id'];
                $users_data_qry = User::where('id','=', $assign_user_id)->with('getVehicle')->get()->toArray();
				$user_data = $users_data_qry[0];
                $dispatch_ticket = DispatchTicket::find($id);
                $dispatch_ticket->emp_brok_name = $user_data['name'];
				$dispatch_ticket->emp_brok_email = $user_data['email'];
				$dispatch_ticket->emp_brok_phone = $user_data['country_code'].''.$user_data['phone'];
				$dispatch_ticket->emp_brok_hst = $user_data['hst'];
                $dispatch_ticket->save();
            }
            
        } else {
            echo 'all updated';
            
        }
        die('');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Auth::user()->can('viewMenu:ActionDispatch') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            $dispatch = Dispatch::find($id);
            $dispatch->delete();
            // redirect
            Session::flash('message', 'Successfully deleted order!');
			return redirect()->route("dispatches.index");
        }
		else
		{
			return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
		}
    }
}