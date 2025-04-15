<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Dispatch;
use App\Models\Transaction;
use App\Models\DispatchTicket;
use App\Models\AssignDispatch;
use App\Models\MeterHistory;
use App\Models\AssignDispatchBrokerVehicle;
use App\Models\Customer;
use App\Models\FuelHistory;
use App\Models\Vehicle;
use App\Models\Fuel;
use Auth;
use Validator;
use Session;
use DB;
use Carbon\Carbon;
use  Config;
class DispatchTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		if(Auth::user()->can('viewMenu:DispatchTicket') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			$perPage = 15;
			$start_time = $till_date = '';
			$keyword = $request->get('search');
			$dispatch_date = $request->get('dispatch_date');
			$status = $request->get('status');
			$customer = $request->get('customer');
			$ticket = $request->get('ticket');
			$driver = $request->get('driver');
			if($dispatch_date != '') {
				$date_arr = explode("-",$dispatch_date);
				$start_time = $date_arr[0];
				$till_date = $date_arr[1];
			} else{}
			$query =  DispatchTicket::query()->with('getDispatch')->with('getUser')->with('getBrokerVehicle');
				if (!empty($keyword) || $start_time != '' || $status != '' || $customer != '' || $ticket != '' || $driver != '') {
					if($status != ''){
						$query->where('status','=',$status);
					}
					if($ticket != ''){
						$query->where('ticket_number','Like','%'.$ticket.'%');
					}
					if($driver != ''){
						$query->where('driver_name','Like','%'.$driver.'%');
					}
					if($customer != ''){
						$query->WhereHas('getDispatch',function ($query)use($customer)
						{
							$query->where('dispatches.customer_company_name','Like','%'.$customer.'%')
							->orWhere('dispatches.customer_address','Like','%'.$customer.'%');
						});
					}
					if($start_time != '') {
						$date_greter_then = date("Y-m-d", strtotime($start_time)); 
						$date_less_then = date("Y-m-d", strtotime($till_date));
						$query->WhereHas('getDispatch',function ($query)use($date_greter_then,$date_less_then)
						{
							$query->whereDate('start_time','>=',$date_greter_then)
							->whereDate('start_time','<=',$date_less_then);
						});
					}
					if(!empty($keyword)) {
						// 	$query->where('driver_name', 'LIKE', "%$keyword%")
						// 	->orwhere('ticket_number', 'LIKE', "%$keyword%")
						// ->orwhere('unit_vehicle_number', 'LIKE', "%$keyword%")
						// ->orWhereHas('getDispatch',function ($query)use($keyword)
						// {
						// 	$query->where('dispatches.customer_company_name','Like','%'.$keyword.'%');
						// })->orWhereHas('getBrokerVehicle',function ($query)use($keyword)
						// {
						// 	$query->where('assign_dispatch_broker_vehicles.driver_name','Like','%'.$keyword.'%')
						// 	->orWhere('assign_dispatch_broker_vehicles.vehicle_number','Like','%'.$keyword.'%')
						// 	->orWhere('assign_dispatch_broker_vehicles.contact_number','Like','%'.$keyword.'%');
						// });
					}
				$dispatch_tickets = $query->latest()->paginate($perPage); 
				} else {
					$dispatch_tickets = DispatchTicket::latest()->paginate($perPage);
				}
				//$dispatch_tickets = DispatchTicket::latest()->paginate($perPage);
				return view('dispatch_tickets.index',compact('dispatch_tickets'));
		}
		else
		{
			return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
		} 
    }
	public function get_dispatch_users(){
		
		extract($_REQUEST);
		$query =  AssignDispatch::query()->with('getUser');
		if($prev_status == 'completed'  && $dispatch_ticket_id > 0) {
			$query->where('status', "=", 'completed');
			$query->where('user_id', "=", $prev_user_id);
		}  else if(($prev_status == 'pending' || $prev_status == 'submitted') && $prev_user_id > 0 && $dispatch_ticket_id > 0) {
			$query->where('status', "=", 'pending')->orWhere('status', "=", 'submitted');
			$query->where('user_id', "=", $prev_user_id);
		} else {
			$query->where('status', "=", 'accepted');
		}
		$dispatch_tickets = $query->where('dispatch_id', $dispatch_id)
		->where('user_type',$selected_id)
	   ->get()->toArray();
		$html = '<option value="">Select User Name</option>';
		if(!empty($dispatch_tickets)) {
			foreach($dispatch_tickets as $value) {
				$getUser = $value['get_user'];
				$selected_val = '';
				if($prev_user_id == $getUser['id']) {
					$selected_val = 'selected';
				}
				$html .= '<option value="'.$getUser['id'].'" '.$selected_val.'>'.$getUser['name'].'</option>';
			}
		} else {
			$html = '<option value="">No data found</option>';
		}
		return $html;
	}
	public function get_broker_users(){
		
		extract($_REQUEST);
		$query =  AssignDispatch::query()->with('getBrokerVehicle');
				$broker_vehicle = $query->where('dispatch_id', '=', "$dispatch_id")
				->where('user_type', '=', "$selected_id")
               ->get()->toArray();
		$html = '<option value="">Select Driver</option>';
		if(isset($broker_vehicle[0]['get_broker_vehicle']) && !empty($broker_vehicle[0]['get_broker_vehicle'])) {
			foreach($broker_vehicle[0]['get_broker_vehicle'] as $getUser) {
				$vehicle = $getUser['driver_name'].' ( '.$getUser['vehicle_number'].' )';
				$selected_val = '';
				if($broker_vehicle_id == $getUser['id']) {
					$selected_val = 'selected';
				}
				$html .= '<option value="'.$getUser['id'].'" '.$selected_val.'>'.$vehicle.'</option>';
			}
		} else {
			$html = '<option value="">No data found</option>';
		}
		return $html;
	}
	public function get_hour_or_load() {
		extract($_REQUEST);
		$dispatches = Dispatch::where('id','=',$row_id)->get()->toArray();
		$type = ucfirst($dispatches[0]['job_type']);
		return \Response::json(['type' => $type,'success'=>true]);
	}
	public function add_ticket_remark() {
		extract($_REQUEST);
		$dispatch_ticket = DispatchTicket::find($row_id);
		$dispatch_ticket->remark = $remark;
		$dispatch_ticket->save();
		return \Response::json(['success'=>true]);
	}
	public function get_assign_user_vehicle(){
		
		extract($_REQUEST);
		$driver_name = $vehicle_number = $contact_number = $assign_dispatch_id = $current_total_km = '';
		$status = $status_show = '';
		$ass_dis = AssignDispatch::where('dispatch_id','=',$dispatch_id)->where('user_id','=',$user_id)->get()->toArray();
		$ass_dis = $ass_dis['0'];
		$assign_dispatch_id = $ass_dis['id'];
		$dispatch_ticket_arr = DispatchTicket::where('id','=',$dispatch_ticket_id)->where('user_id','=',$user_id)->where('assign_dispatch_id','=',$assign_dispatch_id)->get()->toArray();
		if($user_type == 'employee') {
			$userData = User::where('id','=',$row_id)->with('getVehicle')->get()->toArray();
			$user = $userData[0];
			$status = $user['status'];
			$user_id = $user['id'];
			if($status != 'active' && empty($dispatch_ticket_arr)) {
				$page_url = url('/').'/employees/' . $user_id . '/edit';
				$status_show = '<span style="color:red;">Employee is not active.Kindly verify to continue...<a href="'.$page_url.'">click here</a></span>';
				}
			$driver_name = (null != $ass_dis['user_name']) ? $ass_dis['user_name'] : $user['name'];
			$contact_number = (null != $ass_dis['contact_number']) ? $ass_dis['contact_number'] : $user['phone'];
			$vehicle_number = (null != $ass_dis['vehicle_number']) ? $ass_dis['vehicle_number'] : $user['get_vehicle']['vehicle_number'];
			$current_total_km = (null != $user['get_vehicle']) ? $user['get_vehicle']['total_km'] : '';
		} else {
			$userData = AssignDispatchBrokerVehicle::where('id','=',$row_id)->get()->toArray();
			$user = $userData[0];
			$driver_name = $user['driver_name'];
			$contact_number = $user['contact_number'];
			$vehicle_number = $user['vehicle_number'];
		}
		return \Response::json(['current_total_km' => $current_total_km,'status' => $status,'status_show' => $status_show,'driver_name' => $driver_name,'contact_number' => $contact_number,'vehicle_number' => $vehicle_number,'assign_dispatch_id' => $assign_dispatch_id,'success'=>true]);
	}
	public function calculated_fuel_price($dispatch_id,$ending_km,$starting_km) {
		$amount_paid = $amount = 0;
		$price_added = 'no';
		$dispath_arr = Dispatch::find($dispatch_id);
		$start_time = $dispath_arr->start_time;
		$dispath_date = date("Y-m-d", strtotime($start_time));
		$dispath_date_show = date("d M Y", strtotime($start_time));
		$fuel_arr = Fuel::where('on_date',$dispath_date)->get()->toArray();
		if(isset($fuel_arr) && !empty($fuel_arr)) {
			$fuel_arr = $fuel_arr['0'];
			$price_added = 'yes';
			$amount = $fuel_arr['amount'];
			$km_run = $ending_km - $starting_km;
	   	 	$amount_paid = ($km_run / 2) * $amount;
		} else {}
		
		$res_err = array("amount_paid" => $amount_paid,"fuel_amount" => $amount,"price_added" => $price_added,"dispath_date_show" => $dispath_date_show);
		return json_encode($res_err);

	}
	public function verify_fuel_amount(Request $request) {
		$data  = $request->all();
		$html_show = $invalid_num = $fuel_price = $total_amount_paid = $invalid_num_class_name = '';
		$total_amount = 0;
		$dispatch_id = $data['dispatch_id'];
		$ending_km = $data['ending_km'];
		$starting_km = $data['starting_km'];
		if( is_numeric($ending_km) && is_numeric($starting_km) && is_numeric($dispatch_id)) {
				$fuel_res = $this->calculated_fuel_price($dispatch_id,$ending_km,$starting_km);
				$fuel_res_arr = json_decode($fuel_res,true);
				if($fuel_res_arr['price_added'] == 'no') {
					$dispath_date_show = $fuel_res_arr['dispath_date_show'];
					$invalid_num_class_name = 'fuel_amount_paid';
				$invalid_num = 'Fuel price is not added for date '.$dispath_date_show;
				}
				$total_amount_paid = $fuel_res_arr['amount_paid'];
				$fuel_price = $fuel_res_arr['fuel_amount'];
		} else {
			if(!is_numeric($ending_km)) {
				$invalid_num_class_name = 'ending_km';
				$invalid_num = 'Invalid ending km';
			}
			if(!is_numeric($dispatch_id)) {
				$invalid_num_class_name = 'dispatch_id';
				$invalid_num = 'Please select dispatch';
			}
			if(!is_numeric($starting_km)) {
				$invalid_num_class_name = 'starting_km';
				$invalid_num = 'Invalid starting km';
			}
			if($ending_km < $starting_km) {
				$invalid_num_class_name = 'ending_km';
				$invalid_num = "Ending km can't be less then starting km";
			}
		}
		$total_amount_paid = number_format((float)$total_amount_paid, 2, '.', '');
		return \Response::json(["invalid_num_class_name" => $invalid_num_class_name,"fuel_price" => $fuel_price,'total_amount' => $total_amount_paid,'html_show' => $html_show,'invalid_num' => $invalid_num,'success'=>true]);
	}
	public function load_over_hours_amount(Request $request) {
		$data  = $request->all();
		$html_show = $invalid_num = $hourly_rate = '';
		$total_amount = 0;
		$hour_time = $data['hour_time'];
		$emp_type = strtolower($data['emp_type']);
		$user_id = $data['emp_val'];
		$hour_or_load = $data['hour_or_load'];
		 if($hour_time > 0 && is_numeric($hour_time) && $hour_or_load > 0 && is_numeric($hour_or_load)) {
			$user_query = User::where('id','=',$user_id)->get()->toArray();
		   $user = $user_query[0];
		   $hourly_rate  = $user['hourly_rate'];
		   $total_amount = $hour_or_load * $hourly_rate * $hour_time;
		   $total_amount = number_format((float)$total_amount, 2, '.', '');
		   $html_show = "Hourly Rate :- ".$hourly_rate."<br>";
		   $html_show .= "Total Amount :- ".$total_amount;
		 } else {
			$invalid_num = 'invalid';
		 }
		return \Response::json(["hour_rate" => $hourly_rate,'total_amount' => $total_amount,'html_show' => $html_show,'invalid_num' => $invalid_num,'success'=>true]);
	 
	 }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		if(Auth::user()->can('viewMenu:ActionDispatchTicket') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
			$dispatches = Dispatch::where('status','=','pending')->orderBy('start_time', 'ASC')->get();
			return view('dispatch_tickets.create',compact('dispatches'));
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
	public function update_fuel_history( $row_id ) {
		$fuel_id = $per_liter_amount = 0;
				$dispatch_ticket = DispatchTicket::where('id','=',$row_id)->with('getDispatch')->with('getAssignDispatch')->get()->toArray();
				$dispatch_ticket = $dispatch_ticket[0];
				$get_assign_dispatch = $dispatch_ticket['get_assign_dispatch'];
				$get_dispatch = $dispatch_ticket['get_dispatch'];
				$start_time = $get_dispatch['start_time'];
				$fuel_qty = $dispatch_ticket['fuel_qty'];
				$starting_km = $dispatch_ticket['starting_km'];
				$fuel_card_number = $dispatch_ticket['fuel_card_number'];
				$fuel_tbl_amount_date = $on_date = date('Y-m-d',strtotime($start_time));
				if($fuel_qty > 0) {
				$vehicle_id = $get_assign_dispatch['vehicle_id'];
				$vehicle_number = $get_assign_dispatch['vehicle_number'];
				$fuel_arr = Fuel::where('on_date',$on_date)->get()->toArray();
				if(isset($fuel_arr) && !empty($fuel_arr)) {
                    $fuel_arr = $fuel_arr['0'];
                    $price_added = 'yes';
                    $fuel_id = $fuel_arr['id'];
                    $per_liter_amount = $fuel_arr['amount'];
                } else {}
				 /********  get last date before on date  ***/
				 $fuel_last_one_date_history = FuelHistory::where('vehicle_id',$vehicle_id)->where('on_date','<',$on_date)->orderBy('on_date','desc')->limit(1)->get()->toArray();
				
				 if(isset($fuel_last_one_date_history) && !empty($fuel_last_one_date_history)) {
				  $fuel_last_one_date_history = $fuel_last_one_date_history[0];
				  $dispatch_ticket_id = $fuel_last_one_date_history['dispatch_ticket_id'];
					  $id = $fuel_last_one_date_history['id'];
					  $fuel_history_arr = FuelHistory::find($id);
					  $prev_starting_km = $fuel_last_one_date_history['starting_km'];
					  $prev_fuel_qty = $fuel_last_one_date_history['fuel_qty'];
					  $prev_per_liter_amount = $fuel_last_one_date_history['per_liter_amount'];
					  if($starting_km <= $prev_starting_km) {
					   } else {
						  /***********current starting km is ending for prev record */
						  $ending_km = $starting_km;
						  /***********end current starting km is ending for prev record */
						  $fuel_history_arr->ending_km = $starting_km;
						  $total_km = $ending_km - $prev_starting_km;
						  $fuel_history_arr->total_km = $total_km;
						  $fuel_economy = $total_km / $prev_fuel_qty;
						  $fuel_history_arr->fuel_economy = number_format((float)$fuel_economy, 2, '.', '');
						  $amount_paid = ($total_km / 2) * $prev_per_liter_amount;
						 // $fuel_history_arr->fuel_expense = $amount_paid;
						   $fuel_history_arr->save();
					   }
				 } else {}
				  /******** end get last date before on date  ***/
				  $fuel_history = new FuelHistory;
				$fuel_history->created_by = Auth::user()->id;
				$fuel_history->vehicle_id = $vehicle_id;
				$fuel_history->on_date = $on_date;
				$fuel_history->fuel_tbl_amount_date = $on_date;
				$fuel_history->fuel_id = $fuel_id;
				$fuel_history->per_liter_amount = $per_liter_amount;
				$fuel_history->fuel_qty = $fuel_qty;
				$fuel_history->starting_km = $starting_km;
				$fuel_history->dispatch_ticket_id = $row_id;
				$fuel_history->fuel_expense = $fuel_qty * $per_liter_amount;
				$fuel_history->vehicle_number = $vehicle_number;
				$fuel_history->fuel_card_number = $fuel_card_number;
				$fuel_history->comment = 'dispatch ticket added';
				$fuel_history->source = 'dispatch';
				$fuel_history->save();
				/******* get records greter then on starting date */
                $get_records_greter_history = FuelHistory::where('vehicle_id',$vehicle_id)->where('on_date','>=',$on_date)->orderBy('on_date','desc')->get()->toArray();
                $total_records = count($get_records_greter_history);
                if($total_records > 0) {
                   $last_top_index = $total_records - 2;
                    for($loop_start = 0; $loop_start <= $last_top_index;$loop_start++) {
                        $loop_next_index = $loop_start + 1;
                        $id = $get_records_greter_history[$loop_next_index]['id'];
                        $fuel_history_arr = FuelHistory::find($id);
                        $current_starting_km = $get_records_greter_history[$loop_start]['starting_km'];
                        $prev_starting_km = $get_records_greter_history[$loop_next_index]['starting_km'];
                        $prev_per_liter_amount = $get_records_greter_history[$loop_next_index]['per_liter_amount'];
                        $prev_fuel_qty = $get_records_greter_history[$loop_next_index]['fuel_qty'];
                        /***********current starting km is ending for prev record */
                        $ending_km = $current_starting_km;
                        /***********end current starting km is ending for prev record */
                        $fuel_history_arr->ending_km = $ending_km;
                        $total_km = $ending_km - $prev_starting_km;
                        $fuel_history_arr->total_km = $total_km;
                        $fuel_economy = $total_km / $prev_fuel_qty;
                        $fuel_history_arr->fuel_economy = number_format((float)$fuel_economy, 2, '.', '');
                        $amount_paid = ($total_km / 2) * $prev_per_liter_amount;
                       // $fuel_history_arr->fuel_expense = $amount_paid;
                        $fuel_history_arr->save();

                    }
                } else {}
                /******* end get records greter then on starting date */
			}
	}
    public function update_prev_vehicle_fuel_update(Request $request)
    {
		$dispatch_arr = DispatchTicket::where('fuel_qty','>','0')->where('status','=','completed')->get()->toArray();
        if(isset($dispatch_arr) && !empty($dispatch_arr)) {
			foreach($dispatch_arr as $vals) {
                $id = $vals['id'];
				DB::table('fuel_histories')->where('dispatch_ticket_id', $id)->delete();
				$this->update_fuel_history($id);
			}
		}
	}
	public function store(Request $request)
    {
		if(Auth::user()->can('viewMenu:ActionDispatchTicket') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
			$data  = $request->all();
			// validate
			$rules = [
				'dispatch_id' => 'required',
				'shift_type' => 'required',
				'user_type'  => 'required',
				'user_id'  => 'required',
				'driver_name'  => 'required',
				'status'  => 'required',
				'starting_km'  => 'required|numeric',
				'ending_km'  => 'required|numeric',
				//'fuel_qty'  => 'required|numeric',
				//'fuel_card_number'  => 'required',
			// 'def_qty'  => 'required|numeric',
			// 'gas_station_location'  => 'required',
				'ticket_number'  => 'required',
				'hour_or_load'  => 'required',
				//'fuel_receipt' => ['required','max:2048'],
				//'def_receipt' => ['required','max:2048'],
				'ticket_img' => ['required','max:2048'],
			];
			$message = [
				'dispatch_id.required' => 'Please select Dispatch.',
				'shift_type.required' => 'Shift type is required.',
				'user_type.required' => 'User type is required.',
				'user_id.required' => 'User is required.',
				'starting_km.required' => 'Starting Km is required.',
				'starting_km.numeric' => 'Starting Km should be numeric.',
				'ending_km.required' => 'Ending Km is required.',
				'ending_km.numeric' => 'Ending Km should be numeric.',
				'fuel_qty.required' => 'Fuel Quantity is required.',
				'fuel_qty.numeric' => 'Fuel Quantity should be numeric.',
				'fuel_card_number.required' => 'Fuel Card number is required.',
				'def_qty.numeric' => 'Def Quantity should be numeric.',
				'gas_station_location.required' => 'Gas station location is required.',
				'ticket_number.required' => 'Ticket Number is required.',
				'hour_or_load.required' => 'Hour / Load price is required.',
			];
			$validator = Validator::make($data, $rules, $message);

			if ($validator->fails()) {
				return back()->withInput()
					->withErrors($validator);
			} else {
				$error_got = '';
				/*** If broker then vehicle should be as required ***/
				$user_type = $request->user_type;
				$broker_vehicle_id = $request->broker_vehicle_id;
				$assign_dispatch_id = $request->assign_dispatch_id;
				if($user_type == 'broker') {
					if($broker_vehicle_id == '') {
						$error_got = 'yes';
						$validator->getMessageBag()->add('broker_vehicle_id', 'Select Broker vehicle');
					} else {
						$vehicle_id_added = DispatchTicket::where('broker_vehicle_id','=',$broker_vehicle_id)->get()->toArray();
						if(!empty($vehicle_id_added)) {
							$error_got = 'yes';
							$validator->getMessageBag()->add('broker_vehicle_id', 'Driver data already added');
						}
					}
				}
				if($user_type == 'employee') {
					$user_id = $request->user_id;
					$vehicle_id_added = DispatchTicket::where('assign_dispatch_id','=',$assign_dispatch_id)->get()->toArray();
						if(!empty($vehicle_id_added)) {
							$error_got = 'yes';
							$validator->getMessageBag()->add('user_id', 'Employee data already added');
						}
						$userData = User::where('id','=',$user_id)->with('getVehicle')->get()->toArray();
						$user = $userData[0];
						$status = $user['status'];
						if($status != 'active' && empty($dispatch_ticket_arr)) {
							$page_url = url('/').'/employees/' . $user_id . '/edit';
							$status_show = '<span style="color:red;">Employee is not active.Kindly verify to continue...<a href="'.$page_url.'">click here</a></span>';
							$error_got = 'yes';
							$validator->getMessageBag()->add('user_id', '');	
						}
				}
				if($request->starting_km > $request->ending_km) {
					$error_got = 'yes';
					$validator->getMessageBag()->add('starting_km', 'Starting Km cant be more then Ending KM');
				}
				/*** end If broker then vehicle should be as required ***/
				/*******it is to add fuel price ****/
				$fuel_amount_paid = 0;
				$dispatch_id = $request->dispatch_id;
				$ending_km = $request->ending_km;
				$starting_km = $request->starting_km;
				$fuel_res = $this->calculated_fuel_price($dispatch_id,$ending_km,$starting_km);
				$fuel_res_arr = json_decode($fuel_res,true);
				if($fuel_res_arr['price_added'] == 'no') {
					$dispath_date_show = $fuel_res_arr['dispath_date_show'];
				$invalid_num = 'Fuel price is not added for date '.$dispath_date_show;
				$error_got = 'yes';
				$validator->getMessageBag()->add('fuel_amount_paid', $invalid_num);
				} else{}
				$fuel_amount_paid = $fuel_res_arr['amount_paid'];
				
				/*******end it is to add fuel price ****/
				$data_array = array();
				$data_array['dispatch_id'] = $dispatch_id;
				$dispatch_query = Dispatch::where('id','=',$dispatch_id)->get()->toArray();
				$displatch_data = $dispatch_query[0];
				$disp_job_type = $displatch_data['job_type'];
				$disp_job_rate = $displatch_data['job_rate'];
				$customer_id = $displatch_data['customer_id'];
				/*** laod rate will come from dispatch for all users  ****/
				$user_load_rate = $displatch_data['employee_rate'];
				/*** end laod rate will come from dispatch for all users  ****/
				$start_time = $displatch_data['start_time'];
				$on_date = date('Y-m-d',strtotime($start_time));
				$admin_user_query = User::where('type','=','admin')->get()->toArray();
				$admin_user = $admin_user_query[0];
				$admin_id = $admin_user['id'];
				
				$user_id = $request->user_id;
				$user_query = User::where('id','=',$user_id)->with('getVehicle')->get()->toArray();
				$user = $user_query[0];
				/*******add imp fields in ticket table */
				$emp_brok_name = $user['name'];
				$emp_brok_email = $user['email'];
				$emp_brok_phone = $user['country_code'].''.$user['phone'];
				$emp_brok_hst = $user['hst'];
				/*******end add imp fields in ticket table */
				$hourly_rate = $data_array['emp_brok_hour_rate'] = $user['hourly_rate'];
				//$load_rate = $data_array['emp_brok_load_rate'] = $user['load_per'];
				$load_rate = $data_array['emp_brok_load_rate'] = $user_load_rate;
				
				$ticket_hr_ld_rate = $request->hour_or_load;
				$income = $ticket_hr_ld_rate * $disp_job_rate;
				if($fuel_amount_paid > 0 && $income < $fuel_amount_paid) {
					$invalid_num = 'Fuel price is '.$fuel_amount_paid.'.Kindly check your KM';
				$error_got = 'yes';
				$validator->getMessageBag()->add('starting_km', $invalid_num);
				} else {}
				if($error_got == 'yes') {
					return back()->withInput()
					->withErrors($validator);
				} else {}
				
				/*** uplaod images ***/
				$uda_for_iamge_name_concat = $request->user_id.''.$dispatch_id.''.$assign_dispatch_id;
				$dispatch_ticket = new DispatchTicket;
				
				$data_array['fuel_amount_paid'] = $fuel_amount_paid;
				if($request->file('fuel_receipt') != null){
					$file= $request->file('fuel_receipt');
					$exte = $file->extension();
					$filename = date('YmdHis').''.$uda_for_iamge_name_concat.'.'.$exte;
					$file-> move(public_path('images/fuel_receipt'), $filename);
					$data_array['fuel_receipt'] = $filename;
				} else {
					$data_array['fuel_receipt'] = '';
				}
				if($request->file('def_receipt') != null){
					$file= $request->file('def_receipt');
					$exte = $file->extension();
					$filename = date('YmdHis').''.$uda_for_iamge_name_concat.'.'.$exte;
					$file-> move(public_path('images/def_receipt'), $filename);
					$data_array['def_receipt'] = $filename;
				} else {
					$data_array['def_receipt'] = '';
				}
				if($request->file('ticket_img')){
					$file= $request->file('ticket_img');
					$exte = $file->extension();
					$filename = date('YmdHis').''.$uda_for_iamge_name_concat.'.'.$exte;
					$file-> move(public_path('images/ticket_img'), $filename);
					$data_array['ticket_img'] = $filename;
				}
				/*** end upload images ***/
				$expense = 0;
				/*** it is added  if we have to add hours over load */
				$expense_without_emploee_hour_over_load = 0;
				$emploee_hour_over_load = ( null != $request->emploee_hour_over_load && is_numeric($request->emploee_hour_over_load)) ? $request->emploee_hour_over_load : 0;
				$emploee_hourly_rate_over_load = $hourly_rate;
				$emploee_hour_over_load_amount = $emploee_hour_over_load * $emploee_hourly_rate_over_load * $ticket_hr_ld_rate;
				$emploee_hour_over_load_amount = number_format((float)$emploee_hour_over_load_amount, 2, '.', '');
				$data_array['emploee_hour_over_load'] = $emploee_hour_over_load;
				$data_array['emploee_hourly_rate_over_load'] = $emploee_hourly_rate_over_load;
				$data_array['emploee_hour_over_load_amount'] = $emploee_hour_over_load_amount;
				/*** end it is added  if we have to add hours over load */
				if($disp_job_type == 'hourly') {
					$expense = $ticket_hr_ld_rate * $hourly_rate;
				} else {
					/*** it is added  if we have to add hours over load */
					if($emploee_hour_over_load_amount > 0) {
						$expense = $emploee_hour_over_load_amount;
						$expense_without_emploee_hour_over_load = $ticket_hr_ld_rate * $load_rate;
					} else {
						$expense = $ticket_hr_ld_rate * $load_rate;
						$expense_without_emploee_hour_over_load = 0;
					}
					/*** it is added  if we have to add hours over load */
					// $expense = $ticket_hr_ld_rate * $load_rate;
					
				}
				/*** it is added  if we have to add hours over load */
				$data_array['expense_without_emploee_hour_over_load'] = $expense_without_emploee_hour_over_load;
				/*** it is added  if we have to add hours over load */
				$profit = 0;
				if($expense < $income) {
					$profit = $income - $expense - $fuel_amount_paid;
				} else if($income < $expense) {
					$profit = $expense - $income - $fuel_amount_paid;
				} else {}
				$default_ticket_number = 'JPGT'.date('Ymdhis');
				$total_km = $ending_km - $starting_km;
				$data_array['created_by'] = Auth::user()->id;
				$ticket_status = $request->status;
				$data_array['assign_dispatch_id'] = $assign_dispatch_id;
				$data_array['broker_vehicle_id'] = ($broker_vehicle_id == '') ? 0 : $broker_vehicle_id;
				$data_array['shift_type'] = $request->shift_type;
				$data_array['user_type'] = $request->user_type;
				$data_array['user_id'] = $user_id;
				$data_array['driver_name'] = $request->driver_name;
				$data_array['unit_vehicle_number'] = $request->unit_vehicle_number;
				$data_array['contact_number'] = $request->contact_number;
				$data_array['starting_km'] = $starting_km;
				$data_array['ending_km'] = $ending_km;
				$data_array['total_km'] = $total_km;
				$data_array['fuel_qty'] = ($request->fuel_qty == '' || $request->fuel_qty == null) ? 0 : $request->fuel_qty;
				$data_array['fuel_card_number'] = $request->fuel_card_number;
				$data_array['def_qty'] = ($request->def_qty == '' || $request->def_qty == null) ? 0 : $request->def_qty;
				$data_array['gas_station_location'] = $request->gas_station_location;
				$data_array['ticket_number'] = $request->ticket_number;
				$data_array['status'] = $ticket_status;
				$data_array['income'] = $income;
				$data_array['expense'] = $expense;
				$data_array['profit'] = $profit;
				$data_array['hour_or_load'] = $ticket_hr_ld_rate;
				$data_array['default_ticket_number'] = $default_ticket_number;
				$data_array['hour_or_load_integer'] = (is_numeric($ticket_hr_ld_rate)) ? $ticket_hr_ld_rate : 0;
				$data_array['emp_brok_name'] = $emp_brok_name;
				$data_array['emp_brok_email'] = $emp_brok_email;
				$data_array['emp_brok_phone'] = $emp_brok_phone;
				$data_array['emp_brok_hst'] = $emp_brok_hst;
				$data = DispatchTicket::Create($data_array);
				/*** get user total income ******/
				if($data->id > 0) {
					$dispatch_ticket_id = $data->id;
					/*** it is to update relevant table  ***********/
					$get_user_income = DispatchTicket::where('user_id', $user_id)->where('status', 'completed')->sum('expense');
					$get_admin_income = DispatchTicket::where('status', 'completed')->sum('income');
					$user_last_amount = $admin_last_amount = 0;
					/*** Transaction entry while tocket completed  ****/
					$get_user_trans = Transaction::where('user_id', $user_id)->where('user_type', 'employee')->orderBy('id', 'desc')->take(1)->get()->toArray();
					$get_admin_trans  = Transaction::where('user_id', $admin_id)->where('user_type', 'employee')->orderBy('id', 'desc')->take(1)->get()->toArray();
					if(isset($get_user_trans) && !empty($get_user_trans)) {
						$user_last_amount = $get_user_trans[0]['total_amount'];
					}else {}
					if(isset($get_admin_trans) && !empty($get_admin_trans)) {
						$admin_last_amount = $get_admin_trans[0]['total_amount'];
					}else {}
					$user_total_amount = $expense + $user_last_amount;
					$admin_total_amount = $income + $admin_last_amount;
					
					$date = Carbon::now();
					if($ticket_status == 'completed') {
						
						/*** it is to update vehicle km  *********/
						$user_type = $user['type'];
						$vehicle_id = $user['vehicle_id'];
						if($user_type == 'employee') {
							$vehicles = Vehicle::find($vehicle_id);
							$vehicle_number = $vehicles->vehicle_number;
							$total_km = (null != $vehicles->total_km && $vehicles->total_km >= 0) ? $vehicles->total_km : 0;
							$add_ending_km = $ending_km;
							if($ending_km < $total_km) {
								$add_ending_km = $total_km;
							} else {}
							$vehicles->total_km = $add_ending_km;
							$vehicles->save();
							/********* it to add fuel history */
					$this->update_fuel_history($dispatch_ticket_id);
					/********* end it to add fuel history */
							/** add meter history */
						$meter_history = new MeterHistory;
						$meter_history->created_by = Auth::user()->id;
						$meter_history->vehicle_id = $vehicle_id;
						$meter_history->dispatch_ticket_id = $dispatch_ticket_id;
						$meter_history->on_date = $on_date;
						$meter_history->vehicle_number = $vehicle_number;
						$meter_history->starting_km = $starting_km;
						$meter_history->ending_km = $ending_km;
						$meter_history->total_km = $ending_km;
						$meter_history->comment = 'dispatch ticket completed';
						$meter_history->source = 'Dispatch Ticket';
						$meter_history->save();
						/** add meter history */
						} else{}
						/*** end it is to update vehicle km  *********/
						/*** Transaction entry while ticket completed  ****/
						$user_date = date('Ymdhis');
						$admin_date = $user_date.'1';
					$default_transaction_number = 'JPGTN'.$user_date;
					$user_tran_data = array(
					'default_transaction_number' => $default_transaction_number,
					'user_id' => $user_id,
					'dispatch_id' => $dispatch_id,
					'assign_dispatch_id' => $assign_dispatch_id,
					'dispatch_ticket_id' => $dispatch_ticket_id,
					'amount' => $expense,
					'total_amount' => $user_total_amount,
					'message' => 'Amount credit on dispatch complete',
					);
					$user_trans = Transaction::Create($user_tran_data);
					
					
					$default_admin_trans_number = 'JPGTN'.$admin_date;
					$admin_tran_data = array(
					'default_transaction_number' => $default_admin_trans_number,
					'user_id' => $admin_id,
					'dispatch_id' => $dispatch_id,
					'assign_dispatch_id' => $assign_dispatch_id,
					'dispatch_ticket_id' => $dispatch_ticket_id,
					'amount' => $income,
					'total_amount' => $admin_total_amount,
					'message' => 'Amount credit on dispatch complete',
					);
					$admin_trans = Transaction::Create($admin_tran_data);
					/*** end Transaction entry while ticket completed  ****/
						$where = array('user_id' => $user_id,'dispatch_id' => $dispatch_id);
						$update_completed = array('status' => 'completed','completed_date' => $date);
						DB::table('assign_dispatches')
						->where($where)
						->update($update_completed);
					} else {
						$where = array('user_id' => $user_id,'dispatch_id' => $dispatch_id);
						$update_completed = array('status' => 'submitted');
						DB::table('assign_dispatches')
						->where($where)
						->update($update_completed);
					}
					/***it is for user update  ***/
					$user_where = array('id' => $user_id);
					$user_update = array('total_income' => $get_user_income,'current_amount' => $user_total_amount);
					DB::table('users')
					->where($user_where)
					->update($user_update);
					
					$admin_user_where = array('id' => $admin_id);
					$admin_user_update = array('total_income' => $get_admin_income,'current_amount' => $admin_total_amount);
					DB::table('users')
					->where($admin_user_where)
					->update($admin_user_update);
					/***end it is for user update  ***/
					/**if all asign user  completed ticket make order as completed  ****/
					$dispatch_status_data = 'pending';
					$completed_date = '';
					$get_incompelete_dispatch = AssignDispatch::where('dispatch_id', $dispatch_id)->where('status','!=', 'completed')->where('status','!=', 'cancelled')->count();
					$dispatch_status_where = array('id' => $dispatch_id);
					if($get_incompelete_dispatch == 0 || $get_incompelete_dispatch == '0') {
						$dispatch_status_data = 'completed';
						$completed_date = $date;
						$dispatch_status_update = array('status' => $dispatch_status_data,'completed_date' => $completed_date);
						DB::table('dispatches')
						->where($dispatch_status_where)
						->update($dispatch_status_update);
						/*** update customer amount data in transaction and customer table ****/
						$customer_last_amount = 0;
						$get_customer_trans = Transaction::where('user_id', $customer_id)->where('user_type', 'customer')->orderBy('id', 'desc')->take(1)->get()->toArray();
						if(isset($get_customer_trans) && !empty($get_customer_trans)) {
						$customer_last_amount = $get_customer_trans[0]['total_amount'];
					}else {}
						$dispatch_total_sum = DB::table('dispatches')
										->join('dispatch_tickets', 'dispatches.id', '=', 'dispatch_tickets.dispatch_id')
										->where('dispatches.status', '=', 'completed')
										->where('dispatches.customer_id', '=', $customer_id)
										->sum('dispatch_tickets.income');
						$dispatch_sum = DB::table('dispatches')
										->join('dispatch_tickets', 'dispatches.id', '=', 'dispatch_tickets.dispatch_id')
										->where('dispatches.status', '=', 'completed')
										->where('dispatches.id', '=', $dispatch_id)
										->sum('dispatch_tickets.income');
						$customer_current_amount = $customer_last_amount + $dispatch_sum;
						$customer_where = array('id' => $customer_id);
						$customer_update = array('total_amount' => $dispatch_total_sum,'current_amount' => $customer_current_amount);
						DB::table('customers')
						->where($customer_where)
						->update($customer_update);
						$cutomer_date = $user_date.'2';
					$default_customer_trans_number = 'JPGTN'.$cutomer_date;
					$customer_tran_data = array(
					'default_transaction_number' => $default_customer_trans_number,
					'user_id' => $customer_id,
					'dispatch_id' => $dispatch_id,
					'user_type' => 'customer',
					'amount' => $dispatch_sum,
					'total_amount' => $customer_current_amount,
					'message' => 'Amount credit on dispatch complete',
					);
					$customer_trans = Transaction::Create($customer_tran_data);
						/*** end update customer amount data in transaction and customer table ****/
					} else {
						$dispatch_status_update = array('status' => $dispatch_status_data);
						DB::table('dispatches')
						->where($dispatch_status_where)
						->update($dispatch_status_update);
					}
					
					
					/**end if all asign user  completed ticket make order as completed  ****/
					// redirect
				Session::flash('message', 'Successfully ticket added!');
				return redirect("dispatch_tickets/")->with('message', 'Dispatch ticket added successfully!');
				} else {
					// redirect
				Session::flash('message', 'Some error occured!');
				return redirect("dispatch_tickets/create")->with('message', 'Some error occured try again!');
				}
				
				/*** end get user total income ******/
				
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
		if(Auth::user()->can('viewMenu:DispatchTicket') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
			$dispatch_tickets = DispatchTicket::find($id);
			return view('dispatch_tickets.show',compact('dispatch_tickets'));
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
		if(Auth::user()->can('viewMenu:ActionDispatchTicket') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
			$dispatches = Dispatch::orderBy('start_time', 'ASC')->get();
			$dispatch_tickets = DispatchTicket::find($id);
			/*if($dispatch_tickets->status == 'pending') {
				return view('dispatch_tickets.edit',compact('dispatch_tickets','dispatches'));
			} else {
				Session::flash('message', 'Ticket is already completed!');
				return redirect("dispatch_tickets/")->with('message', 'Ticket is already completed!!');
			} */
			return view('dispatch_tickets.edit',compact('dispatch_tickets','dispatches'));
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
	 
	 public function update_status(Request $request) {
		 $data  = $request->all();
		 $dispatch_ticket_id = $id = $data['id'];
		 $ticket_status = 'completed';
		 $user_id = $data['user_id'];
		 $dispatch_id = $data['dispatch_id'];
		
			$dispatch_query = Dispatch::where('id','=',$dispatch_id)->get()->toArray();
			$dispatch_ticket_query = DispatchTicket::where('id','=',$dispatch_ticket_id)->get()->toArray();
			$displatch_data = $dispatch_query[0];
			$displatch_ticket_data = $dispatch_ticket_query[0];
			
			$disp_job_type = $displatch_data['job_type'];
			$disp_job_rate = $displatch_data['job_rate'];
			$customer_id = $displatch_data['customer_id'];
			/*** laod rate will come from dispatch for all users  ****/
			$user_load_rate = $displatch_data['employee_rate'];
			/*** end laod rate will come from dispatch for all users  ****/
			$start_time = $displatch_data['start_time'];
			$on_date = date('Y-m-d',strtotime($start_time));
			$admin_user_query = User::where('type','=','admin')->get()->toArray();
			$admin_user = $admin_user_query[0];
			$admin_id = $admin_user['id'];
			
			$user_query = User::where('id','=',$user_id)->with('getVehicle')->get()->toArray();
			$user = $user_query[0];
			/*******add imp fields in ticket table */
			$emp_brok_name = $user['name'];
			$emp_brok_email = $user['email'];
			$emp_brok_phone = $user['country_code'].''.$user['phone'];
			$emp_brok_hst = $user['hst'];
			/*******end add imp fields in ticket table */
			$hourly_rate = $user['hourly_rate'];
			$load_rate = $user_load_rate;
			
			$ticket_hr_ld_rate = $displatch_ticket_data['hour_or_load'];
			$assign_dispatch_id = $displatch_ticket_data['assign_dispatch_id'];
			$ending_km = $displatch_ticket_data['ending_km'];
			$starting_km = $displatch_ticket_data['starting_km'];
			/*******it is to add fuel price ****/
			$fuel_amount_paid = 0;
			$fuel_res = $this->calculated_fuel_price($dispatch_id,$ending_km,$starting_km);
			$fuel_res_arr = json_decode($fuel_res,true);
			if($fuel_res_arr['price_added'] == 'no') {
				$dispath_date_show = $fuel_res_arr['dispath_date_show'];
			$invalid_num = 'Fuel price is not added for date '.$dispath_date_show;
			return \Response::json(['error'=> $invalid_num,'status' => 'err','success'=>true]);
			} else{}
			$fuel_amount_paid = $fuel_res_arr['amount_paid'];
			$income = $displatch_ticket_data['income'];
			if($fuel_amount_paid > 0 && $income < $fuel_amount_paid) {
				$invalid_num = 'Fuel expense '.$fuel_amount_paid.' seems wrong.Kindly validate.';
			return \Response::json(['error'=> $invalid_num,'status' => 'err','success'=>true]);
			} else {}
			/*******end it is to add fuel price ****/
			/*** it is to update vehicle km  *********/
			$user_type = $user['type'];
			$vehicle_id = $user['vehicle_id'];
			DB::table('meter_histories')->where('dispatch_ticket_id', $dispatch_ticket_id)->delete();
			DB::table('fuel_histories')->where('dispatch_ticket_id', $dispatch_ticket_id)->delete();
			if($user_type == 'employee') {
				$vehicles = Vehicle::find($vehicle_id);
				$total_km = (null != $vehicles->total_km && $vehicles->total_km >= 0) ? $vehicles->total_km : 0;
				$vehicle_number = $vehicles->vehicle_number;
				$add_ending_km = $ending_km;
				if($ending_km < $total_km) {
					$add_ending_km = $total_km;
				} else {}
				$vehicles->total_km = $add_ending_km;
				$vehicles->save();
				/********* it to add fuel history */
				$this->update_fuel_history($dispatch_ticket_id);
				/********* end it to add fuel history */
				/** add meter history */
				
				$meter_history = new MeterHistory;
				$meter_history->created_by = Auth::user()->id;
				$meter_history->vehicle_id = $vehicle_id;
				$meter_history->dispatch_ticket_id = $dispatch_ticket_id;
				$meter_history->on_date = $on_date;
				$meter_history->vehicle_number = $vehicle_number;
				$meter_history->starting_km = $starting_km;
				$meter_history->ending_km = $ending_km;
				$meter_history->total_km = $ending_km;
				$meter_history->comment = 'dispatch ticket completed';
				$meter_history->source = 'Dispatch Ticket';
				$meter_history->save();
				/** end add meter history */
			} else{}
			/*** end it is to update vehicle km  *********/
			$income = $ticket_hr_ld_rate * $disp_job_rate;
			DB::table('transactions')->where('user_id', $user_id)
									->where('dispatch_id', $dispatch_id)
									->where('assign_dispatch_id', $assign_dispatch_id)
									->where('dispatch_ticket_id', $dispatch_ticket_id)
									->where('user_type', 'employee')->delete();
					DB::table('transactions')->where('user_id', $admin_id)
									->where('dispatch_id', $dispatch_id)
									->where('assign_dispatch_id', $assign_dispatch_id)
									->where('dispatch_ticket_id', $dispatch_ticket_id)
									->where('user_type', 'employee')->delete();
					DB::table('transactions')->where('user_id', $customer_id)
									->where('dispatch_id', $dispatch_id)
									->where('user_type', 'customer')->delete();
			$expense = 0;
			/*** it is added  if we have to add hours over load */
			$expense_without_emploee_hour_over_load = 0;
			$emploee_hour_over_load = $displatch_ticket_data['emploee_hour_over_load'];
			$emploee_hourly_rate_over_load = $hourly_rate;
			$emploee_hour_over_load_amount = $emploee_hour_over_load * $emploee_hourly_rate_over_load * $ticket_hr_ld_rate;
			$emploee_hour_over_load_amount = number_format((float)$emploee_hour_over_load_amount, 2, '.', '');
			$data_array['emploee_hour_over_load'] = $emploee_hour_over_load;
			$data_array['emploee_hourly_rate_over_load'] = $emploee_hourly_rate_over_load;
			$data_array['emploee_hour_over_load_amount'] = $emploee_hour_over_load_amount;
			/*** end it is added  if we have to add hours over load */
			if($disp_job_type == 'hourly') {
				$expense = $ticket_hr_ld_rate * $hourly_rate;
			} else {
				/*** it is added  if we have to add hours over load */
				if($emploee_hour_over_load_amount > 0) {
					$expense = $emploee_hour_over_load_amount;
					$expense_without_emploee_hour_over_load = $ticket_hr_ld_rate * $load_rate;
				} else {
					$expense = $ticket_hr_ld_rate * $load_rate;
					$expense_without_emploee_hour_over_load = 0;
				}
				/*** it is added  if we have to add hours over load */
				// $expense = $ticket_hr_ld_rate * $load_rate;
				
			}
			/*** it is added  if we have to add hours over load */
			$data_array['expense_without_emploee_hour_over_load'] = $expense_without_emploee_hour_over_load;
			/*** it is added  if we have to add hours over load */
			$data_array['status'] = $ticket_status;
			$data_array['expense'] = $expense;
			$profit = 0;
				if($expense < $income) {
					$profit = $income - $expense - $fuel_amount_paid;
				} else if($income < $expense) {
					$profit = $expense - $income - $fuel_amount_paid;
				} else {}
			$data_array['profit'] = $profit;
			$data_array['fuel_amount_paid'] = $fuel_amount_paid;
			$data_array['emp_brok_name'] = $emp_brok_name;
			$data_array['emp_brok_email'] = $emp_brok_email;
			$data_array['emp_brok_phone'] = $emp_brok_phone;
			$data_array['emp_brok_hst'] = $emp_brok_hst;
		 DispatchTicket::updateOrCreate(array('id' => $dispatch_ticket_id), $data_array);

				/*** it is to update relevant table  ***********/
				$get_user_income = DispatchTicket::where('user_id', $user_id)->where('status', 'completed')->sum('expense');
				$get_admin_income = DispatchTicket::where('status', 'completed')->sum('income');
				$user_last_amount = $admin_last_amount = 0;
				/*** Transaction entry while tocket completed  ****/
				$get_user_trans = Transaction::where('user_id', $user_id)->where('user_type', 'employee')->orderBy('id', 'desc')->take(1)->get()->toArray();
				$get_admin_trans  = Transaction::where('user_id', $admin_id)->where('user_type', 'employee')->orderBy('id', 'desc')->take(1)->get()->toArray();
				if(isset($get_user_trans) && !empty($get_user_trans)) {
					 $user_last_amount = $get_user_trans[0]['total_amount'];
				 }else {}
				 if(isset($get_admin_trans) && !empty($get_admin_trans)) {
					 $admin_last_amount = $get_admin_trans[0]['total_amount'];
				 }else {}
				 $user_total_amount = $expense + $user_last_amount;
				$admin_total_amount = $income + $admin_last_amount;
				  
				$date = Carbon::now();
				/***it is for user update  ***/
				$user_where = array('id' => $user_id);
				$user_update = array('total_income' => $get_user_income,'current_amount' => $user_total_amount);
				DB::table('users')
				->where($user_where)
				->update($user_update);
				
				$admin_user_where = array('id' => $admin_id);
				$admin_user_update = array('total_income' => $get_admin_income,'current_amount' => $admin_total_amount);
				DB::table('users')
				->where($admin_user_where)
				->update($admin_user_update);
				/***end it is for user update  ***/
				if($ticket_status == 'completed') {
				$user_trans_id = $admin_trans_id = '';
				/*** Transaction entry while ticket completed  ****/
				$user_date = date('Ymdhis');
				$admin_date = $user_date.'1';
				 $default_transaction_number = 'JPGTN'.$user_date;
				 $user_tran_data = array(
				 'default_transaction_number' => $default_transaction_number,
				 'user_id' => $user_id,
				 'dispatch_id' => $dispatch_id,
				 'assign_dispatch_id' => $assign_dispatch_id,
				 'dispatch_ticket_id' => $dispatch_ticket_id,
				 'amount' => $expense,
				 'total_amount' => $user_total_amount,
				 'message' => 'Amount credit on dispatch complete',
				 );
				 
				 $default_admin_trans_number = 'JPGTN'.$admin_date;
				 $admin_tran_data = array(
				 'default_transaction_number' => $default_admin_trans_number,
				 'user_id' => $admin_id,
				 'dispatch_id' => $dispatch_id,
				 'assign_dispatch_id' => $assign_dispatch_id,
				 'dispatch_ticket_id' => $dispatch_ticket_id,
				 'amount' => $income,
				 'total_amount' => $admin_total_amount,
				 'message' => 'Amount credit on dispatch complete',
				 );
					$check_user_specific_trans = Transaction::where('user_id', $user_id)
									->where('dispatch_id', $dispatch_id)
									->where('assign_dispatch_id', $assign_dispatch_id)
									->where('dispatch_ticket_id', $dispatch_ticket_id)
									->where('user_type', 'employee')->orderBy('id', 'desc')->take(1)->get()->toArray();
				$chec_admin_specific_trans  = Transaction::where('user_id', $admin_id)
									->where('dispatch_id', $dispatch_id)
									->where('assign_dispatch_id', $assign_dispatch_id)
									->where('dispatch_ticket_id', $dispatch_ticket_id)
									->where('user_type', 'employee')->orderBy('id', 'desc')->take(1)->get()->toArray();
				if(isset($check_user_specific_trans) && !empty($check_user_specific_trans)) {
					 $user_trans_id = $check_user_specific_trans[0]['id'];
					 $user_trans_where = array('id' => $user_trans_id);
					$update_user_trans = array('amount' => $expense,'total_amount' => $user_total_amount);
					DB::table('transactions')
					->where($user_trans_where)
					->update($update_user_trans);
				 }else {
					 $user_trans = Transaction::Create($user_tran_data);
				 }
				 if(isset($chec_admin_specific_trans) && !empty($chec_admin_specific_trans)) {
					 $admin_trans_id = $chec_admin_specific_trans[0]['id'];
					 $admin_trans_where = array('id' => $admin_trans_id);
					$update_admin_trans = array('amount' => $income,'total_amount' => $admin_total_amount);
					DB::table('transactions')
					->where($admin_trans_where)
					->update($update_admin_trans);
				 }else {
					 $admin_trans = Transaction::Create($admin_tran_data);
				 }
				
				 
				 /*** end Transaction entry while ticket completed  ****/
					$where = array('user_id' => $user_id,'dispatch_id' => $dispatch_id);
					$update_completed = array('status' => 'completed','completed_date' => $date);
					DB::table('assign_dispatches')
					->where($where)
					->update($update_completed);
				} else {
					$where = array('user_id' => $user_id,'dispatch_id' => $dispatch_id);
					$update_completed = array('status' => 'submitted');
					DB::table('assign_dispatches')
					->where($where)
					->update($update_completed);
				}
				
				/**if all asign user  completed ticket make order as completed  ****/
				$dispatch_status_data = 'pending';
				$completed_date = '';
				$get_incompelete_dispatch = AssignDispatch::where('dispatch_id', $dispatch_id)->where('status','!=', 'completed')->where('status','!=', 'cancelled')->count();
				$dispatch_status_where = array('id' => $dispatch_id);
				if($get_incompelete_dispatch == 0 || $get_incompelete_dispatch == '0') {
					$dispatch_status_data = 'completed';
					$completed_date = $date;
					$dispatch_status_update = array('status' => $dispatch_status_data,'completed_date' => $completed_date);
					DB::table('dispatches')
					->where($dispatch_status_where)
					->update($dispatch_status_update);
					/*** update customer amount data in transaction and customer table ****/
					$customer_last_amount = 0;
					$get_customer_trans = Transaction::where('user_id', $customer_id)->where('user_type', 'customer')->orderBy('id', 'desc')->take(1)->get()->toArray();
					if(isset($get_customer_trans) && !empty($get_customer_trans)) {
					 $customer_last_amount = $get_customer_trans[0]['total_amount'];
				 }else {}
					$dispatch_total_sum = DB::table('dispatches')
									->join('dispatch_tickets', 'dispatches.id', '=', 'dispatch_tickets.dispatch_id')
									->where('dispatches.status', '=', 'completed')
									->where('dispatches.customer_id', '=', $customer_id)
									->sum('dispatch_tickets.income');
					$dispatch_sum = DB::table('dispatches')
									->join('dispatch_tickets', 'dispatches.id', '=', 'dispatch_tickets.dispatch_id')
									->where('dispatches.status', '=', 'completed')
									->where('dispatches.id', '=', $dispatch_id)
									->sum('dispatch_tickets.income');
					$customer_current_amount = $customer_last_amount + $dispatch_sum;
					$customer_where = array('id' => $customer_id);
					$customer_update = array('total_amount' => $dispatch_total_sum,'current_amount' => $customer_current_amount);
					DB::table('customers')
					->where($customer_where)
					->update($customer_update);
					$cutomer_date = $user_date.'2';
				$default_customer_trans_number = 'JPGTN'.$cutomer_date;
				 $customer_tran_data = array(
				 'default_transaction_number' => $default_customer_trans_number,
				 'user_id' => $customer_id,
				 'dispatch_id' => $dispatch_id,
				 'user_type' => 'customer',
				 'amount' => $dispatch_sum,
				 'total_amount' => $customer_current_amount,
				 'message' => 'Amount credit on dispatch complete',
				 );
				 $check_customer_specific_trans = Transaction::where('user_id', $customer_id)
									->where('dispatch_id', $dispatch_id)
									->where('user_type', 'customer')->orderBy('id', 'desc')->take(1)->get()->toArray();
				 if(isset($check_customer_specific_trans) && !empty($check_customer_specific_trans)) {
					 $customer_trans_id = $check_customer_specific_trans[0]['id'];
					 $customer_trans_where = array('id' => $customer_trans_id);
					$update_customer_trans = array('amount' => $dispatch_sum,'total_amount' => $customer_current_amount);
					DB::table('transactions')
					->where($customer_trans_where)
					->update($update_customer_trans);
				 }else {
					 $customer_trans = Transaction::Create($customer_tran_data);
				 }
					/*** end update customer amount data in transaction and customer table ****/
				} else {
					$dispatch_status_update = array('status' => $dispatch_status_data);
					DB::table('dispatches')
					->where($dispatch_status_where)
					->update($dispatch_status_update);
				}
				/**end if all asign user  completed ticket make order as completed  ****/
				/*** end it is to update relevant table  ***********/
				return \Response::json(['error'=> '','status' => 'done','success'=>true]);
	 }
    public function update(Request $request, $id)
    {
		if(Auth::user()->can('viewMenu:ActionDispatchTicket') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
			$data  = $request->all();
			$rules = [
				'dispatch_id' => 'required',
				'shift_type' => 'required',
				'user_type'  => 'required',
				'user_id'  => 'required',
				'status'  => 'required',
				'starting_km'  => 'required|numeric',
				'ending_km'  => 'required|numeric',
				//'fuel_qty'  => 'required|numeric',
			// 'fuel_card_number'  => 'required',
			// 'def_qty'  => 'required|numeric',
			// 'gas_station_location'  => 'required',
				'ticket_number'  => 'required',
				'hour_or_load'  => 'required'
			];
			$message = [
				'dispatch_id.required' => 'Please select Dispatch.',
				'shift_type.required' => 'Shift type is required.',
				'user_type.required' => 'User type is required.',
				'user_id.required' => 'User is required.',
				'starting_km.required' => 'Starting Km is required.',
				'starting_km.numeric' => 'Starting Km should be numeric.',
				'ending_km.required' => 'Ending Km is required.',
				'ending_km.numeric' => 'Ending Km should be numeric.',
				'fuel_qty.required' => 'Fuel Quantity is required.',
				'fuel_qty.numeric' => 'Fuel Quantity should be numeric.',
				'fuel_card_number.required' => 'Fuel Card number is required.',
				'def_qty.numeric' => 'Def Quantity should be numeric.',
				'gas_station_location.required' => 'Gas station location is required.',
				'ticket_number.required' => 'Ticket Number is required.',
				'hour_or_load.required' => 'Hour / Load price is required.',
			];
			$validator = Validator::make($data, $rules, $message);
			if ($validator->fails()) {
				return back()->withInput()
					->withErrors($validator);
			} else {
				$error_got = '';
				/*** If broker then vehicle should be as required ***/
				$user_type = $request->user_type;
				$broker_vehicle_id = $request->broker_vehicle_id;
				$assign_dispatch_id = $request->assign_dispatch_id;
				if($user_type == 'broker') {
					if($broker_vehicle_id == '') {
						$error_got = 'yes';
						$validator->getMessageBag()->add('broker_vehicle_id', 'Select Broker vehicle');
					} else {
						$vehicle_id_added = DispatchTicket::where('broker_vehicle_id','=',$broker_vehicle_id)
						->where('id','!=',$id)
						->get()->toArray();
						if(!empty($vehicle_id_added)) {
							$error_got = 'yes';
							$validator->getMessageBag()->add('broker_vehicle_id', 'Driver data already added');
						}
					}
				}
				if($user_type == 'employee') {
					$vehicle_id_added = DispatchTicket::where('assign_dispatch_id','=',$assign_dispatch_id)
					->where('id','!=',$id)
					->get()->toArray();
						if(!empty($vehicle_id_added)) {
							$error_got = 'yes';
							$validator->getMessageBag()->add('user_id', 'Employee data already added');
						}
				}
				if($request->starting_km > $request->ending_km) {
					$error_got = 'yes';
					$validator->getMessageBag()->add('starting_km', 'Starting Km cant be more then Ending KM');
				}
				/*** end If broker then vehicle should be as required ***/
				/*******it is to add fuel price ****/
				$fuel_amount_paid = 0;
				$dispatch_id = $request->dispatch_id;
				$ending_km = $request->ending_km;
				$starting_km = $request->starting_km;
				$fuel_res = $this->calculated_fuel_price($dispatch_id,$ending_km,$starting_km);
				$fuel_res_arr = json_decode($fuel_res,true);
				if($fuel_res_arr['price_added'] == 'no') {
					$dispath_date_show = $fuel_res_arr['dispath_date_show'];
				$invalid_num = 'Fuel price is not added for date '.$dispath_date_show;
				$error_got = 'yes';
				$validator->getMessageBag()->add('fuel_amount_paid', $invalid_num);
				} else{}
				$fuel_amount_paid = $fuel_res_arr['amount_paid'];
				
				/*******end it is to add fuel price ****/
				$data_array = array();
				$dispatch_id = $data_array['dispatch_id'] = $request->dispatch_id;
				$dispatch_query = Dispatch::where('id','=',$dispatch_id)->get()->toArray();
				$displatch_data = $dispatch_query[0];
				$disp_job_type = $displatch_data['job_type'];
				$disp_job_rate = $displatch_data['job_rate'];
				$customer_id = $displatch_data['customer_id'];
				/*** laod rate will come from dispatch for all users  ****/
				$user_load_rate = $displatch_data['employee_rate'];
				/*** end laod rate will come from dispatch for all users  ****/
				$start_time = $displatch_data['start_time'];
				$on_date = date('Y-m-d',strtotime($start_time));
				$admin_user_query = User::where('type','=','admin')->get()->toArray();
				$admin_user = $admin_user_query[0];
				$admin_id = $admin_user['id'];
				
				$user_id = $request->user_id;
				$user_query = User::where('id','=',$user_id)->with('getVehicle')->get()->toArray();
				$user = $user_query[0];
				/*******add imp fields in ticket table */
				$emp_brok_name = $user['name'];
				$emp_brok_email = $user['email'];
				$emp_brok_phone = $user['country_code'].''.$user['phone'];
				$emp_brok_hst = $user['hst'];
				/*******end add imp fields in ticket table */
				$hourly_rate = $data_array['emp_brok_hour_rate'] = $user['hourly_rate'];
				//$load_rate = $data_array['emp_brok_load_rate'] = $user['load_per'];
				$load_rate = $data_array['emp_brok_load_rate'] = $user_load_rate;
				
				$ticket_hr_ld_rate = $request->hour_or_load;
				$income = $ticket_hr_ld_rate * $disp_job_rate;
				if($fuel_amount_paid > 0 && $income < $fuel_amount_paid) {
					$invalid_num = 'Fuel price is '.$fuel_amount_paid.'.Kindly check your KM';
				$error_got = 'yes';
				$validator->getMessageBag()->add('starting_km', $invalid_num);
				} else {}
				if($error_got == 'yes') {
					return back()->withInput()
					->withErrors($validator);
				} else {}
				/*** uplaod images ***/
				$uda_for_iamge_name_concat = $request->user_id.''.$dispatch_id.''.$assign_dispatch_id;
				$data_array['fuel_amount_paid'] = $fuel_amount_paid;
				if($request->file('fuel_receipt') != null){
					$file = $request->file('fuel_receipt');
					$exte = $file->extension();
					$filename = date('YmdHis').''.$uda_for_iamge_name_concat.'.'.$exte;
					$file->move(public_path('images/fuel_receipt'), $filename);
					$old_fuel_receipt = $request->old_fuel_receipt;
					$path = public_path()."/images/fuel_receipt/".$old_fuel_receipt;
					if(file_exists($path) && $old_fuel_receipt != '' && null != $old_fuel_receipt) {
						unlink($path);
					}
					
					$data_array['fuel_receipt'] = $filename;
				}
				if($request->file('def_receipt') != null){
					$file= $request->file('def_receipt');
					$exte = $file->extension();
					$filename = date('YmdHis').''.$uda_for_iamge_name_concat.'.'.$exte;
					$file-> move(public_path('images/def_receipt'), $filename);
					$old_def_receipt = $request->old_def_receipt;
					$path = public_path()."/images/def_receipt/".$old_def_receipt;
					if(file_exists($path) && $old_def_receipt != '' && null != $old_def_receipt) {
						unlink($path);
					}
					$data_array['def_receipt'] = $filename;
				}
				if($request->file('ticket_img') != null){
					$file= $request->file('ticket_img');
					$exte = $file->extension();
					$filename = date('YmdHis').''.$uda_for_iamge_name_concat.'.'.$exte;
					$file-> move(public_path('images/ticket_img'), $filename);
					$old_ticket_img = $request->old_ticket_img;
					$path = public_path()."/images/ticket_img/".$old_ticket_img;
					if(file_exists($path) && $old_ticket_img != '' && null != $old_ticket_img) {
						unlink($path);
					}
					$data_array['ticket_img'] = $filename;
				}
				/*** end uplaod images ***/
				$expense = 0;
				/*** it is added  if we have to add hours over load */
				$expense_without_emploee_hour_over_load = 0;
				$emploee_hour_over_load = ( null != $request->emploee_hour_over_load && is_numeric($request->emploee_hour_over_load)) ? $request->emploee_hour_over_load : 0;
				$emploee_hourly_rate_over_load = $hourly_rate;
				$emploee_hour_over_load_amount = $emploee_hour_over_load * $emploee_hourly_rate_over_load * $ticket_hr_ld_rate;
				$emploee_hour_over_load_amount = number_format((float)$emploee_hour_over_load_amount, 2, '.', '');
				$data_array['emploee_hour_over_load'] = $emploee_hour_over_load;
				$data_array['emploee_hourly_rate_over_load'] = $emploee_hourly_rate_over_load;
				$data_array['emploee_hour_over_load_amount'] = $emploee_hour_over_load_amount;
				/*** end it is added  if we have to add hours over load */
				if($disp_job_type == 'hourly') {
					$expense = $ticket_hr_ld_rate * $hourly_rate;
				} else {
					/*** it is added  if we have to add hours over load */
					if($emploee_hour_over_load_amount > 0) {
						$expense = $emploee_hour_over_load_amount;
						$expense_without_emploee_hour_over_load = $ticket_hr_ld_rate * $load_rate;
					} else {
						$expense = $ticket_hr_ld_rate * $load_rate;
						$expense_without_emploee_hour_over_load = 0;
					}
					/*** it is added  if we have to add hours over load */
					// $expense = $ticket_hr_ld_rate * $load_rate;
					
				}
				/*** it is added  if we have to add hours over load */
				$data_array['expense_without_emploee_hour_over_load'] = $expense_without_emploee_hour_over_load;
				/*** it is added  if we have to add hours over load */
				$profit = 0;
				if($expense < $income) {
					$profit = $income - $expense - $fuel_amount_paid;
				} else if($income < $expense) {
					$profit = $expense - $income - $fuel_amount_paid;
				} else {}
				$ticket_status = $request->status;
				$total_km = $ending_km - $starting_km;
				$data_array['created_by'] = Auth::user()->id;
				$data_array['assign_dispatch_id'] = $assign_dispatch_id;
				$data_array['broker_vehicle_id'] = ($broker_vehicle_id == '') ? 0 : $broker_vehicle_id;
				$data_array['shift_type'] = $request->shift_type;
				$data_array['user_type'] = $request->user_type;
				$data_array['user_id'] = $user_id;
				$data_array['driver_name'] = $request->driver_name;
				$data_array['unit_vehicle_number'] = $request->unit_vehicle_number;
				$data_array['contact_number'] = $request->contact_number;
				$data_array['starting_km'] = $starting_km;
				$data_array['ending_km'] = $ending_km;
				$data_array['total_km'] = $total_km;
				$data_array['fuel_qty'] = ($request->fuel_qty == '' || $request->fuel_qty == null) ? 0 : $request->fuel_qty;
				$data_array['fuel_card_number'] = $request->fuel_card_number;
				$data_array['def_qty'] = ($request->def_qty == '' || $request->def_qty == null) ? 0 : $request->def_qty;
				$data_array['gas_station_location'] = $request->gas_station_location;
				$data_array['ticket_number'] = $request->ticket_number;
				$data_array['status'] = $ticket_status;
				$data_array['income'] = $income;
				$data_array['expense'] = $expense;
				$data_array['profit'] = $profit;
				$data_array['hour_or_load'] = $ticket_hr_ld_rate;
				$data_array['hour_or_load_integer'] = (is_numeric($ticket_hr_ld_rate)) ? $ticket_hr_ld_rate : 0;
				$data_array['emp_brok_name'] = $emp_brok_name;
				$data_array['emp_brok_email'] = $emp_brok_email;
				$data_array['emp_brok_phone'] = $emp_brok_phone;
				$data_array['emp_brok_hst'] = $emp_brok_hst;
					DispatchTicket::updateOrCreate(array('id' => $id), $data_array);
					/*** it is to update relevant table  ***********/
					$dispatch_ticket_id = $id;
					DB::table('transactions')->where('user_id', $user_id)
										->where('dispatch_id', $dispatch_id)
										->where('assign_dispatch_id', $assign_dispatch_id)
										->where('dispatch_ticket_id', $dispatch_ticket_id)
										->where('user_type', 'employee')->delete();
						DB::table('transactions')->where('user_id', $admin_id)
										->where('dispatch_id', $dispatch_id)
										->where('assign_dispatch_id', $assign_dispatch_id)
										->where('dispatch_ticket_id', $dispatch_ticket_id)
										->where('user_type', 'employee')->delete();
						DB::table('transactions')->where('user_id', $customer_id)
										->where('dispatch_id', $dispatch_id)
										->where('user_type', 'customer')->delete();
					$get_user_income = DispatchTicket::where('user_id', $user_id)->where('user_type', 'employee')->where('status', 'completed')->sum('expense');
					$get_admin_income = DispatchTicket::where('status', 'completed')->where('user_type', 'employee')->sum('income');
					$user_last_amount = $admin_last_amount = 0;
					/*** Transaction entry while ticket completed  ****/
					$get_user_trans = Transaction::where('user_id', $user_id)->where('user_type', 'employee')->orderBy('id', 'desc')->take(1)->get()->toArray();
					$get_admin_trans  = Transaction::where('user_id', $admin_id)->where('user_type', 'employee')->orderBy('id', 'desc')->take(1)->get()->toArray();
					if(isset($get_user_trans) && !empty($get_user_trans)) {
						$user_last_amount = $get_user_trans[0]['total_amount'];
					}else {}
					if(isset($get_admin_trans) && !empty($get_admin_trans)) {
						$admin_last_amount = $get_admin_trans[0]['total_amount'];
					}else {}
					$user_total_amount = $expense + $user_last_amount;
					$admin_total_amount = $income + $admin_last_amount;
					
					$date = Carbon::now();
					/***it is for user update  ***/
					$user_where = array('id' => $user_id);
					$user_update = array('total_income' => $get_user_income,'current_amount' => $user_total_amount);
					DB::table('users')
					->where($user_where)
					->update($user_update);
					
					$admin_user_where = array('id' => $admin_id);
					$admin_user_update = array('total_income' => $get_admin_income,'current_amount' => $admin_total_amount);
					DB::table('users')
					->where($admin_user_where)
					->update($admin_user_update);
					/***end it is for user update  ***/
					DB::table('meter_histories')->where('dispatch_ticket_id', $dispatch_ticket_id)->delete();
					DB::table('fuel_histories')->where('dispatch_ticket_id', $dispatch_ticket_id)->delete();
					if($ticket_status == 'completed') {
						/*** it is to update vehicle km  *********/
						$user_type = $user['type'];
						$vehicle_id = $user['vehicle_id'];
						if($user_type == 'employee') {
							$vehicles = Vehicle::find($vehicle_id);
							$total_km = (null != $vehicles->total_km && $vehicles->total_km >= 0) ? $vehicles->total_km : 0;
							$vehicle_number = $vehicles->vehicle_number;
							$add_ending_km = $ending_km;
							if($ending_km < $total_km) {
								$add_ending_km = $total_km;
							} else {}
							$vehicles->total_km = $add_ending_km;
							$vehicles->save();
							/********* it to add fuel history */
							$this->update_fuel_history($dispatch_ticket_id);
							/********* end it to add fuel history */
							/** add meter history */
						$meter_history = new MeterHistory;
						$meter_history->created_by = Auth::user()->id;
						$meter_history->vehicle_id = $vehicle_id;
						$meter_history->dispatch_ticket_id = $dispatch_ticket_id;
						$meter_history->on_date = $on_date;
						$meter_history->vehicle_number = $vehicle_number;
						$meter_history->starting_km = $starting_km;
						$meter_history->ending_km = $ending_km;
						$meter_history->total_km = $ending_km;
						$meter_history->comment = 'dispatch ticket completed';
						$meter_history->source = 'Dispatch Ticket';
						$meter_history->save();
						/** end add meter history */
						} else{}
						/*** end it is to update vehicle km  *********/
						$user_trans_id = $admin_trans_id = '';
						/*** Transaction entry while ticket completed  ****/
						$user_date = date('Ymdhis');
					$admin_date = $user_date.'1';
					$default_transaction_number = 'JPGTN'.$user_date;
					$user_tran_data = array(
					'default_transaction_number' => $default_transaction_number,
					'user_id' => $user_id,
					'dispatch_id' => $dispatch_id,
					'assign_dispatch_id' => $assign_dispatch_id,
					'dispatch_ticket_id' => $dispatch_ticket_id,
					'amount' => $expense,
					'total_amount' => $user_total_amount,
					'message' => 'Amount credit on dispatch complete',
					);
					
					$default_admin_trans_number = 'JPGTN'.$admin_date;
					$admin_tran_data = array(
					'default_transaction_number' => $default_admin_trans_number,
					'user_id' => $admin_id,
					'dispatch_id' => $dispatch_id,
					'assign_dispatch_id' => $assign_dispatch_id,
					'dispatch_ticket_id' => $dispatch_ticket_id,
					'amount' => $income,
					'total_amount' => $admin_total_amount,
					'message' => 'Amount credit on dispatch complete',
					);
						$check_user_specific_trans = Transaction::where('user_id', $user_id)
										->where('dispatch_id', $dispatch_id)
										->where('assign_dispatch_id', $assign_dispatch_id)
										->where('dispatch_ticket_id', $dispatch_ticket_id)
										->where('user_type', 'employee')->orderBy('id', 'desc')->take(1)->get()->toArray();
					$chec_admin_specific_trans  = Transaction::where('user_id', $admin_id)
										->where('dispatch_id', $dispatch_id)
										->where('assign_dispatch_id', $assign_dispatch_id)
										->where('dispatch_ticket_id', $dispatch_ticket_id)
										->where('user_type', 'employee')->orderBy('id', 'desc')->take(1)->get()->toArray();
										
					if(isset($check_user_specific_trans) && !empty($check_user_specific_trans)) {
						$user_trans_id = $check_user_specific_trans[0]['id'];
						$user_trans_where = array('id' => $user_trans_id);
						$update_user_trans = array('amount' => $expense,'total_amount' => $user_total_amount);
						DB::table('transactions')
						->where($user_trans_where)
						->update($update_user_trans);
					}else {
						$user_trans = Transaction::Create($user_tran_data);
					}
					if(isset($chec_admin_specific_trans) && !empty($chec_admin_specific_trans)) {
						$admin_trans_id = $chec_admin_specific_trans[0]['id'];
						$admin_trans_where = array('id' => $admin_trans_id);
						$update_admin_trans = array('amount' => $income,'total_amount' => $admin_total_amount);
						DB::table('transactions')
						->where($admin_trans_where)
						->update($update_admin_trans);
					}else {
						$admin_trans = Transaction::Create($admin_tran_data);
					}
					
					/*** end Transaction entry while ticket completed  ****/
						$where = array('user_id' => $user_id,'dispatch_id' => $dispatch_id);
						$update_completed = array('status' => 'completed','completed_date' => $date);
						DB::table('assign_dispatches')
						->where($where)
						->update($update_completed);
					} else {
						$where = array('user_id' => $user_id,'dispatch_id' => $dispatch_id);
						$update_completed = array('status' => 'submitted');
						DB::table('assign_dispatches')
						->where($where)
						->update($update_completed);
					}
					
					/**if all asign user  completed ticket make order as completed  ****/
					$dispatch_status_data = 'pending';
					$completed_date = '';
					$get_incompelete_dispatch = AssignDispatch::where('dispatch_id', $dispatch_id)->where('status','!=', 'completed')->where('status','!=', 'cancelled')->count();
					$dispatch_status_where = array('id' => $dispatch_id);
					if($get_incompelete_dispatch == 0 || $get_incompelete_dispatch == '0') {
						$dispatch_status_data = 'completed';
						$completed_date = $date;
						$dispatch_status_update = array('status' => $dispatch_status_data,'completed_date' => $completed_date);
						DB::table('dispatches')
						->where($dispatch_status_where)
						->update($dispatch_status_update);
						/*** update customer amount data in transaction and customer table ****/
						$customer_last_amount = 0;
						$get_customer_trans = Transaction::where('user_id', $customer_id)->where('user_type', 'customer')->orderBy('id', 'desc')->take(1)->get()->toArray();
						if(isset($get_customer_trans) && !empty($get_customer_trans)) {
						$customer_last_amount = $get_customer_trans[0]['total_amount'];
					}else {}
						$dispatch_total_sum = DB::table('dispatches')
										->join('dispatch_tickets', 'dispatches.id', '=', 'dispatch_tickets.dispatch_id')
										->where('dispatches.status', '=', 'completed')
										->where('dispatches.customer_id', '=', $customer_id)
										->sum('dispatch_tickets.income');
						$dispatch_sum = DB::table('dispatches')
										->join('dispatch_tickets', 'dispatches.id', '=', 'dispatch_tickets.dispatch_id')
										->where('dispatches.status', '=', 'completed')
										->where('dispatches.id', '=', $dispatch_id)
										->sum('dispatch_tickets.income');
						$customer_current_amount = $customer_last_amount + $dispatch_sum;
						$customer_where = array('id' => $customer_id);
						$customer_update = array('total_amount' => $dispatch_total_sum,'current_amount' => $customer_current_amount);
						DB::table('customers')
						->where($customer_where)
						->update($customer_update);
						$cutomer_date = $user_date.'2';
					$default_customer_trans_number = 'JPGTN'.$cutomer_date;
					$customer_tran_data = array(
					'default_transaction_number' => $default_customer_trans_number,
					'user_id' => $customer_id,
					'dispatch_id' => $dispatch_id,
					'user_type' => 'customer',
					'amount' => $dispatch_sum,
					'total_amount' => $customer_current_amount,
					'message' => 'Amount credit on dispatch complete',
					);
					$check_customer_specific_trans = Transaction::where('user_id', $customer_id)
										->where('dispatch_id', $dispatch_id)
										->where('user_type', 'customer')->orderBy('id', 'desc')->take(1)->get()->toArray();
					if(isset($check_customer_specific_trans) && !empty($check_customer_specific_trans)) {
						$customer_trans_id = $check_customer_specific_trans[0]['id'];
						$customer_trans_where = array('id' => $customer_trans_id);
						$update_customer_trans = array('amount' => $dispatch_sum,'total_amount' => $customer_current_amount);
						DB::table('transactions')
						->where($customer_trans_where)
						->update($update_customer_trans);
					}else {
						$customer_trans = Transaction::Create($customer_tran_data);
					}
					
						/*** end update customer amount data in transaction and customer table ****/
					} else {
						$dispatch_status_update = array('status' => $dispatch_status_data);
						DB::table('dispatches')
						->where($dispatch_status_where)
						->update($dispatch_status_update);
					}
					/**end if all asign user  completed ticket make order as completed  ****/
					/*** end it is to update relevant table  ***********/
				Session::flash('message', 'Successfully updated ticket!');
				return redirect("dispatch_tickets/")->with('message', 'Successfully updated dispatch ticket!');
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
		if(Auth::user()->can('viewMenu:ActionDispatchTicket') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
			$dispatch = DispatchTicket::find($id);
			$dispatch->delete();
			// redirect
			Session::flash('message', 'Successfully deleted dispatch!');
			return redirect()->route("dispatch_tickets.index");
		}
		else
		{
			return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
		} 
    }
}