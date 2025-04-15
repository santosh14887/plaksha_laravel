<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Dispatch;
use App\Models\AssignDispatch;
use App\Models\Notification;
use App\Models\Customer;
use App\Models\Vehicle;
use Auth;
use Validator;
use Session;
use DB;
use Twilio\Rest\Client;
use Config;
class AssignDispatchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		die('here');
        $perPage = 15;
       /* $keyword = $request->get('search');
			if (!empty($keyword)) {
                $query =  AssignDispatch::query()->with('getCustomer');
				$assign_dispatches = $query->where('no_of_vehicles', 'LIKE', "%$keyword%")
                ->orWhereHas('getCustomer',function ($query)use($keyword)
              {
                  $query->where('customers.company_name','Like','%'.$keyword.'%');
              })->orWhereHas('getAssignDispatch',function ($query)use($keyword)
              {
                  $query->where('assign_dispatches.company_name','Like','%'.$keyword.'%');
              })
					->latest()->paginate($perPage);
			} else {
				$assign_dispatches = AssignDispatch::latest()->paginate($perPage);
			}*/
			$assign_dispatches = AssignDispatch::latest()->paginate($perPage);
            return view('assign_dispatches.index',compact('assign_dispatches'));
    }
	public function specific_assigned_dispatch($id){
		if(Auth::user()->can('viewMenu:Dispatch') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
			$dispatches = Dispatch::find($id);
			$assign_dispatches = AssignDispatch::where('dispatch_id','=',$id)->get();
			return view('assign_dispatches.index',compact('assign_dispatches','id','dispatches'));
		}
		else
		{
			return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
		}
	}
	public function add_more_dispach_assignment(){
		extract($_REQUEST);
		$customer = Customer::get()->pluck('company_name','id');
		$vehicle = Vehicle::get()->pluck('vehicle_number','id');
		$dispatches = Dispatch::find($dispatch_id);
		$html=view('assign_dispatches.ajax_assignment')->with(['customer'=>$customer,'vehicle'=>$vehicle,'dispatches'=>$dispatches,'count'=>$count])->render();
		return \Response::json(['html' => $html,'success'=>true]);
	}
	public function get_users(){
		extract($_REQUEST);
		$user_arr = explode(',',$user_arr);
		$user_arr = array_filter($user_arr);
		//$users = User::where('type','=',$type)->whereNotIn('id', $user_arr)->get()->pluck('name','id');
		$users = User::where('type','=',$type)->get()->pluck('name','id');
		$html = '<option value="">Select User Name</option>';
		foreach($users as $users_key => $users_val) {
		$html .= '<option value="'.$users_key.'">'.$users_val.'</option>';
		}
		return $html;
	}
	public function get_user_vehicle(){
		extract($_REQUEST);
		$html = '';
		$vehicle = $users = $users_vehicle = array();
		$multiple = '';
		$users_data = User::where('id','=',$user_id)->with('getVehicle')->get()->toArray();
		$user_status = $users_data[0]['status'];
		if(isset($users_data) && !empty($users_data)) {
			$users_vehicle = $users_data[0]['get_vehicle'];
		}
		$users = User::where('vehicle_id','>', '0')->get()->pluck('vehicle_id','vehicle_id')->toArray();
		$vehicle = Vehicle::get()->pluck('vehicle_number','id');
		if( isset($user_id) && !isset($users_vehicle['vehicle_number'])) {
			$multiple = 'multiple';
		}
		if(! isset($count)) {
			$count = 1;
		}
		$html=view('assign_dispatches.ajax_vehicles')->with(['users_vehicle'=>$users_vehicle,'user_status'=>$user_status,'users'=>$users,'vehicle'=>$vehicle,'user_type'=>$user_type,'multiple'=>$multiple,'user_id'=>$user_id,'count'=>$count])->render();
		return \Response::json(['html' => $html,'success'=>true,'count'=>$count]);
	}
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
		if(Auth::user()->can('viewMenu:ActionDispatch') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
			$customer = Customer::get()->pluck('company_name','id');
			$vehicle = Vehicle::get()->pluck('vehicle_number','id');
			/*** expect customer vehicle get all vehicle  ***/
			$users = User::where('vehicle_id','>', '0')->get()->pluck('vehicle_id','vehicle_id')->toArray();
			$vehicle = Vehicle::get()->pluck('vehicle_number','id');
			/***end expect customer vehicle get all vehicle  ***/
			$dispatches = Dispatch::find($id);
			$employee_data = User::where('type','=','employee')->with('getVehicle')->get();
			$broker_data = User::where('type','=','broker')->with('getVehicle')->get();
			return view('assign_dispatches.create',compact('customer','dispatches','vehicle','employee_data','broker_data'));
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
    public function cancel_dispatch_from_users(Request $request)
    {
		$data  = $request->all();
		$dispatch_id = $data['dispatch_id'];
		$cancel_message = $data['cancel_message'];
		$assign_dispatch_ids = $data['assign_dispatch_ids'];
		$dispatch_data_all =  AssignDispatch::whereIn('id', $assign_dispatch_ids)->with('getUser')->with('getDispatch')->with('getDispatch.getCustomer')->get();
		
		foreach($dispatch_data_all as $vals) 
		{
			$notification_text = $user_msg = $message = '';
			$assign_dispatch_id = $vals->id;
			$dispatch_detail =  $vals->getDispatch;
			$order_time = $dispatch_date = $dispatch_detail->start_time;
			$start_location = $dispatch_detail->start_location;
			$dump_location = $dispatch_detail->dump_location;
			$customer_name = $dispatch_detail->getCustomer->company_name;
			
			$notification_text = $start_location." to ".$dump_location." (".$order_time.") ";
			$notification_text .= "\n";
			$notification_text .= $cancel_message;
			$notification_text .= "\n";
			$user_data = $vals->getUser;
				$name = $user_data['name'];
				 $mobile_no = $user_data['phone'];
				 $country_code = $user_data['country_code'];
				 $expo_token = $user_data['expo_token'];
				 /***send sms to employee *****************/
				if($mobile_no != '' && $mobile_no != null) {
					$app_constant_name = Config::get('constants.APP_CONSTANT_NAME');
					$user_msg = 'Your Dispatch has been cancelled by admin from '.$notification_text;
					$user_msg .= "Kindly visit ".$app_constant_name." for more details.";
					$receiverNumber = $country_code.$mobile_no;
					$message = "Hello ".$name."\n";
					$message .= $user_msg;
					$sms_res = send_sms($receiverNumber,$message);
					/***end send sms to employee *****************/
				} else {}
				$assign_dispatch =  AssignDispatch::find($assign_dispatch_id);
					$assign_dispatch->status = 'cancelled';
					$assign_dispatch->cancel_push_notification_message = $cancel_message;
				/*** it is to send push notification  ***/
				$result_data = array();
				if(isset($expo_token) && $expo_token != '') {
					$body_text = 'Your Dispatch has been cancelled by admin from '.$notification_text;
					$result = send_notification($body_text,$expo_token,'cancel_order');
					$result_data = json_decode($result, TRUE);
					if(isset($result_data['data']) && !empty($result_data['data'])) {
					if(isset($result_data['data']['status']) && $result_data['data']['status'] == 'error') {
					$assign_dispatch->cancel_push_notification_sent = 'no';
					$assign_dispatch->cancel_push_notification_message_id = '';
					$assign_dispatch->cancel_push_notification_message = $result_data['data']['message'];
					} else{
					$msg_id = $result_data['data']['id'];
					$notification = new Notification();
					$notification->user_id = $user_id_arr[$key];
					$notification->push_notification_message_id = $msg_id;
					$notification->message = $body_text;
					$notification->save();
					$notification_id = $notification->id;
					$assign_dispatch->cancel_push_notification_sent = 'yes';
					$assign_dispatch->cancel_push_notification_message_id = $msg_id;
					$assign_dispatch->cancel_notification_id = $notification_id;
					}
					
				} else {}
			} else {}
				/*** end it is to send push notification  ***/	
				$assign_dispatch->save();
		}
		$dispatches = AssignDispatch::where('dispatch_id','=',$dispatch_id)->where('status','!=', 'cancelled')->where('status','!=', 'decline')->get()->toArray();
		
		if(empty($dispatches)) {
			$dispatch = Dispatch::find($dispatch_id);
			$dispatch->status = 'cancelled';
			$dispatch->save();
		} else {}
		echo 'done';
	}
	public function send_new_sms_notification($user_data = array(),$notification_text = '') {
				$name = $user_data['name'];
				$mobile_no = $user_data['phone'];
				$country_code = $user_data['country_code'];
				$expo_token = $user_data['expo_token'];
				/***send sms to employee *****************/
			if($mobile_no != '' && $mobile_no != null) {
				$app_constant_name = Config::get('constants.APP_CONSTANT_NAME');
				$user_msg = $notification_text;
				$user_msg .= " \n Kindly visit ".$app_constant_name." App for details.";
			//	$country_code = Config::get('constants.TWILIO_Country_Code');
				$receiverNumber = $country_code.$mobile_no;
				$message = "Hello ".$name."\n";
				$message .= $user_msg;
				$sms_res = send_sms($receiverNumber,$message);
			}
			/***end send sms to employee *****************/	
			/*** it is to send push notification  ***/
			$result = array();
			$result = json_encode($result);
			if(isset($expo_token) && $expo_token != '') {
			$body_text = $notification_text;
			$result = send_notification($body_text,$expo_token,'new_order');
		/*** end it is to send push notification  ***/
		}
		return $result;
	}
	public function generate_push_notification_arr($result_data = array(),$message_txt = '',$user_id = '0') {
		$data_array = array();
		if(isset($result_data['data']) && !empty($result_data['data']))
		{
			if(isset($result_data['data']['status']) && $result_data['data']['status'] == 'error') {
			$data_array['push_notification_sent'] = 'yes';
			$data_array['push_notification_message_id'] = '';
			$data_array['push_notification_message'] = 'Push notification has been sent';
			} else{
					$msg_id = $result_data['data']['id'];
					$data_array['push_notification_sent'] = 'yes';
					$data_array['push_notification_message_id'] = $msg_id;
					$data_array['push_notification_message'] = 'Push notification has been sent';
					$notification = new Notification();
					$notification->user_id = $user_id;
					$notification->push_notification_message_id = $msg_id;
					$notification->message = $message_txt;
					$notification->save();
					$notification_id = $notification->id;
					$data_array['notification_id'] = $notification_id;
			}	
		} else {
			$data_array['push_notification_sent'] = 'no';
			$data_array['push_notification_message'] = 'Expo id does not exist';
		}
		return $data_array = json_encode($data_array);
	}
	public function generate_cancel_push_notification_arr($result_data = array(),$message_txt = '',$user_id = '0') {
		$data_array = array();
		if(isset($result_data['data']) && !empty($result_data['data']))
		{
			if(isset($result_data['data']['status']) && $result_data['data']['status'] == 'error') {
			$data_array['cancel_push_notification_sent'] = 'yes';
			$data_array['cancel_push_notification_message_id'] = '';
			$data_array['cancel_push_notification_message'] = 'Push notification has been sent';
			} else{
					$msg_id = $result_data['data']['id'];
					$data_array['cancel_push_notification_sent'] = 'yes';
					$data_array['cancel_push_notification_message_id'] = $msg_id;
					$data_array['cancel_push_notification_message'] = 'Push notification has been sent';
					$notification = new Notification();
					$notification->user_id = $user_id;
					$notification->push_notification_message_id = $msg_id;
					$notification->message = $message_txt;
					$notification->save();
					$notification_id = $notification->id;
					$data_array['cancel_notification_id'] = $notification_id;
			}	
		} else {
			$data_array['cancel_push_notification_sent'] = 'no';
			$data_array['cancel_push_notification_message'] = 'Expo id does not exist';
		}
		return $data_array = json_encode($data_array);
	}
	public function store(Request $request)
    {
		if(Auth::user()->can('viewMenu:ActionDispatch') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
			$dispatch_id = $request->dispatch_id;
			$dispatches = Dispatch::find($dispatch_id);
			$data  = $request->all();
			// validate
			$rules = [
				'user_id.*' => 'required',
				'user_type.*' => 'required',
				'vehicle_id.*'  => 'required',
				'no_of_vehicles.*'  => 'required',
				'vehicle_number.*'  => 'required',
				'status.*'  => 'required'
			];
			$message = [
				'user_id.*.required' => 'User name is required.',
				'user_type.*.required' => 'User type is required.',
				'vehicle_id.*.required' => 'Unit number is required.',
				'no_of_vehicles.*.required' => 'Unit assigned is required.',
				'vehicle_number.*.required' => 'Unit number is required.',
				'status.*.required' => 'status is required.',
			];
			$validator = Validator::make($data, $rules, $message);

			if ($validator->fails()) {
				return back()->withInput()
					->withErrors($validator);
			} else {
				$error_got = '';
				/*** broker maximum vehicle validation  ********/
				$user_type_arr = $data['user_type'];
				$searchval = 'broker';
				$user_type_arr_keys = array_keys($user_type_arr, $searchval);
				if(isset($user_type_arr_keys) && !empty($user_type_arr_keys)) {
					foreach($user_type_arr_keys as $user_type_val) {
						$user_id = $data['user_id'][$user_type_val];
						$get_user = User::where('id','=',$user_id)->get()->toArray();
						$available_vehicle_unit = $get_user[0]['available_unit'];
						$assigned_vehicle = $data['no_of_vehicles'][$user_type_val];
						if($available_vehicle_unit < $assigned_vehicle) {
							$error_got = 'yes';
							$validator->getMessageBag()->add('no_of_vehicles.'.$user_type_val.'', 'Broker maximum vehicle is '.$available_vehicle_unit);
						}
					}
				}
				/*** end broker maximum vehicle validation  ********/
				/*** Sum of added no of vehicle should be as required ***/
				$required_unit = (int)$dispatches->required_unit;
				$user_id_arr = $request->user_id;
				$total_vehicles_assigned = (int)array_sum($request->no_of_vehicles);
				if($required_unit != $total_vehicles_assigned) {
					$error_got = 'yes';
					$validator->getMessageBag()->add('no_of_vehicles.0', 'Sum of all units should be '.$required_unit);
				}
				/***end Sum of added no of vehicle should be as required ***/
				/*** making username as unique ***/
				$unique_user = array_unique($user_id_arr);
				$duplicate_user = array_diff_assoc($user_id_arr, $unique_user);
				if(!empty($duplicate_user)) {
					$error_got = 'yes';
					foreach($duplicate_user as $key => $value) {
						$validator->getMessageBag()->add('user_id.'.$key, 'User name already selected');
					}	
				}
				if($error_got == 'yes') {
					return back()->withInput()
					->withErrors($validator);
				}
				/***end making username as unique ***/
				// store
				$data_insert = array();
				$user_type_arr = $request->user_type;
				$vehicle_id_arr = $request->vehicle_id;
				$no_of_vehicles_arr = $request->no_of_vehicles;
				$status_arr = $request->status;
				/*** get order or dispatch detail ***/
				$dispatch_detail = Dispatch::where('id','=',$dispatch_id)->with('getCustomer')->get();
				$dispatch_detail = $dispatch_detail[0];
				$order_time = $dispatch_date = $dispatch_detail->start_time;
				$start_location = $dispatch_detail->start_location;
				$dump_location = $dispatch_detail->dump_location;
				$customer_name = $dispatch_detail->getCustomer->company_name;
				
				$notification_text = 'New dispatch assigned for '.$start_location." to ".$dump_location." (".$order_time.") ";
				/***end get order or dispatch detail ***/
				foreach($user_type_arr as $key => $value) {
					$assign_user_id = $user_id_arr[$key];
					$status = $status_arr[$key];
					$result_data = array();
					$send_notification = false;
					if( $status == 'pending' ) {
						$send_notification = true;
						$notification_text = 'New dispatch assigned for '.$start_location." to ".$dump_location." (".$order_time.") ";
					} else {
						$send_notification = true;
						$notification_text = 'Your Dispatch has been '.$status.' by admin from '.$start_location." to ".$dump_location." (".$order_time.") ";
					}
					$users_data_qry = User::where('id','=', $assign_user_id)->with('getVehicle')->get()->toArray();
					$user_data = $users_data_qry[0];
					/** send sms and push notification and get notifcation status ***/
					if($send_notification) {
						$result = $this->send_new_sms_notification($user_data,$notification_text);
						$result_data = json_decode($result, TRUE);
					} else {}
					
					/** end send sms and push notification and get notifcation status ***/
					
				
				$assign_dispatch = new AssignDispatch;
				$assign_dispatch->created_by = Auth::user()->id;
				$assign_dispatch->user_name = $user_data['name'];
				$assign_dispatch->contact_number = $user_data['country_code'].' '.$user_data['phone'];
				/*******  get vehicle number if employee selected */
				if(isset($user_data['get_vehicle']) && !empty($user_data['get_vehicle'])) {
					$assign_dispatch->vehicle_number = $user_data['get_vehicle']['vehicle_number'];
						
					} else {}
					/******* end get vehicle number if employee selected */
				$assign_dispatch->dispatch_id = $dispatch_id;
				$new_arr = array();
				if(isset($result_data['data']) && !empty($result_data['data'])) {
						if(isset($result_data['data']['status']) && $result_data['data']['status'] == 'error') {
						$assign_dispatch->push_notification_sent = 'no';
						$assign_dispatch->push_notification_message_id = '';
						$assign_dispatch->push_notification_message = $result_data['data']['message'];
						} else{
						$msg_id = $result_data['data']['id'];
						$notification = new Notification();
						$notification->user_id = $user_id_arr[$key];
						$notification->push_notification_message_id = $msg_id;
						$notification->message = $notification_text;
						$notification->save();
						$notification_id = $notification->id;
						$assign_dispatch->push_notification_sent = 'yes';
						$assign_dispatch->push_notification_message_id = $msg_id;
						$assign_dispatch->notification_id = $notification_id;
						$assign_dispatch->push_notification_message = 'Push notification has been sent';
						}
					
				}
				$assign_dispatch->user_type = $value;
				$assign_dispatch->user_id = $user_id_arr[$key];
				$assign_dispatch->vehicle_id = $vehicle_id_arr[$key];
				$assign_dispatch->no_of_vehicles = $no_of_vehicles_arr[$key];
				$assign_dispatch->no_of_vehicles_provide = $no_of_vehicles_arr[$key];
				$assign_dispatch->status = $status_arr[$key];
				$assign_dispatch->save();
				}
				
				
				

				// redirect
				Session::flash('message', 'Successfully assigned dispatch!');
				return redirect("assigned_dispatche/".$dispatch_id)->with('message', 'Successfully assigned dispatch!');
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
		if(Auth::user()->can('viewMenu:ActionDispatch') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
			$customer = Customer::get()->pluck('company_name','id');
			$vehicle = Vehicle::get()->pluck('vehicle_number','id');
			/*** expect customer vehicle get all vehicle  ***/
			$users = User::where('vehicle_id','>', '0')->get()->pluck('vehicle_id','vehicle_id')->toArray();
			$vehicle = Vehicle::get()->pluck('vehicle_number','id');
			/***end expect customer vehicle get all vehicle  ***/
			$dispatches = Dispatch::find($id);
			$employee_data = User::where('type','=','employee')->with('getVehicle')->get();
			$broker_data = User::where('type','=','broker')->with('getVehicle')->get();
			$assign_dispatches = AssignDispatch::where('dispatch_id','=',$id)->with('getUser')->get();
			$user_type = $user_id =  $user_status = $vehicle_id = $status = $no_of_vehicles = array();
			foreach($assign_dispatches as $value) {
				$user_type[] = $value->user_type;
				$user_id[] = $value->user_id;
				$user_status[] = $value->getUser->status;
				$vehicle_id[] = $value->vehicle_id;
				$status[] = $value->status;
				$no_of_vehicles[] = $value->no_of_vehicles;
			}
			$assign_dispatches->user_type = json_encode($user_type);
			$assign_dispatches->user_id = json_encode($user_id);
			$assign_dispatches->user_status = json_encode($user_status);
			$assign_dispatches->vehicle_id = json_encode($vehicle_id);
			$assign_dispatches->status = json_encode($status);
			$assign_dispatches->no_of_vehicles = json_encode($no_of_vehicles);
			
			return view('assign_dispatches.edit',compact('assign_dispatches','customer','dispatches','vehicle','employee_data','broker_data'));
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
			/** get all user *****/
			$all_users = User::get()->pluck('name','id');
			/** end get all user *****/
			$dispatch_id = $request->dispatch_id;
			$cancel_message = ( null !== $request->cancel_message) ? $request->cancel_message : '';
			$dispatches = Dispatch::find($dispatch_id);
			$data  = $request->all();
			// validate
			$rules = [
				'user_id.*' => 'required',
				'user_type.*' => 'required',
				'vehicle_id.*'  => 'required',
				'no_of_vehicles.*'  => 'required',
				'vehicle_number.*'  => 'required',
				'status.*'  => 'required'
			];
			$message = [
				'user_id.*.required' => 'User name is required.',
				'user_type.*.required' => 'User type is required.',
				'vehicle_id.*.required' => 'Unit number is required.',
				'no_of_vehicles.*.required' => 'Unit assigned is required.',
				'vehicle_number.*.required' => 'Unit number is required.',
				'status.*.required' => 'status is required.',
			];
			$validator = Validator::make($data, $rules, $message);
			if ($validator->fails()) {
				return back()->withInput()
					->withErrors($validator);
			} else {
				$error_got = '';
				/*** broker maximum vehicle validation  ********/
				$user_type_arr = $data['user_type'];
				$searchval = 'broker';
				$user_type_arr_keys = array_keys($user_type_arr, $searchval);
				if(isset($user_type_arr_keys) && !empty($user_type_arr_keys)) {
					foreach($user_type_arr_keys as $user_type_val) {
						$user_id = $data['user_id'][$user_type_val];
						$get_user = User::where('id','=',$user_id)->get()->toArray();
						$available_vehicle_unit = $get_user[0]['available_unit'];
						$assigned_vehicle = $data['no_of_vehicles'][$user_type_val];
						if($available_vehicle_unit < $assigned_vehicle) {
							$error_got = 'yes';
							$validator->getMessageBag()->add('no_of_vehicles.'.$user_type_val.'', 'Broker maximum vehicle is '.$available_vehicle_unit);
						}
					}
				}
				/*** end broker maximum vehicle validation  ********/
				/*** Sum of added no of vehicle should be as required ***/
				$test_vehicles_arr = $request->no_of_vehicles;
				/***  remove cancelled value from array */
				foreach($data['status'] as $arr_key => $arr_val) {
					if($arr_val == 'cancelled') {
						unset($test_vehicles_arr[$arr_key]);
					}
				}
				/***  end remove cancelled value from array */
				
				$required_unit = (int)$dispatches->required_unit;
				$user_id_arr = $request->user_id;
				$total_vehicles_assigned = (int)array_sum($test_vehicles_arr);
				//if($required_unit != $total_vehicles_assigned) {
				if($total_vehicles_assigned > $required_unit) {
					$error_got = 'yes';
					$validator->getMessageBag()->add('no_of_vehicles.0', 'Sum of all units should be '.$required_unit);
				}
				/***end Sum of added no of vehicle should be as required ***/
				/*** making username as unique ***/
				$unique_user = array_unique($user_id_arr);
				$duplicate_user = array_diff_assoc($user_id_arr, $unique_user);
				if(!empty($duplicate_user)) {
					$error_got = 'yes';
					foreach($duplicate_user as $key => $value) {
						$validator->getMessageBag()->add('user_id.'.$key, 'User name already selected');
					}	
				}
				if($error_got == 'yes') {
					return back()->withInput()
					->withErrors($validator);
				}
				/***end making username as unique ***/
				// store
				$assign_dispatches = $existed_assign_disapatch_status = array();
				$assign_dispatch_qry = AssignDispatch::where('dispatch_id','=',$dispatch_id)->get()->toArray();
				if(isset($assign_dispatch_qry) && !empty($assign_dispatch_qry)) {
						foreach($assign_dispatch_qry as $_assign_key => $assign_val) {
							$user_id = $assign_val['user_id'];
							$status = $assign_val['status'];
							$assign_dispatches[$user_id] = $user_id;
							$existed_assign_disapatch_status[$user_id] = $status;
						}
				} else {}
				$data_insert = array();
				$user_type_arr = $request->user_type;
				$vehicle_id_arr = $request->vehicle_id;
				$no_of_vehicles_arr = $request->no_of_vehicles;
				$status_arr = $request->status;
				/*** get order or dispatch detail ***/
				$dispatch_detail = Dispatch::where('id','=',$dispatch_id)->with('getCustomer')->get();
				$dispatch_detail = $dispatch_detail[0];
				$order_time = $dispatch_date = $dispatch_detail->start_time;
				$start_location = $dispatch_detail->start_location;
				$dump_location = $dispatch_detail->dump_location;
				$customer_name = $dispatch_detail->getCustomer->company_name;
				/***end get order or dispatch detail ***/
				foreach($user_type_arr as $key => $value) {
					$new_arr = $result_data = array();
					$updated_by = Auth::user()->id;
					$user_type = $value;
					$user_id = $user_id_arr[$key];
					$vehicle_id = $vehicle_id_arr[$key];
					$no_of_vehicles = $no_of_vehicles_arr[$key];
					$no_of_vehicles_provide = $no_of_vehicles_arr[$key];
					$status = $status_arr[$key];
					$data_array = array('user_type' => $user_type,'user_id' => $user_id,'vehicle_id' => $vehicle_id,'no_of_vehicles' => $no_of_vehicles,'no_of_vehicles_provide' => $no_of_vehicles_provide,'dispatch_id' => $dispatch_id,'status' => $status,'updated_by' => $updated_by);
					
					/** check if same user id get again then remove from array at end only extra rows will left***/
					
					$assign_user_id = $user_id;
					$users_data_qry = User::where('id','=', $assign_user_id)->with('getVehicle')->get()->toArray();
					$user_data = $users_data_qry[0];
					$data_array['user_name'] = $user_data['name'];
					$data_array['contact_number'] = $user_data['country_code'].' '.$user_data['phone'];
					/*******  get vehicle number if employee selected */
					if(isset($user_data['get_vehicle']) && !empty($user_data['get_vehicle'])) {
						$data_array['vehicle_number'] = $user_data['get_vehicle']['vehicle_number'];
							
						} else {}
						/******* end get vehicle number if employee selected */
					if(in_array($user_id,$assign_dispatches)){
						if($existed_assign_disapatch_status[$user_id] != $status) {
							$send_notification = false;
							if($status == 'cancelled') {
								$send_notification = true;
								$notification_text = 'Your Dispatch has been cancelled by admin from '.$start_location." to ".$dump_location." (".$order_time.") ";
								$notification_text .= "\n";
								$notification_text .= $cancel_message;
							} else if( $status == 'pending' ) {
								$send_notification = true;
								$notification_text = 'New dispatch assigned for '.$start_location." to ".$dump_location." (".$order_time.") ";
							} else {
								$send_notification = true;
								$notification_text = 'Your Dispatch has been '.$status.' by admin from '.$start_location." to ".$dump_location." (".$order_time.") ";
							}
							if($send_notification) {
								/** send sms and push notification and get notifcation status ***/
								$result = $this->send_new_sms_notification($user_data,$notification_text);
								$result_data = json_decode($result, TRUE);
								/** end send sms and push notification and get notifcation status ***/
								if($status == 'cancelled') {
									$notification_data = $this->generate_cancel_push_notification_arr($result_data,$notification_text,$user_id);
									$notification_data = json_decode($notification_data, TRUE);
									$data_array = array_merge($data_array,$notification_data);
								} else {
									$notification_data = $this->generate_push_notification_arr($result_data,$notification_text,$user_id);
									$notification_data = json_decode($notification_data, TRUE);
									$data_array = array_merge($data_array,$notification_data);
								}
							}
						}
						unset($assign_dispatches[$user_id]);
					} else {
						$notification_text = false;
						$data_array['created_by'] = $updated_by;
						if( $status == 'pending' ) {
							$send_notification = true;
							$notification_text = 'New dispatch assigned for '.$start_location." to ".$dump_location." (".$order_time.") ";
						} else {
							$send_notification = true;
							$notification_text = 'Your Dispatch has been '.$status.' by admin from '.$start_location." to ".$dump_location." (".$order_time.") ";
						}
						if($send_notification) {
							/** send sms and push notification and get notifcation status ***/
							$result = $this->send_new_sms_notification($user_data,$notification_text);
							$result_data = json_decode($result, TRUE);
							/** end send sms and push notification and get notifcation status ***/
							$notification_data = $this->generate_push_notification_arr($result_data,$notification_text,$user_id);
							$notification_data = json_decode($notification_data, TRUE);
							$data_array = array_merge($data_array,$notification_data);
						}
					}
					/** end check if same user id get again then remove from array at end only extra rows will left***/
					
					AssignDispatch::updateOrCreate(array('user_id' => $user_id,'dispatch_id' => $dispatch_id), $data_array);
				}
				/** delete extra rows ***/
				if(isset($assign_dispatches) && !empty($assign_dispatches)) {
					foreach($assign_dispatches as $user_id) {
						AssignDispatch::where(array('user_id' => $user_id,'dispatch_id' => $dispatch_id))->delete();
					}
				} else {}
				/** end delete extra rows ***/
				/******if all assign dispatch cancelled then make dispatch as cancelled */
				$dispatch = Dispatch::find($dispatch_id);
				$dispatch->status = 'pending';
				$dispatches = AssignDispatch::where('dispatch_id','=',$dispatch_id)->where('status','!=', 'cancelled')->where('status','!=', 'decline')->get()->toArray();
				if(empty($dispatches)) {
					$dispatch->status = 'cancelled';
				} else {}
				$dispatch->save();
				/****** end if all assign dispatch cancelled then make dispatch as cancelled */
				// redirect
				Session::flash('message', 'Successfully assigned dispatch!');
				return redirect("assigned_dispatche/".$dispatch_id)->with('message', 'Successfully assigned dispatch!');
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
		if(Auth::user()->can('viewMenu:ActionDispatch') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
			$dispatch = AssignDispatch::find($id);
			$dispatch->delete();
			// redirect
			Session::flash('message', 'Successfully deleted dispatch!');
			return redirect()->route("assign_dispatches.index");
		}
		else
		{
			return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
		}
    }
}