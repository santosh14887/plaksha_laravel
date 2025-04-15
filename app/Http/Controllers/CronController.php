<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\VehicleService;
use App\Models\AssignDispatch;
use App\Models\Vehicle;
use Notifiable;
use App\Notifications\WelcomeNotification;
use Illuminate\Http\Request;
use DB;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use Illuminate\Support\Facades\Mail;
use App\Mail\NormalServiceMail;
use App\Mail\AirFilterMail;

class CronController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('guest');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function cron_air_filter()
    {
		$all_records = array();
		$current_date = date('Y-m-d');
		$more_days = 2;
		$next_two_day = date('Y-m-d', strtotime($current_date. ' + '.$more_days.' days'));
		$air_filter_query = Vehicle::whereDate('due_air_filter_date', '>=', $current_date)->whereDate('due_air_filter_date', '<=', $next_two_day)->get()->toArray();
		if(isset($air_filter_query) && !empty($air_filter_query)) {
			foreach($air_filter_query as $air_filter_query_val) {
				$vehicle_number = $air_filter_query_val['vehicle_number'];
				$vin_number = $air_filter_query_val['vin_number'];
				$last_air_filter_date = date("Y-m-d", strtotime($air_filter_query_val['last_air_filter_date']));
				$due_air_filter_date = date("Y-m-d", strtotime($air_filter_query_val['due_air_filter_date']));
				$all_records[] = array(
				'vehicle_number' => $vehicle_number,
				'vin_number' => $vin_number,
				'last_air_filter_date' => $last_air_filter_date,
				'due_air_filter_date' => $due_air_filter_date,
				);
			}
		}
		if(isset($all_records) && !empty($all_records)) {
			/******send mail to admin **********/
				$admin_user_query = User::where('type','=','admin')->get()->toArray();
				$admin_user = $admin_user_query[0];
				$name = $admin_user['name'];
				$email = $admin_user['send_mail_on'];
			$mailData = [
						'title' => 'JapGobind Transport Service Reminder',
						'name' => $name,
						'all_records' => $all_records
						];
			try{
				$mail_res = Mail::to($email)->send(new AirFilterMail($mailData));
			} catch (\Exception $e){
			}
		}
		die('68');
	}
	public function cron_normal_service()
    {
		$all_records = array();
		$vehicle_ser = VehicleService::select('vehicle_id', DB::raw('MAX(on_km) as max_km'))->where('service_type','regular_service')->groupBy('vehicle_id')->with('getVehicle')->get()->toArray();
		
		if(isset($vehicle_ser) && !empty($vehicle_ser)) {
			foreach($vehicle_ser as $vehicle_ser_val) {
				$prev_km = $vehicle_ser_val['max_km'];
				$vehicle_number = $vehicle_ser_val['get_vehicle']['vehicle_number'];
				$vin_number = $vehicle_ser_val['get_vehicle']['vin_number'];
				$due_after_km = $vehicle_ser_val['get_vehicle']['service_due_every_km'];
				$total_run_km = $vehicle_ser_val['get_vehicle']['total_km'];
				$total_cron_km = $prev_km +  $due_after_km;
				if($total_run_km >= $total_cron_km) {
					$all_records[] = array(
					'vehicle_number' => $vehicle_number,
					'vin_number' => $vin_number,
					'previous_service_km' => $prev_km,
					'due_after_km' => $due_after_km,
					'total_run_km' => $total_run_km,
					);
				}
			}
		}
		if(isset($all_records) && !empty($all_records)) {
			/******send mail to admin **********/
				$admin_user_query = User::where('type','=','admin')->get()->toArray();
				$admin_user = $admin_user_query[0];
				$name = $admin_user['name'];
				$email = $admin_user['send_mail_on'];
			$mailData = [
						'title' => 'JapGobind Transport Service Reminder',
						'name' => $name,
						'all_records' => $all_records
						];
				try {		
					$mail_res = Mail::to($email)->send(new NormalServiceMail($mailData));
				} catch (\Exception $e){
				}
						die('106');
		}
	}
	public function order_pending_notification()
    {
		$date = date("Y-m-d H:i:s", time() - 1800);
		$current_date = date("Y-m-d H:i:s");
		$dispatches =  AssignDispatch::with('getDispatch')->with('getUser')->where('created_at', '<=', $date)
		->where('created_at', '>=', $current_date)
		->where('status', '=', 'pending')->get();
		if(null !== $dispatches) {
			foreach($dispatches as $dispatches_vals) {
					$dispatch_id = $dispatches_vals->id;
					$user_id = $dispatches_vals->user_id;
					$start_location = $dispatches_vals->getDispatch->start_location;
					$dump_location = $dispatches_vals->getDispatch->dump_location;
					$order_time = $dispatches_vals->getDispatch->start_time;
					$expo_token = $dispatches_vals->getUser->expo_token;
					$notification_text = $start_location." to ".$dump_location." (".$order_time.") ";
					$notification_text .= "\n";
					/*** it is to send push notification  ***/
					$result_data = array();
					if(isset($expo_token) && $expo_token != '') {
						$body_text = 'New dispatch assigned confirmaion pending for '.$notification_text;
					$data_arr = array(
					"to" => $expo_token,
					"sound" => "default",
					"body" => $body_text,
					"remote" => true,
				   "channelId" => "japgobind",
					"content" => array(
					  "autoDismiss" => true,
					  "badge" => 2,
					  "body" => "connent",
					  "sound" => "default",
					  "sticky" => false,
					  "subtitle" => null,
					  "title" =>"new_order"
					)
				);
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, "https://exp.host/--/api/v2/push/send");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_arr));
					curl_setopt($ch, CURLOPT_POST, 1);

					$headers = array();
					$headers[] = 'Content-Type: application/json';
				   // $headers[] = 'Authorization: key=<YOUR-AUTH-KEY>';
					curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
					$result = curl_exec($ch);
					$result_data = json_decode($result, TRUE);
					$new_arr = array();
					if(isset($result_data['data']) && !empty($result_data['data'])) {
							if(isset($result_data['data']['status']) && $result_data['data']['status'] == 'error') {
							} else{
								$msg_id = $result_data['data']['id'];
								$notification = new Notification();
								$notification->user_id = $user_id;
								$notification->push_notification_message_id = $msg_id;
								$notification->message = $body_text;
								$notification->save();
							}
						
					}
					
				}
			/*** end it is to send push notification  ***/
			}
		}
		die('176');
	}
}