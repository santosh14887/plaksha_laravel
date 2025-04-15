<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\DispatchTicket;
use App\Models\Dispatch;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserIssue;
use App\Models\AssignDispatch;
use App\Models\IssueCategory;
use App\Models\UserAuth;
use Auth;
use Validator;
use Session;
use DB;
use Carbon\Carbon;
use File;
use Illuminate\Support\Facades\Mail;
use App\Mail\IssueMail;
use App\Mail\AddTicketMail;
use App\Mail\DispatchStatusMail;
use Twilio\Rest\Client;
use Config;
class ApiController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
	  /**
            * success response method.
            *
            * @return \Illuminate\Http\Response
            */
 
		public function sendResponse($result, $message)
		{
			$response = [
				'success' => true,
				'data'    => $result,
				'message' => $message,
			];
			return response()->json($response, 200);
		}

		/**
		* return error response.
		*
		* @return \Illuminate\Http\Response
		*/

		public function sendError($error, $errorMessages = [], $code = 404)
		{
			$response = [
				'success' => false,
				'message' => $error,
			];

			if(!empty($errorMessages)){
				$response['data'] = $errorMessages;
			}
			return response()->json($response, $code);
		}
    public function login(Request $request)
	{
		$dispatch_oject = array();
		$expo_token = 'empty token';
		if(isset($request->expo_token) && $request->expo_token != '') {
			$expo_token = $request->expo_token;
		}
		
		if(Auth::attempt(['email' => $request->email, 'password' => $request->password,'type' => 'employee','status' => 'active'])){ 
			$user = Auth::user();
			$user_datas = User::find($user->id);
			$user_datas->expo_token = $expo_token;
			$user_datas->save();
			$success['token'] =  $user->createToken('MyApp')->plainTextToken; 
			$success['name'] =  $user->name;
			$success['user_data'] =  $user;
			return $this->sendResponse($success, 'User login successfully.');
		} 
		else{ 
			return $this->sendError('Invalid .', ['error'=>'Unauthorised']);
		} 
	}
	public function logout(Request $request){
		$user_id = $request->user_id;
		$user_datas = User::find($user_id);
		$user_datas->expo_token = '';
		$user_datas->save();
		$success['user_data'] =  'user logout';
		return $this->sendResponse($success, 'User logout successfully.');
	}
	public function dashboard(Request $request){
		$response_arr = array();
		$till_date = $request->till_date;
		$user_id = $request->user_id;
		$date_greter_then = '';
		$pending_dispatch = $complete_dispatch = $pending_ticket = $total_earning = 0;
		$user_datas = User::find($user_id);
		$user_status = $user_datas->status;
		$pending_query =  AssignDispatch::query();
		$complete_query =  AssignDispatch::query();
		$pending_ticket_query =  AssignDispatch::query();
		$ticket_completed_query =  DispatchTicket::query();
		$ticket_completed_query->where('status', 'completed');
		if($till_date == 'current_week' || $till_date == 'current_month') {
			$date_greter_then = ($till_date == 'current_week') ? date('Y-m-d', strtotime('-7 days')) : date('Y-m-d', strtotime('-30 days'));
			$pending_query->whereDate('created_at','>=',$date_greter_then);
			$pending_ticket_query->whereDate('accepted_decline_date','>=',$date_greter_then);
			$complete_query->whereDate('completed_date','>=',$date_greter_then);
			$ticket_completed_query->whereDate('created_at','>=',$date_greter_then);	
		} else if($till_date == 'today') {
			$date_greter_then = date('Y-m-d');
			$pending_query->whereDate('created_at','=',$date_greter_then);
			$pending_ticket_query->whereDate('accepted_decline_date','=',$date_greter_then);
			$complete_query->whereDate('completed_date','=',$date_greter_then);
			$ticket_completed_query->whereDate('created_at','=',$date_greter_then);
		}
		$total_earning = $ticket_completed_query->where('user_id','=',$user_id)->sum('expense');
		$pending_dispatch_arr = $pending_query->where('user_id','=',$user_id)->where('status','=','pending')
							->pluck('id')->toArray();
		$complete_dispatch_arr = $complete_query->where('user_id','=',$user_id)
							->where('status','=','completed')
							->pluck('id')->toArray();
		$pending_ticket_arr = $pending_ticket_query->where('user_id','=',$user_id)
							->where('status','=','accepted')
							->pluck('id')->toArray();
		
		$response_arr = array(
		'pending_dispatch' => count($pending_dispatch_arr),
		'complete_dispatch' => count($complete_dispatch_arr),
		'pending_ticket' => count($pending_ticket_arr),
		'total_earning' => $total_earning,
		//'date_greter_then' => $date_greter_then,
		);
		if($user_status == 'inactive' || $user_status == 'suspended') {
			$response_arr = array(
				'pending_dispatch' => '0',
				'complete_dispatch' => '0',
				'pending_ticket' => '0',
				'total_earning' => '0',
				//'date_greter_then' => $date_greter_then,
				);
		}
		return $this->sendResponse($response_arr, 'user_dashboard');
	}
	public function update_assign_dispatch_status(Request $request)
    {
		$id = $request->id;
		$status = $request->status;
		$date = Carbon::now();
		$accepted_decline_date = $date;
		$decline_reason = $request->decline_reason;
		$status_update = ($status == 'accept') ? 'accepted' : 'decline';
		$user_id = $request->user_id;
		$user_datas = User::find($user_id);
		$user_status = $user_datas->status;
		$assign_dispatches = AssignDispatch::where('id','=',$id)
		->where('user_id','=',$user_id)
		->pluck('user_id','user_id')->toArray();
		if(isset($assign_dispatches) && !empty($assign_dispatches) && $user_status == 'active') {
			
			try {
				/******send mail to admin **********/
				$admin_user_query = User::where('type','=','admin')->get()->toArray();
				$admin_user = $admin_user_query[0];
				$name = $admin_user['name'];
				$email = $admin_user['send_mail_on'];
				$assign_dispatch_qry = AssignDispatch::where('id','=',$id)->with('getUser')->with('getDispatch')->with('getDispatch.getCustomer')->get()->toArray();
				$assign_dispatch_data = $assign_dispatch_qry[0];
				$user_email = $assign_dispatch_data['get_user']['email'];
				$user_name = $assign_dispatch_data['get_user']['name'];
				$start_time = $assign_dispatch_data['get_dispatch']['start_time'];
				$company_name = $assign_dispatch_data['get_dispatch']['get_customer']['company_name'];
				$dispatch_name = $company_name.'('.$start_time.')';
				$mailData = [
						'title' => 'JapGobind Transport Dispach Status Update',
						'name' => $name,
						'user_email' => $user_email,
						'user_name' => $user_name,
						'dispatch' => $dispatch_name,
						'status' => $status_update,
						'decline_reason' => $decline_reason,
						];
						try {
							$mail_res = Mail::to($email)->send(new DispatchStatusMail($mailData));
						} catch (\Exception $e){
						}
				/******end send mail to admin **********/
				$assign_dispatch = AssignDispatch::find($id);
				$assign_dispatch->updated_by = $user_id;
				$assign_dispatch->status = $status_update;
				$assign_dispatch->updated_at = $date;
				$assign_dispatch->decline_reason = $decline_reason;
				$assign_dispatch->accepted_decline_date = $accepted_decline_date;
				$assign_dispatch->save();
				$success['status'] =  'success';
				return $this->sendResponse($success, 'Status updated successfully.');
			}  catch(Exception $e) {
				return $this->sendError('Please try later', ['error'=>'Please try later']);
			}
		}
		
	}
	public function get_order(Request $request)
    {
		$perPage = 9;
		$res_arr = array();
		$msg = '';
		$user_id = $request->user_id;
		$user_datas = User::find($user_id);
		$user_status = $user_datas->status;
		$dispatch_type = strtolower($request->type);
		$status_arr = ($dispatch_type != 'completed') ? array($dispatch_type) : array($dispatch_type,'submitted');
		$dispatches =  AssignDispatch::where('user_id', '=', $user_id)
		->whereIn('status', $status_arr)->with('getVehicle')->with('getDispatch')->latest()->paginate($perPage);
		if(null !== $dispatches && $user_status == 'active') {
			//$res_arr['last_page'] = $dispatches->lastPage();
			$msg = 'Data found';
			foreach($dispatches as $data) {
				/**  it is providing dispatch association with customer(No idea how its adding values in dispatches)  ***/
				 $getCustomer = $data->getDispatch->getCustomer;
				/** end it is providing dispatch association with customer(No idea how its adding values in dispatches)  ***/
			}
		} else{
			$msg = 'No data found';
		}
        return $this->sendResponse($dispatches, $msg);
    }
	public function get_ticket(Request $request) {
		$user_id = $request->user_id;
		$user_datas = User::find($user_id);
		$user_status = $user_datas->status;
		$dispatch_id  = $request->dispatch_id;
		$assign_dispatch_id = $request->assign_dispatch_id;
		$ticket_added = DispatchTicket::where('user_id','=',$user_id)
						->where('dispatch_id','=',$dispatch_id)
						->where('assign_dispatch_id','=',$assign_dispatch_id)->with('getAssignDispatch')->get();
		if($user_status != 'active') {
			return $this->sendError('Please try later.Some error occured', ['error'=>'Please try later.Some error occured']);
		} else {
			return $this->sendResponse($ticket_added,'ticked detail');
		}
		
	}
	public function add_ticket(Request $request) {
			$user_id = $request->user_id;
			$user_datas = User::find($user_id);
			$user_status = $user_datas->status;
			if($user_status != 'active') {
				return $this->sendError('Please try later.Some error occured', ['error'=>'Please try later.Some error occured']);
				die;
			}
			$dispatch_id = $request->dispatch_id;
			$assign_dispatch_id = $request->assign_dispatch_id;
			$uda_for_iamge_name_concat = $user_id.''.$dispatch_id.''.$assign_dispatch_id;
			$user_query = User::where('id','=',$user_id)->with('getVehicle')->get()->toArray();
			$admin_user_query = User::where('type','=','admin')->get()->toArray();
			$dispatch_query = Dispatch::where('id','=',$dispatch_id)->with('getCustomer')->get()->toArray();
			$admin_user = $admin_user_query[0];
			$admin_id = $admin_user['id'];
			$displatch_data = $dispatch_query[0];
			$get_customer = $displatch_data['get_customer'];
			$dispatch_name = $get_customer['company_name'].'('.$displatch_data['start_time'].')';
			$disp_job_type = $displatch_data['job_type'];
			$disp_job_rate = $displatch_data['job_rate'];
			/*** laod rate will come from dispatch for all users  ****/
			$user_load_rate = $displatch_data['employee_rate'];
			/*** end laod rate will come from dispatch for all users  ****/
			$user = $user_query[0];
			$hourly_rate = $data_array['emp_brok_hour_rate'] = $user['hourly_rate'];
			//$load_rate = $data_array['emp_brok_load_rate'] = $user['load_per'];
			$load_rate = $data_array['emp_brok_load_rate'] = $user_load_rate;
			$ticket_hr_ld_rate = $request->hour_or_load;
			$income = $ticket_hr_ld_rate * $disp_job_rate;
			$expense = 0;
			if($disp_job_type == 'hourly') {
				$expense = $ticket_hr_ld_rate * $hourly_rate;
			} else {
				$expense = $ticket_hr_ld_rate * $load_rate;
			}
			$profit = 0;
			if($expense < $income) {
				$profit = $income - $expense;
			} else if($income < $expense) {
				$profit = $expense - $income;
			} else {}
			$user_name = $user['name'];
			$user_email = $user['email'];
			$name = $admin_user['name'];
			$email = $admin_user['send_mail_on'];
			$ticket = $request->ticket_number;
			$ticket_status = 'pending';
			$default_ticket_number = 'JPGT'.date('Ymdhis');
			$total_km = $request->ending_km - $request->starting_km;
			$data_array['created_by'] = $user_id;
			$data_array['dispatch_id'] = $dispatch_id;
			$data_array['assign_dispatch_id'] = $assign_dispatch_id;
			$data_array['broker_vehicle_id'] = 0;
			$data_array['shift_type'] = strtolower($request->shift_type);
			$data_array['user_type'] = $user['type'];
			$data_array['user_id'] = $user_id;
			$data_array['driver_name'] = $user_name;
			$data_array['unit_vehicle_number'] = $user['get_vehicle']['vehicle_number'];
			$data_array['contact_number'] = $user['phone'];
			$data_array['starting_km'] = $request->starting_km;
			$data_array['ending_km'] = $request->ending_km;
			$data_array['total_km'] = $total_km;
			$data_array['fuel_qty'] = ($request->fuel_qty == '' || $request->fuel_qty == null) ? 0 : $request->fuel_qty;
			$data_array['fuel_card_number'] = $request->fuel_card_number;
			$data_array['def_qty'] = ($request->def_qty == '' || $request->def_qty == null) ? 0 : $request->def_qty;
			$data_array['gas_station_location'] = $request->gas_station_location;
			$data_array['ticket_number'] = $ticket;
			$data_array['hour_or_load'] = $request->hour_or_load;
			$data_array['income'] = $income;
			$data_array['expense'] = $expense;
			$data_array['profit'] = $profit;
			$data_array['status'] = $ticket_status;
			$data_array['default_ticket_number'] = $default_ticket_number;
			$data_array['total_load'] = ($request->total_load) ? $request->total_load : 0;
			$data_array['hour_or_load_integer'] = (is_numeric($request->hour_or_load)) ? $request->hour_or_load : 0;
			
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
			if($request->file('ticket_img') != null){
				$file= $request->file('ticket_img');
				$exte = $file->extension();
				$filename = date('YmdHis').''.$uda_for_iamge_name_concat.'.'.$exte;
				$file-> move(public_path('images/ticket_img'), $filename);
				$data_array['ticket_img'] = $filename;
			} else {
				$data_array['ticket_img'] = '';
			}
			
			//return $this->sendResponse($data_array, 'Added successfully');
			$check_ticket_added = DispatchTicket::where('user_id','=',$user_id)->where('dispatch_id','=',$dispatch_id)->where('assign_dispatch_id','=',$assign_dispatch_id)->get()->toArray();
			if(isset($check_ticket_added) && !empty($check_ticket_added)) {
				return $this->sendResponse($success, 'Ticket already added for the dispatch');
			} else {
			$data = DispatchTicket::Create($data_array);
			$mailData = [
				'title' => 'JapGobind Transport New Ticket',
				'name' => $name,
				'user_email' => $user_email,
				'user_name' => $user_name,
				'ticket' => $ticket,
				'dispatch' => $dispatch_name,
				];
				
				try {
					$mail_res = Mail::to($email)->send(new AddTicketMail($mailData));
				} catch (\Exception $e){
				}
			if($data->id > 0) {
				/**if all asign user  completed ticket make order as completed  ****/
				$dispatch_status_data = 'pending';
				$dispatch_status_update = array('status' => $dispatch_status_data);
				$dispatch_status_where = array('id' => $dispatch_id);
				DB::table('dispatches')
				->where($dispatch_status_where)
				->update($dispatch_status_update);
				/**end if all asign user  completed ticket make order as completed  ****/
				/*** make ticket as submitted  */
				$assign_dispatch_status_data = 'submitted';
				$assign_dispatch_status_update = array('status' => $assign_dispatch_status_data);
				$assign_dispatch_status_where = array('id' => $assign_dispatch_id);
				DB::table('assign_dispatches')
				->where($assign_dispatch_status_where)
				->update($assign_dispatch_status_update);
				/*** end make ticket as submitted  */
				$success['data'] =  'Added successfully';
			return $this->sendResponse($success, 'Added successfully');
			} else {
				return $this->sendError('Please try later.Some error occured', ['error'=>'Please try later.Some error occured']);
			}
		}
			
	}
	public function user_update(Request $request)
    {
		$user_id = $id = $request->user_id;
		$user_datas = User::find($user_id);
			$user_status = $user_datas->status;
			if($user_status != 'active') {
				return $this->sendError('Please try later.Some error occured', ['error'=>'Please try later.Some error occured']);
				die;
			}
		$email = strtolower($request->email);
		$duplicate_user = User::where('id', '!=', $id)->where('email', '=', $email)->get()->toArray();
		if(isset($duplicate_user) && !empty($duplicate_user)) {
			return $this->sendError('Email already exist', ['error'=>'Email already exist']);
		} else {
				$date = Carbon::now();
				$user = User::find($id);
				$user->first_name = $request->first_name;
				$user->last_name = $request->last_name;
				$user->name = $request->name;
				$user->email = $email;
				$user->phone = $request->phone;
				$user->hst = $request->hst;
				$user->address = $request->address;
				$user->license_number = $request->license_number;
				$user->updated_at = $date;
				$user->save();
			$success['data'] =  'profile updated';
			return $this->sendResponse($success, 'profile updated');
		}
    }
	public function get_user_issue(Request $request)
	{
		$perPage = 9;
		$user_id = $request->user_id;
		$user_datas = User::find($user_id);
		$user_status = $user_datas->status;
		if($user_status != 'active') {
			return $this->sendError('Please try later.Some error occured', ['error'=>'Please try later.Some error occured']);
			die;
		}
		$user_issue = UserIssue::where(['user_id' => $user_id])->with('getIssueCatgory')->latest()->paginate($perPage); 
		return $this->sendResponse($user_issue, 'issue_data');
	}
	public function notification(Request $request)
	{
		$perPage = 9;
		$user_id = $request->user_id;
		$user_datas = User::find($user_id);
		$user_status = $user_datas->status;
		if($user_status != 'active') {
			return $this->sendError('Please try later.Some error occured', ['error'=>'Please try later.Some error occured']);
			die;
		}
		$notifiaction = Notification::where(['user_id' => $user_id])->latest()->paginate($perPage); 
		return $this->sendResponse($notifiaction, 'notifiaction');
	}
	public function add_issue(Request $request)
    {
		$user_id = $request->user_id;
		$user_datas = User::find($user_id);
		$user_status = $user_datas->status;
		if($user_status != 'active') {
			return $this->sendError('Please try later.Some error occured', ['error'=>'Please try later.Some error occured']);
			die;
		}
		$title = $request->title;
		$description = $request->description;
		$issue_category = $request->issue_category;
		$user = User::where('id', '=', $user_id)->with('getVehicle')->get()->toArray();
		$admin_user = User::where('type', '=', 'admin')->get()->toArray();
		$issue_category_qry = IssueCategory::where('id', '=', $issue_category)->get()->toArray();
		$email = $admin_user[0]['send_mail_on'];
		$name = $admin_user[0]['name'];
		$category_name = $issue_category_qry[0]['title'];
		$user = $user[0];
		$user_type = $user['type'];
		$user_name = $user['name'];
		$user_email = $user['email'];
		$user_phone = $user['country_code'].''.$user['phone'];
		$vehicle_id = $vehicle_number = $licence_plate = $vin_number = '';
		if(isset($user['get_vehicle']) && !empty($user['get_vehicle'])) {
			$vehicle_id = $user['get_vehicle']['id'];
			$vehicle_number = $user['get_vehicle']['vehicle_number'];
			$licence_plate = $user['get_vehicle']['licence_plate'];
			$vin_number = $user['get_vehicle']['vin_number'];
		} else{}
		$user_issue = new UserIssue;
		$user_issue->user_id = $user_id;
		$user_issue->user_name = $user_name;
		$user_issue->user_email = $user_email;
		$user_issue->user_phone = $user_phone;
		$user_issue->user_type = $user_type;
		$user_issue->vehicle_id = $vehicle_id;
		$user_issue->vehicle_number = $vehicle_number;
		$user_issue->licence_plate = $licence_plate;
		$user_issue->vin_number = $vin_number;
		$user_issue->issue_category = $issue_category;
		$user_issue->title = $title;
		$user_issue->description = $description;
		$user_issue->save();
		$mailData = [
				'title' => 'JapGobind Transport New Issue',
				'name' => $name,
				'user_email' => $user_email,
				'user_name' => $user_name,
				'title' => $title,
				'description' => $description,
				'category_name' => $category_name,
				];
				try {
					$mail_res = Mail::to($email)->send(new IssueMail($mailData));
				} catch (\Exception $e){
				}
				
		$success['data'] =  'Issue added auccessfully';
		return $this->sendResponse($success, 'Issue added auccessfully');
    }
	public function user(Request $request)
    {
		$user_id = $request->user_id;
		$user_datas = User::find($user_id);
		$user_status = $user_datas->status;
		if($user_status != 'active') {
			return $this->sendError('Please try later.Some error occured', ['error'=>'Please try later.Some error occured']);
			die;
		}
		$user = User::where('id', '=', $user_id)->get();
        $success['data'] =  $user;
        return $this->sendResponse($user, 'User data');
    }
	public function issue_category(Request $request)
    {
		$issue_category = IssueCategory::get();
        return $this->sendResponse($issue_category, 'issue category');
    }
}