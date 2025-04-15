<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Dispatch;
use App\Models\Customer;
use App\Models\CredsDetail;
use App\Models\DispatchTicket;
use App\Models\VehicleService;
use Notifiable;
use App\Notifications\WelcomeNotification;
use Illuminate\Http\Request;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\DemoMail;
use DB;
use Auth;
use Twilio\Rest\Client;
use Config;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;
use QuickBooksOnline\API\Facades\Customer as quickbookCustomer; 
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Invoice as QuickbookInvoice;
use QuickBooksOnline\API\Facades\Vendor as quickbookVendor;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
	public function gas_api(Request $request)
    {
		$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, "https://api.collectapi.com/gasPrice/canada");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
					curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
					curl_setopt($ch, CURLOPT_HTTP_VERSION, "CURL_HTTP_VERSION_1_1");

					$headers = array();
					$headers[] = 'Content-Type: application/json';
				    $headers[] = 'authorization: apikey 4kxNTUBlrXB924BmvZTctR:1MaQkDTs8NPiN5eZxJdgls';
					curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
					$result = curl_exec($ch);
					$result_data = json_decode($result, TRUE);
					echo '<pre>';
					print_r($result);
					print_r($result_data);
					echo '</pre>';
		die('in gas api');
	}
	public function vehicle_api(Request $request,$id)
    {
		$url = "https://vpic.nhtsa.dot.gov/api/vehicles/decodevinvalues/".$id."?format=json";
         
        $crl = curl_init();
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
         
        $response = curl_exec($crl);
        if(!$response){
           die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
        }
		$vehicle_des_arr = array();
		//$res_arr = json_decode($response);
		$res_arr = json_decode($response, true);
		$res = $res_arr['Results'][0];
		$vehicle_des_arr['BodyCabType'] = $res['BodyCabType'];
		$vehicle_des_arr['BodyClass'] = $res['BodyClass'];
		$vehicle_des_arr['BrakeSystemType'] = $res['BrakeSystemType'];
		$vehicle_des_arr['DisplacementCC'] = $res['DisplacementCC'];
		$vehicle_des_arr['DisplacementCI'] = $res['DisplacementCI'];
		$vehicle_des_arr['DisplacementL'] = $res['DisplacementL'];
		$vehicle_des_arr['DriveType'] = $res['DriveType'];
		$vehicle_des_arr['EngineConfiguration'] = $res['EngineConfiguration']; 
		$vehicle_des_arr['EngineCycles'] = $res['EngineCycles'];
		$vehicle_des_arr['EngineCylinders'] = $res['EngineCylinders'];
		$vehicle_des_arr['EngineHP'] = $res['EngineHP'];
		$vehicle_des_arr['EngineHP_to'] = $res['EngineHP_to'];
		$vehicle_des_arr['EngineKW'] = $res['EngineKW'];
		$vehicle_des_arr['EngineManufacturer'] = $res['EngineManufacturer'];
		$vehicle_des_arr['EngineModel'] = $res['EngineModel'];
		$vehicle_des_arr['ErrorText'] = $res['ErrorText'];
		$vehicle_des_arr['FuelTypePrimary'] = $res['FuelTypePrimary'];
		$vehicle_des_arr['GVWR'] = $res['GVWR'];
		$vehicle_des_arr['Make'] = $res['Make'];
		$vehicle_des_arr['MakeID'] = $res['MakeID'];
		$vehicle_des_arr['Manufacturer'] = $res['Manufacturer'];
		$vehicle_des_arr['ManufacturerId'] = $res['ManufacturerId'];
		$vehicle_des_arr['Model'] = $res['Model'];
		$vehicle_des_arr['ModelID'] = $res['ModelID'];
		$vehicle_des_arr['ModelYear'] = $res['ModelYear'];
		$vehicle_des_arr['OtherEngineInfo'] = $res['OtherEngineInfo'];
		$vehicle_des_arr['PlantCity'] = $res['PlantCity'];
		$vehicle_des_arr['PlantCompanyName'] = $res['PlantCompanyName'];
		$vehicle_des_arr['PlantCountry'] = $res['PlantCountry'];
		$vehicle_des_arr['PlantState'] = $res['PlantState'];
		$vehicle_des_arr['VIN'] = $res['VIN']; 
		$vehicle_des_arr['VehicleDescriptor'] = $res['VehicleDescriptor']; 
		$vehicle_des_arr['VehicleType'] = $res['VehicleType'];  
		$vehicle_desc = json_encode($vehicle_des_arr);
		echo $vehicle_desc;
		die;
	}
	public function quickbook_test(Request $request)
    {
		$quicbook_creds =  CredsDetail::where('creds_for', '=', 'quickbook')->get()->toArray();
		$quicbook_creds = $quicbook_creds['0'];
		$row_id = $quicbook_creds['id'];
		
		/***** Update the OAuth2Token ******/
		
		$oauth2LoginHelper = new OAuth2LoginHelper($quicbook_creds['client_id'],$quicbook_creds['client_secret']);
		
		$accessTokenObj = $oauth2LoginHelper->refreshAccessTokenWithRefreshToken($quicbook_creds['refresh_token']);
		
		try {
		$accessTokenValue = $accessTokenObj->getAccessToken();
		$refreshTokenValue = $accessTokenObj->getRefreshToken();
		} catch (Exception $e) {
            dd("Error: ". $e->getMessage());
        }
		$creds_details = CredsDetail::find($row_id);
		$creds_details->access_token = $accessTokenValue;
		$creds_details->refresh_token = $refreshTokenValue;
		$creds_details->save();
		/***** end Update the OAuth2Token ******/
		$dataService = DataService::Configure([
            'auth_mode' => $quicbook_creds['auth_mode'],
            'ClientID' => $quicbook_creds['client_id'],
            'ClientSecret' => $quicbook_creds['client_secret'],
            'RedirectURI' => $quicbook_creds['redirect_uri'],
            'accessTokenKey' => $accessTokenValue,
            'refreshTokenKey' => $refreshTokenValue,
            'QBORealmID' => $quicbook_creds['realm_id'],
            'baseUrl' => $quicbook_creds['type'],
        ]);
		$dataService->throwExceptionOnError(true);
		//Add a new Vendor
		/* $theResourceObj = quickbookCustomer::create([
			"BillAddr" => [
				"Line1" => "123 Main Street",
				"City" => "Mountain View",
				"Country" => "USA",
				"CountrySubDivisionCode" => "CA",
				"PostalCode" => "94042"
			],
			"Notes" => "Here are other details.",
			"Title" => "Mr",
			"GivenName" => "santosh",
			"MiddleName" => "kumar",
			"FamilyName" => "sharma",
			"Suffix" => "Jr",
			"FullyQualifiedName" => "santosh kumar",
			"CompanyName" => "santosh kumar",
			"DisplayName" => "santosh kumar Displayname",
			"PrimaryPhone" => [
				"FreeFormNumber" => "(555) 555-5555"
			],
			"PrimaryEmailAddr" => [
				"Address" => "santosh@jap.com"
			]
		]);

		$resultingObj = $dataService->Add($theResourceObj);
		$error = $dataService->getLastError();
		if ($error) {
			echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
			echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
			echo "The Response message is: " . $error->getResponseBody() . "\n";
		}
		else {
			echo "Created Id={$resultingObj->Id}. Reconstructed response body:\n\n";
			$xmlBody = XmlObjectSerializer::getPostXmlFromArbitraryEntity($resultingObj, $urlResource);
			echo $xmlBody . "\n";
		} */
		//Add a new Invoice
		/* $theResourceObj = QuickbookInvoice::create([
			"Line" => [
			[
				 "Amount" => 100.00,
				 "DetailType" => "SalesItemLineDetail",
				 "SalesItemLineDetail" => [
				   "ItemRef" => [
					 "value" => 1,
					 "name" => "Services"
				   ]
				 ]
			]
			],
			"CustomerRef"=> [
				  "value"=> 1
			],
			"BillEmail" => [
				  "Address" => "Familiystore@intuit.com"
			],
			"BillEmailCc" => [
				  "Address" => "a@intuit.com"
			],
			"BillEmailBcc" => [
				  "Address" => "v@intuit.com"
			]
		]);
		$resultingObj = $dataService->Add($theResourceObj);
		$error = $dataService->getLastError();
		if ($error) {
			echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
			echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
			echo "The Response message is: " . $error->getResponseBody() . "\n";
		}
		else {
			echo "Created Id={$resultingObj->Id}. Reconstructed response body:\n\n";
			$xmlBody = XmlObjectSerializer::getPostXmlFromArbitraryEntity($resultingObj, $urlResource);
			echo $xmlBody . "\n";
		} */
		/*$displayname = "santosh kumar Displayname";
		echo $query = "SELECT * FROM Customer WHERE DisplayName = '".$displayname."'";
		 $customer_search = $dataService->Query($query);
		 echo '<pre>';
		 print_r($customer_search);
		 echo '</pre>'; */
		 /****** get employee user company name   *********/
		/* $company_name_arr = User::where('company_corporation_name','!=','')->where('company_corporation_name','!=','NA')
		 ->where('type','=','employee')->get()->toArray();
		 if(isset($company_name_arr) && !empty($company_name_arr)) {
			 foreach($company_name_arr as $company_name_arr_val) {
				$id = $company_name_arr_val['id'];
				$street_line = $company_name_arr_val['street_line'];
				$city = $company_name_arr_val['city'];
				$country = $company_name_arr_val['country'];
				$country_devision_code = $company_name_arr_val['country_devision_code'];
				$postal_code = $company_name_arr_val['zip_code'];
				$email = $company_name_arr_val['email'];
				$company_corporation_name = $company_name_arr_val['company_corporation_name'];
				$company_name = $displayname = $company_corporation_name;
				$vendor_query = "SELECT * FROM vendor WHERE DisplayName = '".$displayname."'";
				 $vendor_search = $dataService->Query($vendor_query);
				 if(isset($vendor_search) && !empty($vendor_search)) {
					 $quickbook_id = $vendor_search[0]->Id;
					 $user = User::find($id);
					$user->quickbook_id = $quickbook_id;
					$user->save();
					echo 'User id :- '.$id.' with  quick id :- '.$quickbook_id.'<br>';
				 } else {
					 $customer_hst = '';
					 $quickbook_id = 0;
					 $quickbook_res = '';
					  
						try {
							$theResourceObj = quickbookVendor::create([
								"BillAddr" => [
									"Line1" => $street_line,
									"City" => $city,
									"Country" => $country,
									"CountrySubDivisionCode" => $country_devision_code,
									"PostalCode" => $postal_code
								],
								"Notes" => $customer_hst,
								"Title" => "",
								"GivenName" => $company_name,
								"MiddleName" => "",
								"FamilyName" => "",
								"Suffix" => "",
								"FullyQualifiedName" => '',
								"CompanyName" => '',
								"DisplayName" => '',
								"PrimaryPhone" => [
									"FreeFormNumber" => ""
								],
								"PrimaryEmailAddr" => [
									"Address" => $email
								]
							]);

							$resultingObj = $dataService->Add($theResourceObj);
							$error = $dataService->getLastError();
							if ($error) {
								$quickbook_res = "The Status code is: " . $error->getHttpStatusCode() . "\n";
								$quickbook_res .= "The Helper message is: " . $error->getOAuthHelperError() . "\n";
								$quickbook_res .= "The Response message is: " . $error->getResponseBody() . "\n";
							}
							else {
								$quickbook_id = $resultingObj->Id;
							}
						} catch (\Exception $e){
							$message = $e->getMessage();
						 
						}
						$user = User::find($id);
						$user->quickbook_id = $quickbook_id;
						$user->quickbook_res = $quickbook_res;
						$user->save();
						echo 'User id :- '.$id.' with  quick id :- '.$quickbook_id.'<br>';
				 }
			 }
		 } else {} */
		 
		 /******** it is used in dispatch invoice for tax rate like 13%**********/
		 $query = "SELECT * FROM TaxRate";
			$tax_rate_search = $dataService->Query($query);
			/******** it is used in dispatch invoice for hour or load**********/
		 $query = "SELECT * FROM Item";
			$item_search = $dataService->Query($query);
			/*****  it is APAccountRef while creating bill for employee *******/
			$account_query = "select * from Account where Classification = 'Liability' && AccountSubType = 'AccountsPayable'";
			$account_item_search = $dataService->Query($account_query);
			foreach($account_item_search as $vals) {
			//	echo $vals->Id.' '.$vals->FullyQualifiedName.'<br>';
			}
			/******* bookkipping->charts of accounts used in bill category******/
			$expense_account_query = "select * from Account where AccountType = 'Expense' and Name = 'TestLoad'";
			$expense_account_search = $dataService->Query($expense_account_query);
			if(isset($expense_account_search) && !empty($expense_account_search)) {
				foreach($expense_account_search as $vals) {
				//	echo $vals->Id.' '.$vals->FullyQualifiedName.'<br>';
				}
			} else {}
			/***** used in bill payment as bank account ref while we make eployee invoice as complted *********/
			$bill_payment_account_query = "select * from Account where AccountType = 'Bank' && AccountSubType  = 'Checking'";
			$bill_payment_account_search = $dataService->Query($bill_payment_account_query);
			foreach($bill_payment_account_search as $vals) {
			//	echo $vals->Id.' '.$vals->FullyQualifiedName.'<br>';
			}
			/******** it is used in dispatch invoice for tax rate like 13%**********/
			echo '<pre>';
		 $tax_code_query = "SELECT * FROM TaxCode";
			$tax_code_query_search = $dataService->Query($tax_code_query);
			/* foreach($tax_code_query_search as $vals) {
				if(isset($vals->PurchaseTaxRateList->TaxRateDetail->TaxRateRef)) {
				//	echo $vals->Id.' || '.$vals->Name.' || '.$vals->Description.' || '.$vals->PurchaseTaxRateList->TaxRateDetail->TaxRateRef.'<br>';
				//	print_r($vals->name);
				}
			} */
			///for invoice
			foreach($tax_code_query_search as $vals) {
				if(isset($vals->SalesTaxRateList->TaxRateDetail->TaxRateRef)) {
					echo $vals->Id.' || '.$vals->Name.' || '.$vals->Description.' || '.$vals->SalesTaxRateList->TaxRateDetail->TaxRateRef.'<br>';
				//	print_r($vals);
				}
			}
			foreach($tax_rate_search as $vals) {
				if(isset($vals->EffectiveTaxRate->RateValue) && $vals->TaxReturnLineRef > 0 && $vals->EffectiveTaxRate->RateValue = 13) {
				//	echo $vals->Id.' '.$vals->Name.' || '.$vals->Description.' || '.$vals->TaxReturnLineRef.'<br>';
				//	print_r($vals->name);
				}
			}
			
			//print_r($item_search[0]->Id);
			echo '</pre>'; 
			/* $query = "SELECT * FROM TaxRate";
			$template_search = $dataService->Query($query);
			echo '<pre>';
			print_r($template_search);
			echo '</pre>'; */
			/* $quickbook_invoice = QuickbookInvoice::create([
							"Id" => 194
						]);
			$path = public_path('images/pdf');
			$directoryForThePDF = $dataService->DownloadPDF($quickbook_invoice, $path);
			$pdf_name = str_replace($path,'',$directoryForThePDF);
			echo $pdf_name; */
		die('');
	}
	public function parseAuthRedirectUrl($url)
	{
		parse_str($url,$qsArray);
		return array(
			'code' => $qsArray['code'],
			'realmId' => $qsArray['realmId']
		);
	}
	public function twillio_test(Request $request,$number)
    {
		$app_constant_name = Config::get('constants.APP_CONSTANT_NAME');
		$receiverNumber = $number;
        $message = "Hello Santosh\n";
        $message .= "You have registered as driver in ".$app_constant_name." with login detail.\n";
        $message .= "username :- s.kumar14887@gmail.com\n";
        $message .= "password :- 12345\n";
  
        try {
  
            $account_sid = Config::get('constants.TWILIO_SID');
            $auth_token = Config::get('constants.TWILIO_TOKEN');
            $twilio_number = Config::get('constants.TWILIO_FROM');
			$msg_service_sid = Config::get('constants.Twillio_messagingServiceSid');
            $client = new Client($account_sid, $auth_token);
            $res = $client->messages->create($receiverNumber, [
                'from' => $twilio_number, 
				"messagingServiceSid" => $msg_service_sid,
                'body' => $message]);
            dd('SMS Sent Successfully.');
  
        } catch (Exception $e) {
            dd("Error: ". $e->getMessage());
        }
	}
	public function email_test(Request $request, $email)
    {
		$mailData = [
            'title' => 'JapGobind Transport Register',
            'body' => 'This is for testing email using smtp.',
			'name' => 'santosh kumar',
			'email' => 's.kumar14887@gmail.com',
			'password_string' => '123'
        ];
        $mail_res = Mail::to($email)->send(new DemoMail($mailData));
		echo '<pre>';
		print_r($mail_res);
		echo '</pre>';
           
        dd("Email is sent successfully.");
	}
	public function notification_test(Request $request, $id)
    {
		$body_text = 'New order received from test nitification';
					$data_arr = array(
					"to" => $id,
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
					//curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_arr));
					curl_setopt($ch, CURLOPT_POST, 1);

					$headers = array();
					$headers[] = 'Content-Type: application/json';
				   // $headers[] = 'Authorization: key=<YOUR-AUTH-KEY>';
					curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
					$result = curl_exec($ch);
					$result_data = json_decode($result, TRUE);
					echo '<pre>';
					print_r($result);
					print_r($result_data);
					echo '</pre>';
					die;
					
	}
	
   public function cron_notification()
    {
		$dispatches =  AssignDispatch::with('getDispatch')->with('getUser')->where('created_at', '<=', $date)
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
					/*** it is to send push notification  ***/
					$result_data = array();
					if(isset($expo_token) && $expo_token != '') {
					$body_text = 'New order received from '.$notification_text;
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
	}
		public function update_sidebar_help(Request $request)
		{
			$request->session()->put('sidebar_help', 'added');
			return 'done';
		}
		public function index(Request $request)
		{
			if(Auth::user()->can('viewMenu:Dashboard') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
			{
			$till_date = $start_time = '';
			$duration = $request->get('duration');
			$graph_date = $request->get('graph_date');
			if($graph_date != '' && $duration == 'custom') {
				$date_arr = explode("-",$graph_date);
				$start_time = $date_arr[0];
				$till_date = $date_arr[1];
			} else{}
			$emplyee_query =  User::query();
			$broker_query =  User::query();
			$completed_order_query =  Dispatch::query();
			$pending_order_query =  Dispatch::query();
			$total_customers_query =  Customer::query();
			$expense_graph_query =  DispatchTicket::query();
			$income_graph_query =  DispatchTicket::query();
			$profit_graph_query =  DispatchTicket::query();
			$pnding_from_cusomer_query =  DispatchTicket::query();
			$expense_except_fuel_query =  VehicleService::query();
			$expense_graph_query->where('status', 'completed');
			$income_graph_query->where('status', 'completed');
			$profit_graph_query->where('status', 'completed');
			$pnding_from_cusomer_query->where('status', 'completed');
			$group_by_p = '%m-%Y';
			$get_data_group_by_p = '%M %Y';
			$group_by_date_format = 'M-Y';
			if (!empty($duration) || !empty($start_time) || !empty($till_date)) {
				if($duration != 'custom') {
						if($duration == 'current_week' || $duration == 'current_month') {
							$group_by_p = '%d-%M';
							$get_data_group_by_p = '%d %M';
							$group_by_date_format = 'd';
						if($duration == 'current_week') {
							$monday = strtotime("last monday");
							$monday = date('w', $monday)==date('w') ? $monday+7*86400 : $monday;
							$sunday = strtotime(date("Y-m-d",$monday)." +6 days");
							$date_greter_then = date("Y-m-d",$monday);
							$date_less_then = date("Y-m-d",$sunday);
							} else if($duration == 'current_month') {
								$dt = date("Y-m-d");
								$date_greter_then = date("Y-m-01", strtotime($dt));
								$date_less_then = date("Y-m-t", strtotime($dt));
							} } else{}
				}
				else{
					if(!empty($start_time)) {
						$date_greter_then = date("Y-m-d", strtotime($start_time)); 
					}
					if(!empty($till_date)) {
						$date_less_then = date("Y-m-d", strtotime($till_date)); 
					}
				}
				if(!empty($date_greter_then)) {
					$emplyee_query->whereDate('created_at','>=',$date_greter_then);
					$broker_query->whereDate('created_at','>=',$date_greter_then);
					$total_customers_query->whereDate('created_at','>=',$date_greter_then);
					$completed_order_query->whereDate('completed_date','>=',$date_greter_then);
					$pending_order_query->whereDate('created_at','>=',$date_greter_then);
					$expense_graph_query->whereDate('created_at','>=',$date_greter_then);
					$income_graph_query->whereDate('created_at','>=',$date_greter_then);
					$pnding_from_cusomer_query->whereDate('created_at','>=',$date_greter_then);
					$profit_graph_query->whereDate('created_at','>=',$date_greter_then);
					$expense_except_fuel_query->whereDate('created_at','>=',$date_greter_then);
				}
				if(!empty($date_less_then)) {
					$emplyee_query->whereDate('created_at','<=',$date_less_then);
					$broker_query->whereDate('created_at','<=',$date_less_then);
					$total_customers_query->whereDate('created_at','<=',$date_less_then);
					$completed_order_query->whereDate('completed_date','<=',$date_less_then);
					$pending_order_query->whereDate('created_at','<=',$date_less_then);
					$expense_graph_query->whereDate('created_at','<=',$date_less_then);
					$income_graph_query->whereDate('created_at','<=',$date_less_then);
					$pnding_from_cusomer_query->whereDate('created_at','<=',$date_less_then);
					$profit_graph_query->whereDate('created_at','<=',$date_less_then);
					$expense_except_fuel_query->whereDate('created_at','<=',$date_less_then);
				}
			}
			$completed_order = $pending_order = 0;
			$completed_order_query->where('status', 'completed');
			/******* complete order div data */
			$complete_order_graph_data = array();
			$completed_order_query_data = $completed_order_query->selectRaw("count(id) as total_count,DATE_FORMAT(completed_date, '".$get_data_group_by_p."') as date")->groupBy(DB::raw("DATE_FORMAT(completed_date, '".$get_data_group_by_p."')"))->get()->toArray();
			
			if(isset($completed_order_query_data) && !empty($completed_order_query_data)) {
				foreach($completed_order_query_data as $graph_data_val) {
					$date = $graph_data_val['date'];
					if($group_by_date_format == 'd') {
						$month = date("M");
						$year = date("Y");
						$date = $date.' '.$year;
						$date = date('d M', strtotime($date));
					} else {
						$date = '1-'.$date;
						$date = date('M Y', strtotime($date));
					}
					
					$complete_order_graph_data['date'][] = $date;
					$specific_total_income = $graph_data_val['total_count'];
					$complete_order_graph_data['amount'][] = $specific_total_income;
				}
				$completed_order = array_sum($complete_order_graph_data['amount']);
			} else{}
			/******* end complete order div data */
			$pending_order_query->where('status', 'pending');
			$pending_order = 0;
			/******* pending order div data */
			$pending_order_graph_data = array();
			$pending_order_query_data = $pending_order_query->selectRaw("count(id) as total_count,DATE_FORMAT(created_at, '".$get_data_group_by_p."') as date")->groupBy(DB::raw("DATE_FORMAT(created_at, '".$get_data_group_by_p."')"))->get()->toArray();
			
			if(isset($pending_order_query_data) && !empty($pending_order_query_data)) {
				foreach($pending_order_query_data as $graph_data_val) {
					$date = $graph_data_val['date'];
					if($group_by_date_format == 'd') {
						$month = date("M");
						$year = date("Y");
						$date = $date.' '.$year;
						$date = date('d M', strtotime($date));
					} else {
						$date = '1-'.$date;
						$date = date('M Y', strtotime($date));
					}
					
					$pending_order_graph_data['date'][] = $date;
					$pending_order_graph_data['amount'][] = $graph_data_val['total_count'];
				}
				$pending_order = array_sum($pending_order_graph_data['amount']);
			} else{}
			/******* end pending order div data */
			/******* employee div data */
			$total_employee = $total_broker = $total_customers = 0;
			$employee_graph_data = $broker_graph_data =  $customer_graph_data = array();
			$total_employee_data = $emplyee_query->where('type', 'employee')->selectRaw("count(id) as total_count,DATE_FORMAT(created_at, '".$get_data_group_by_p."') as date")->groupBy(DB::raw("DATE_FORMAT(created_at, '".$get_data_group_by_p."')"))->get()->toArray();
			if(isset($total_employee_data) && !empty($total_employee_data)) {
				foreach($total_employee_data as $employee_data) {
					$date = $employee_data['date'];
					if($group_by_date_format == 'd') {
						$month = date("M");
						$year = date("Y");
						$date = $date.' '.$year;
						$date = date('d M', strtotime($date));
					} else {
						$date = '1-'.$date;
						$date = date('M Y', strtotime($date));
					}
					
					$employee_graph_data['date'][] = $date;
					$employee_graph_data['amount'][] = $employee_data['total_count'];
				}
				$total_employee = array_sum($employee_graph_data['amount']);
			} else{}
			/******* end employee div data */
			/******* broker div data */
			$total_broker_data = $broker_query->where('type', 'broker')->selectRaw("count(id) as total_count,DATE_FORMAT(created_at, '".$get_data_group_by_p."') as date")->groupBy(DB::raw("DATE_FORMAT(created_at, '".$get_data_group_by_p."')"))->get()->toArray();
			if(isset($total_broker_data) && !empty($total_broker_data)) {
				foreach($total_broker_data as $broker_data) {
					$date = $broker_data['date'];
					if($group_by_date_format == 'd') {
						$month = date("M");
						$year = date("Y");
						$date = $date.' '.$year;
						$date = date('d M', strtotime($date));
					} else {
						$date = '1-'.$date;
						$date = date('M Y', strtotime($date));
					}
					
					$broker_graph_data['date'][] = $date;
					$broker_graph_data['amount'][] = $broker_data['total_count'];
				}
				$total_broker = array_sum($broker_graph_data['amount']);
			} else{}
			/******* end broker div data */
			/******* customer div data */
			$total_customer_data = $total_customers_query->selectRaw("count(id) as total_count,DATE_FORMAT(created_at, '".$get_data_group_by_p."') as date")->groupBy(DB::raw("DATE_FORMAT(created_at, '".$get_data_group_by_p."')"))->get()->toArray();
			if(isset($total_customer_data) && !empty($total_customer_data)) {
				foreach($total_customer_data as $customer_data) {
					$date = $customer_data['date'];
					if($group_by_date_format == 'd') {
						$month = date("M");
						$year = date("Y");
						$date = $date.' '.$year;
						$date = date('d M', strtotime($date));
					} else {
						$date = '1-'.$date;
						$date = date('M Y', strtotime($date));
					}
					
					$customer_graph_data['date'][] = $date;
					$customer_graph_data['amount'][] = $customer_data['total_count'];
				}
				$total_customers = array_sum($customer_graph_data['amount']);
			} else{}
			/******* end customer div data */
		$income_graph_query_data = $income_graph_query->selectRaw("sum(income) as total_income,DATE_FORMAT(created_at, '".$group_by_p."') as date")->groupBy(DB::raw("DATE_FORMAT(created_at, '".$group_by_p."')"))->get()->toArray();
		$pnding_from_cusomer_query_data = $pnding_from_cusomer_query->where('paid_to_company','=','pending')->selectRaw("sum(income) as total_income,DATE_FORMAT(created_at, '".$group_by_p."') as date")->groupBy(DB::raw("DATE_FORMAT(created_at, '".$group_by_p."')"))->get()->toArray();
		
		$expense_graph_query_data = $expense_graph_query->selectRaw("sum(expense) as total_income,sum(fuel_amount_paid) as total_fuel_amount_paid,DATE_FORMAT(created_at, '".$group_by_p."') as date")->groupBy(DB::raw("DATE_FORMAT(created_at, '".$group_by_p."')"))->get()->toArray();
		
		$profit_graph_query_data = $profit_graph_query->selectRaw("sum(profit) as total_income,DATE_FORMAT(created_at, '".$group_by_p."') as date")->groupBy(DB::raw("DATE_FORMAT(created_at, '".$group_by_p."')"))->get()->toArray();
		
		$expense_except_fuel_query_data = $expense_except_fuel_query->selectRaw("sum(expense_amount) as total_expense,DATE_FORMAT(on_date, '".$group_by_p."') as date")->groupBy(DB::raw("DATE_FORMAT(on_date, '".$group_by_p."')"))->get()->toArray();
		
		$income_arr_level = $expense_arr_level = $profit_arr_level = $expense_except_fuel_arr_level = $pending_cust_amount_arr = $total_income_arr = $total_expense_arr = $total_profit_arr = $expense_except_fuel_arr = array();
		$expense_chart_arr = $income_chart_arr = array();
		$income_chart_arr['date'] = array();
		$income_arr_level_json = $income_arr_data_json = $expense_arr_level_json = $expense_arr_data_json = $profit_arr_level_json = $profit_arr_data_json = '';
		if(isset($pnding_from_cusomer_query_data) && !empty($pnding_from_cusomer_query_data)) {
			foreach($pnding_from_cusomer_query_data as $cusomer_query_data) {
				$customer_total_pending = $cusomer_query_data['total_income'];
				$pending_cust_amount_arr[] = $customer_total_pending;
			}
		}
		if(isset($income_graph_query_data) && !empty($income_graph_query_data)) {
			foreach($income_graph_query_data as $graph_data_val) {
				$date = $graph_data_val['date'];
				if($group_by_date_format == 'd') {
					$month = date("M");
					$year = date("Y");
					$date = $date.'-'.$year;
				} else {
					$date = '1-'.$date;
					$date = date('M-Y', strtotime($date));
				}
				$specific_total_income = $graph_data_val['total_income'];
				$total_income_arr[] = $specific_total_income;
				$income_chart_arr['data_with_date'][$date] = $specific_total_income;
				$income_chart_arr['date'][] = $date;
			}
		}
		
		if(isset($profit_graph_query_data) && !empty($profit_graph_query_data)) {
			foreach($profit_graph_query_data as $graph_p_data_val) {
				$p_date = $graph_p_data_val['date'];
				if($group_by_date_format == 'd') {
					$month = date("M");
					$year = date("Y");
					$p_date = $p_date.'-'.$year;
				} else {
					$p_date = '1-'.$p_date;
					$p_date = date('M-Y', strtotime($p_date));
				}
				
				$profit_arr_level['date'][] = $p_date;
				$specific_p_total_income = $graph_p_data_val['total_income'];
				$profit_arr_level['amount'][] = $specific_p_total_income;
				$total_profit_arr[] = $specific_p_total_income;
			}
		}
		if(isset($expense_graph_query_data) && !empty($expense_graph_query_data)) {
			foreach($expense_graph_query_data as $graph_e_data_val) {
				$e_date = $graph_e_data_val['date'];
				if($group_by_date_format == 'd') {
					$month = date("M");
					$year = date("Y");
					$e_date = $e_date.'-'.$year;
				} else {
					$e_date = '1-'.$e_date;
					$e_date = date('M-Y', strtotime($e_date));
				}
				$specific_e_total_income = $graph_e_data_val['total_income'];
				$total_fuel_amount_paid = $graph_e_data_val['total_fuel_amount_paid'];
				$specific_e_total_income = $specific_e_total_income + $total_fuel_amount_paid;
				$total_expense_arr[] = $specific_e_total_income;
				$expense_chart_arr['data_with_date'][$e_date][] = $specific_e_total_income;
			}
		}
		if(isset($expense_except_fuel_query_data) && !empty($expense_except_fuel_query_data)) {
			foreach($expense_except_fuel_query_data as $graph_p_data_val) {
				$p_date = $graph_p_data_val['date'];
				if($group_by_date_format == 'd') {
					$month = date("M");
					$year = date("Y");
					$p_date = $p_date.'-'.$year;
				} else {
					$p_date = '1-'.$p_date;
					$p_date = date('M-Y', strtotime($p_date));
				}
				$specific_expense_except_fuel_total_income = $graph_p_data_val['total_expense'];
				$expense_except_fuel_arr[] = $specific_expense_except_fuel_total_income;
				$expense_except_fuel_arr_level['data_with_date'][$p_date][] = $specific_expense_except_fuel_total_income;
			}
		}
		$total_income = array_sum($total_income_arr);
		$total_expense = array_sum($total_expense_arr) + array_sum($expense_except_fuel_arr);
		$total_profit = array_sum($total_profit_arr);
		
		/******* it is required as we have other expenses then fuel  ********/
		$total_profit = $total_income - $total_expense;
		/******* end it is required as we have other expenses then fuel  ********/
		$pending_from_cust = array_sum($pending_cust_amount_arr);
		
		if(isset($profit_arr_level) && !empty($profit_arr_level)) {
			$profit_arr_level_json = json_encode($profit_arr_level['date']);	
			$profit_arr_data_json = json_encode($profit_arr_level['amount']);
		}
		/********* it is to calulate dispatch expense and other expense  ********/
		$ticket_expense_arr = $expense_other_arr = array();
		if(isset($expense_chart_arr['data_with_date']) && !empty($expense_chart_arr['data_with_date'])) {
			foreach($expense_chart_arr['data_with_date'] as $date_key => $date_val_arr) {
				$ticket_expense_arr[$date_key] = array_sum($expense_chart_arr['data_with_date'][$date_key]);
			}
		}
		if(isset($expense_except_fuel_arr_level['data_with_date']) && !empty($expense_except_fuel_arr_level['data_with_date'])) {
			foreach($expense_except_fuel_arr_level['data_with_date'] as $date_key => $date_val_arr) {
				$expense_other_arr[$date_key] = array_sum($expense_except_fuel_arr_level['data_with_date'][$date_key]);
			}
		}
		$all_expense = array_merge_recursive($ticket_expense_arr,$expense_other_arr);
		$expense_chart_arr['date'] = array();
		if(isset($all_expense) && !empty($all_expense)) {
			foreach($all_expense as $date_k => $amount) {
				$current_val = $all_expense[$date_k];
				$expense_amount = (is_array($current_val)) ? array_sum($all_expense[$date_k]) : $current_val;
				$expense_chart_arr['date_amount'][$date_k] = $expense_amount;
				$expense_chart_arr['date'][] = $date_k;
			}	
		}
		/******* it is to make date same for income and expense **********/
		$all_dates = array_merge_recursive($expense_chart_arr['date'],$income_chart_arr['date']);
		$unique_dates = array_unique($all_dates);
		foreach($unique_dates as $date_vals) {
			$expense_amount = $income_amount = 0;
			$expense_arr = $expense_chart_arr['date_amount'];
			$income_arr = $income_chart_arr['data_with_date'];
			if (array_key_exists($date_vals,$expense_arr)) {
				$expense_amount = $expense_arr[$date_vals];
			}
			if (array_key_exists($date_vals,$income_arr)) {
				$income_amount = $income_arr[$date_vals];
			}
			$expense_arr_level['date'][] = $date_vals;
			$expense_arr_level['amount'][] = $expense_amount;
			$income_arr_level['date'][] = $date_vals;
			$income_arr_level['amount'][] = $income_amount;
			
		}
		/******* end it is to make date same for income and expense **********/
		if(isset($expense_arr_level) && !empty($expense_arr_level)) {
			$expense_arr_level_json = json_encode($expense_arr_level['date']);	
			$expense_arr_data_json = json_encode($expense_arr_level['amount']);
		}
		if(isset($income_arr_level) && !empty($income_arr_level)) {
			$income_arr_level_json = json_encode($income_arr_level['date']);	
			$income_arr_data_json = json_encode($income_arr_level['amount']);
		}	
		/********* end it is to calulate dispatch expense and other expense  ********/		
		/*********complete order date array */
		$complete_order_json = $pending_order_json = $employee_graph_data_json = array();
		$broker_graph_data_json = $customer_graph_data_json = array();
		if(isset($complete_order_graph_data) && !empty($complete_order_graph_data)) {
			$complete_order_json['level'] = json_encode($complete_order_graph_data['date']);	
			$complete_order_json['data']  = json_encode($complete_order_graph_data['amount']);
		}
		/********* end complete order date array */
		/*********pending order date array */
		if(isset($pending_order_graph_data) && !empty($pending_order_graph_data)) {
			$pending_order_json['level'] = json_encode($pending_order_graph_data['date']);	
			$pending_order_json['data']  = json_encode($pending_order_graph_data['amount']);
		}
		/********* end complete order date array */	
		/*********Employee date array */
		if(isset($employee_graph_data) && !empty($employee_graph_data)) {
			$employee_graph_data_json['level'] = json_encode($employee_graph_data['date']);	
			$employee_graph_data_json['data']  = json_encode($employee_graph_data['amount']);
		}
		/********* end employee date array */
		/*********Broker date array */
		if(isset($broker_graph_data) && !empty($broker_graph_data)) {
			$broker_graph_data_json['level'] = json_encode($broker_graph_data['date']);	
			$broker_graph_data_json['data']  = json_encode($broker_graph_data['amount']);
		}
		/********* end broker date array */	
		/*********Broker date array */
		if(isset($customer_graph_data) && !empty($customer_graph_data)) {
			$customer_graph_data_json['level'] = json_encode($customer_graph_data['date']);	
			$customer_graph_data_json['data']  = json_encode($customer_graph_data['amount']);
		}
		/********* end customer date array */	
			$total_orders = $completed_order + $pending_order;
			return view('home',compact('customer_graph_data_json','broker_graph_data_json','employee_graph_data_json','pending_order_json','complete_order_json','pending_from_cust','total_employee','total_broker','completed_order','pending_order','total_customers','total_orders','total_expense','total_income','total_profit','income_arr_level_json','income_arr_data_json','expense_arr_level_json','expense_arr_data_json','profit_arr_level_json','profit_arr_data_json'));
		}  else
		{
			return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
		}
	}
	public function noaccess(Request $request) {
		return view('noaccess');
	}
}