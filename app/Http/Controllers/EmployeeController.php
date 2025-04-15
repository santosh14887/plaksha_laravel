<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleAssignmentHistory;
use Auth;
use Validator;
use Session;
use DB;
use Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmployeeRegisterMail;
use Twilio\Rest\Client;
use Config;

use App\Models\CredsDetail;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;
use QuickBooksOnline\API\Facades\Customer as quickbookCustomer; 
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Vendor as quickbookVendor;
class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
            if(Auth::user()->can('viewMenu:Employee') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            $perPage = 15;
            $keyword = $request->get('search');
                if (!empty($keyword)) {
                    $query =  User::query()->with('getVehicle');
                    $employees = $query->where('type', '=','employee')->where(function($query) use ($keyword) {
                        $query->where('first_name', 'LIKE', "%$keyword%")
                        ->orWhere('last_name', 'LIKE', "%$keyword%")
                        ->orWhere('name', 'LIKE', "%$keyword%")
                        ->orWhere('address', 'LIKE', "%$keyword%")
                        ->orWhere('phone', 'LIKE', "%$keyword%")
                        ->orWhere('email', 'LIKE', "%$keyword%")
                        ->orWhere('zip_code', 'LIKE', "%$keyword%")
                        ->orWhere('vehicle_id', 'LIKE', "%$keyword%")
                        ->orWhere('status', 'LIKE', "%$keyword%")
                        ->orWhere('license_number', 'LIKE', "%$keyword%")
                        ->orWhere('company_corporation_name', 'LIKE', "%$keyword%")
                        ->orWhere('hourly_rate', 'LIKE', "%$keyword%")
                        ->orWhere('hst', 'LIKE', "%$keyword%");					
                        })->latest()->orderBy('id', 'desc')->paginate($perPage);
                } else {
                    $employees = User::where(['type' => 'employee'])->latest()->paginate($perPage);
                }
            
            return view('employees.index',compact('employees'));
        }
        else
        {
            return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
        }
    }
	public function all_transaction(Request $request,$id) {
        if(Auth::user()->can('viewMenu:Transaction') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
		$perPage = 15;
        $keyword = $request->get('search');
			if (!empty($keyword)) {
                $query =  Transaction::query()->with('getUser');
                $transactions = $query->where(function($query) use ($keyword) {
					$query->where('type', '=','employee');
					})->orWhereHas('getVehicle',function ($query)use($keyword)
                    {
                        $query->where('vehicles.vehicle_number','Like','%'.$keyword.'%');
                    })->where(function($query) use ($keyword) {
					$query->where('first_name', 'LIKE', "%$keyword%")
                    ->orWhere('last_name', 'LIKE', "%$keyword%")
                    ->orWhere('address', 'LIKE', "%$keyword%")
                    ->orWhere('phone', 'LIKE', "%$keyword%")
                    ->orWhere('email', 'LIKE', "%$keyword%")
                    ->orWhere('zip_code', 'LIKE', "%$keyword%")
                    ->orWhere('vehicle_id', 'LIKE', "%$keyword%")
                    ->orWhere('status', 'LIKE', "%$keyword%")
                    ->orWhere('license_number', 'LIKE', "%$keyword%")
                    ->orWhere('company_corporation_name', 'LIKE', "%$keyword%")
                    ->orWhere('hourly_rate', 'LIKE', "%$keyword%")
                    //->orWhere('load_per', 'LIKE', "%$keyword%")
                    ->orWhere('total_income', '=', "%$keyword%")
                    ->orWhere('hst', 'LIKE', "%$keyword%");					
					})->latest()->orderBy('id', 'desc')->paginate($perPage);
			} else {
				$transactions = User::where(['user_id' => $id])->latest()->paginate($perPage);
			}
			return view('transactions.index',compact('transactions'));
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
        if(Auth::user()->can('viewMenu:ActionEmployee') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            $employee_vehicle_id = 0;
            $used_vehicle = User::where('vehicle_id','!=',$employee_vehicle_id)->get()->pluck('vehicle_id')->toArray();
            $vehicle = Vehicle::whereNotIn('id', $used_vehicle)->get()->pluck('vehicle_number','id');
            return view('employees.create',compact('vehicle'));
        }
        else
        {
            return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
        }
    }
	public function get_quickbook_creds() {
		$res_err = array();
		$quicbook_creds =  CredsDetail::where('creds_for', '=', 'quickbook')->where('use_quickbook_api', '=', 'active')->get()->toArray();
		if(isset($quicbook_creds) && !empty($quicbook_creds)) {
		$quicbook_creds = $quicbook_creds['0'];
		$row_id = $quicbook_creds['id'];
        try {
            $oauth2LoginHelper = new OAuth2LoginHelper($quicbook_creds['client_id'],$quicbook_creds['client_secret']);
            /***** Update the OAuth2Token ******/
            $accessTokenObj = $oauth2LoginHelper->refreshAccessTokenWithRefreshToken($quicbook_creds['refresh_token']);
            $accessTokenValue = $accessTokenObj->getAccessToken();
            $refreshTokenValue = $accessTokenObj->getRefreshToken();
            $creds_details = CredsDetail::find($row_id);
            $creds_details->access_token = $accessTokenValue;
            $creds_details->refresh_token = $refreshTokenValue;
            $creds_details->save();
            /***** end Update the OAuth2Token ******/
            $res_err = array(
            'auth_mode' => $quicbook_creds['auth_mode'],
            'ClientID' => $quicbook_creds['client_id'],
            'ClientSecret' => $quicbook_creds['client_secret'],
            'RedirectURI' => $quicbook_creds['redirect_uri'],
            'accessTokenKey' => $accessTokenValue,
            'refreshTokenKey' => $refreshTokenValue,
            'QBORealmID' => $quicbook_creds['realm_id'],
            'baseUrl' => $quicbook_creds['type']
                );
            } catch (\Exception $e){
                    $res_err = array(
                        'api_error' => $e->getMessage(),
                            );
                }
		} else {}
		return json_encode($res_err);

	}
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	 public function admin_change_password(Request $request)
    {
		$data  = $request->all();
		$pwd = $request->password;
        $password = Hash::make($pwd);
		$id = Auth::user()->id;
        $user = User::find($id);
		$user->password = $password;
		$user->password_string = $pwd;
		$user->save();
	}
    public function update_vehicle_assign_table($user_id) {
        $user_arr = User::where('id','=',$user_id)->with('getVehicle')->get()->toArray();
        if(isset($user_arr) && !empty($user_arr)) {
            $start_time = date('Y-m-d h:i:s');
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
                    $vehicle_assignment->save();
                } else {}
                /******* end get vehicle number if employee selected */
            }
            
        } else {}
    }
    public function store(Request $request)
    {
        if(Auth::user()->can('viewMenu:ActionEmployee') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            $data  = $request->all();
            $user = new User;
            // validate
            $rules = [
                'first_name'  => 'required',
                'last_name'  => 'required',
                'email'  => 'required|email|unique:users',
                'phone'  => 'required|unique:users',
                'hst'  => 'required',
                'zip_code'  => 'required',
            // 'vehicle_id'  => 'required',
              //  'address'  => 'required',
                'status'  => 'required',
                'hourly_rate'  => 'sometimes|nullable|numeric|regex:/^\d*(\.\d{2})?$/',
				'street_line'  => 'required',
                'city'  => 'required',
                'country'  => 'required',
                'country_devision_code'  => 'required',
                'company_corporation_name'  => 'required',
            // 'load_per'  => 'sometimes|nullable|numeric|regex:/^\d*(\.\d{2})?$/',
            ];
            
            $message = [
                'first_name.required' => 'First name must be added',
                'last_name.required' => 'Last name must be added',
                'email.required' => 'Email must be added',
                'phone.required' => 'Phone must be added',
                'hst.required' => 'HST must be added',
                'zip_code.required' => 'Zip code must be added',
				'street_line.required' => 'The Street or Line must be added',
                'city.required' => 'The City must be added',
                'country.required' => 'The Country must be added',
                'country_devision_code.required' => 'The Country devision code must be added',
                'company_corporation_name.required' => 'The Company corporation must be added',
            // 'vehicle_id.required' => 'Unit number must be added',
             //   'address.required' => 'Address must be added',
            ];
            if(isset($request->vehicle_id) && null !== $request->vehicle_id && $request->vehicle_id > 0) {
                $rules['vehicle_id'] = 'unique:users';
                $message['vehicle_id.unique'] = 'Unit number already assign';
            }
            $validator = Validator::make($data, $rules, $message);

            if ($validator->fails()) {
                return back()->withInput()
                    ->withErrors($validator);
            } else {
                // store
                $email = $request->email;
                
            $password_string = $password =  $request->password;
                if($password != '') {
                    $password = Hash::make($password);
                } else{}
                $first_name = $request->first_name;
                $last_name = $request->last_name;
                $name = $first_name.' '.$last_name;
                $mobile_no = $request->phone;
                $country_code = $request->country_code;
				/******* it is for quickbook  *********/
				$quickbook_res = '';
				$quickbook_id = 0;
				$address =  '';
				$street_line = $request->street_line;
				$city = $request->city;
				$country = $request->country;
				$country_devision_code = $request->country_devision_code;
				$postal_code = $zip_code = $request->zip_code;
				$address = $street_line.','.$city.','.$country.'('.$country_devision_code.')'.$postal_code;
				$company_name = $request->company_corporation_name;
				$quickbook_creds = $this->get_quickbook_creds();
				$quickbook_creds_arr = json_decode($quickbook_creds,true);
                if(isset($quickbook_creds_arr) && !empty($quickbook_creds_arr) && isset($quickbook_creds_arr['api_error'])) {
                    $message = $quickbook_creds_arr['api_error'];
                    $validator->getMessageBag()->add('first_name', $message);
                    return back()->withInput()
					->withErrors($validator);
                } else {}
				/*******end it is for quickbook  *********/
                /****send mail if username and password exist **********/
                if($password != '') {
                    /***send sms to employee *****************/
                    if($mobile_no != '') {
                        //$country_code = Config::get('constants.TWILIO_Country_Code');
                        $app_constant_name = Config::get('constants.APP_CONSTANT_NAME');
                        $receiverNumber = $country_code.$mobile_no;
                        $message = "Hello ".$name."\n";
                            $message .= "You have been registered to ".$app_constant_name.". Kindly use below details to login to app:\n";
                            $message .= "Id : ".$email."\n";
                            $message .= "password : ".$password_string."\n";
                        
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
                            $user->register_sms_sent = 'yes';
                
                        }  catch (\Exception $e){
                            if($e->getCode() == 21211)
                            {      
                                $message = $e->getMessage();
                                $show_msg = $receiverNumber. ' is not a valid number';
                                $validator->getMessageBag()->add('phone', $show_msg);
                                return back()->withInput()
                                ->withErrors($validator);
                            }


                        }
                    }
                    /***end send sms to employee *****************/
                    $mailData = [
                    'title' => 'JapGobind Transport Register',
                    'email' => $email,
                    'name' => $name,
                    'password_string' => $password_string,
                    ];
                    try {
                        $mail_res = Mail::to($email)->send(new EmployeeRegisterMail($mailData));
                        $user->register_email_sent = 'yes';	
                    } catch (\Exception $e){
                    }
                } else{}
                /****end send mail if username and password exist **********/
                /*******  add employee as vendor **************/
				if(isset($quickbook_creds_arr) && !empty($quickbook_creds_arr) && $company_name != '' && $company_name != 'NA') {
					$dataService = DataService::Configure($quickbook_creds_arr);
					$dataService->throwExceptionOnError(true);
					//Add a new Vendor
					$customer_hst = '';
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
                        $validator->getMessageBag()->add('first_name', $message);
                        return back()->withInput()
                        ->withErrors($validator);
                     
                    }
				} else {}
                /*******  end add employee as vendor **************/
				$user->quickbook_id = $quickbook_id;
                $user->quickbook_res = $quickbook_res;
				$user->street_line = $street_line;
                $user->city = $city;
                $user->country = $country;
                $user->country_devision_code = $country_devision_code;
                $user->first_name = $first_name;
                $user->last_name = $last_name;
                $user->name = $name;
                $user->email = $email;
                $user->password = $password;
                $user->password_string = $password_string;
                $user->phone = $mobile_no;
                $user->hst = $request->hst;
                $user->zip_code = $zip_code;
                $user->country_code = $country_code;
                $user->vehicle_id = ($request->vehicle_id > 0) ? $request->vehicle_id : 0;
                $user->address = $address;
                $user->license_number = $request->license_number;
                $user->company_corporation_name = $request->company_corporation_name;
                $user->status = $request->status;
                $user->type = 'employee';
                $user->hourly_rate = ($request->hourly_rate > 0) ? $request->hourly_rate : 0;
                // $user->load_per = $request->load_per;
                $user->save();
                $user_id = $user->id;
                $this->update_vehicle_assign_table($user_id);
                // redirect
                Session::flash('message', 'Successfully created Employee!');
                return redirect()->route("employees.index");
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
        if(Auth::user()->can('viewMenu:Employee') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            $employees = User::find($id);
            if(null !== $employees) {
                return view('employees.show', compact('employees'));
            } else {
                Session::flash('message', 'Employee does not exist!');
                return redirect()->route("employees.index");
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
        if(Auth::user()->can('viewMenu:ActionEmployee') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            $employees = User::find($id);
            $employee_vehicle_id = $employees->vehicle_id;
            $used_vehicle = User::where('vehicle_id','!=',$employee_vehicle_id)->get()->pluck('vehicle_id','vehicle_id')->toArray();
            $vehicle = Vehicle::whereNotIn('id', $used_vehicle)->get()->pluck('vehicle_number','id');
            return view('employees.edit',compact('employees','vehicle'));
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
        if(Auth::user()->can('viewMenu:ActionEmployee') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            $data  = $request->all();
            // validate
            $rules = [
                'first_name'  => 'required',
                'last_name'  => 'required',
                'email'  => "required|email|unique:users,email,$id",
                'phone'  => "required|unique:users,phone,$id",
                'hst'  => 'required',
                'zip_code'  => 'required',
            // 'vehicle_id'  => 'required',
             //   'address'  => 'required',
                'status'  => 'required',
                'hourly_rate'  => 'sometimes|nullable|numeric|regex:/^\d*(\.\d{2})?$/',
				'street_line'  => 'required',
                'city'  => 'required',
                'country'  => 'required',
                'country_devision_code'  => 'required',
                'company_corporation_name'  => 'required',
                //'load_per'  => 'sometimes|nullable|numeric|regex:/^\d*(\.\d{2})?$/',
            ];
            $message = [
                'first_name.required' => 'First name must be added',
                'last_name.required' => 'Last name must be added',
                'email.required' => 'Email must be added',
                'phone.required' => 'Phone must be added',
                'hst.required' => 'HST must be added',
                'zip_code.required' => 'Zip code must be added',
				'street_line.required' => 'The Street or Line must be added',
                'city.required' => 'The City must be added',
                'country.required' => 'The Country must be added',
                'country_devision_code.required' => 'The Country devision code must be added',
                'company_corporation_name.required' => 'The Company corporation must be added',
            // 'vehicle_id.required' => 'Unit number must be added',
             //   'address.required' => 'Address must be added',
            ];
            if(isset($request->vehicle_id) && null !== $request->vehicle_id && $request->vehicle_id > 0) {
                $rules['vehicle_id'] = "unique:users,vehicle_id,$id";
                $message['vehicle_id.unique'] = 'Unit number already assign';
            }
            $validator = Validator::make($data, $rules, $message);
            if ($validator->fails()) {
                return back()->withInput()
                    ->withErrors($validator);
            } else {
                // store
                $user = User::find($id);
				$quickbook_id = $user->quickbook_id;
                $registered_vehicle_id = $user->vehicle_id;
                $register_email_sent = $user->register_email_sent;
                $register_sms_sent = $user->register_sms_sent;
                $existing_email = $user->email;
                $password_string = $password =  $request->password;
                $email = $request->email;
                
                if($password != '') {
                    $password = Hash::make($password);
                } else{}
                $current_vehicle_id = $request->vehicle_id;
                $first_name = $request->first_name;
                $last_name = $request->last_name;
                $name = $first_name.' '.$last_name;
                $country_code = $request->country_code;
				/******* it is for quickbook  *********/
				$quickbook_res = '';
				$address =  '';
				$street_line = $request->street_line;
				$city = $request->city;
				$country = $request->country;
				$country_devision_code = $request->country_devision_code;
				$postal_code = $zip_code = $request->zip_code;
				$address = $street_line.', '.$city.', '.$country.'('.$country_devision_code.'), '.$postal_code;
				$company_name = $request->company_corporation_name;
				$quickbook_creds = $this->get_quickbook_creds();
				$quickbook_creds_arr = json_decode($quickbook_creds,true);
                if(isset($quickbook_creds_arr) && !empty($quickbook_creds_arr) && isset($quickbook_creds_arr['api_error'])) {
                    $message = $quickbook_creds_arr['api_error'];
                    $validator->getMessageBag()->add('first_name', $message);
                    return back()->withInput()
					->withErrors($validator);
                } else {}
				/*******end it is for quickbook  *********/
                /***send sms to employee *****************/
                $mobile_no = $request->phone;
                if($mobile_no != '' && ($register_sms_sent == 'no' || $existing_email != $email)) {
                    //$country_code = Config::get('constants.TWILIO_Country_Code');
                    $receiverNumber = $country_code.$mobile_no;
                        $message = "Hello ".$name."\n";
                            $message .= "You have been registered to JapGobindTransport. Kindly use below details to login to app:\n";
                            $message .= "Id : ".$email."\n";
                            $message .= "password : ".$password_string."\n";
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
                            $user->register_sms_sent = 'yes';
                
                        }  catch (\Exception $e){
                            if($e->getCode() == 21211)
                            {      
                                $message = $e->getMessage();
                                $show_msg = $receiverNumber. ' is not a valid number';
                                $validator->getMessageBag()->add('phone', $show_msg);
                                return back()->withInput()
                                ->withErrors($validator);
                            }
                        }
                } else{}
                /***end send sms to employee *****************/
                /****send mail if username and password exist **********/
                if($password != '' && ($register_email_sent == 'no' || $existing_email != $email)) {
                    $mailData = [
                    'title' => 'JapGobind Transport Register',
                    'email' => $email,
                    'name' => $name,
                    'password_string' => $password_string,
                    ];
                    try {
                        $mail_res = Mail::to($email)->send(new EmployeeRegisterMail($mailData));	
                        $user->register_email_sent = 'yes';
                    } catch (\Exception $e){
                    }
                } else{}
                /****end send mail if username and password exist **********/
                /*******  add employee as vendor **************/
				if(isset($quickbook_creds_arr) && !empty($quickbook_creds_arr)) {
					$dataService = DataService::Configure($quickbook_creds_arr);
					$dataService->throwExceptionOnError(true);
					//Add a new Vendor
					/*********if vendor added then update else add  ********/
					if($quickbook_id > 0) {
						/********* end if vendor added then update else add  ********/
						$customer_hst = '';
						try {
							$vendor = $dataService->FindbyId('vendor', $quickbook_id);
							$theResourceObj = quickbookVendor::update($vendor , [
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
							$validator->getMessageBag()->add('first_name', $message);
							return back()->withInput()
							->withErrors($validator);
						 
						}
					} else if($company_name != '' && $company_name != 'NA') {
						$customer_hst = '';
						$dataService = DataService::Configure($quickbook_creds_arr);
						$dataService->throwExceptionOnError(true);
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
							$validator->getMessageBag()->add('first_name', $message);
							return back()->withInput()
							->withErrors($validator);
						 
						}
					} else {}
				} else {}
                /*******  end add employee as vendor **************/
				$user->quickbook_id = $quickbook_id;
                $user->quickbook_res = $quickbook_res;
				$user->street_line = $street_line;
                $user->city = $city;
                $user->country = $country;
                $user->country_devision_code = $country_devision_code;
                $user->first_name = $first_name;
                $user->last_name = $last_name;
                $user->name = $name;
                $user->email = $email;
                $user->password = $password;
                $user->password_string = $password_string;
                $user->phone = $mobile_no;
                $user->hst = $request->hst;
                $user->country_code = $country_code;
                $user->zip_code = $request->zip_code;
                $user->vehicle_id = ($request->vehicle_id > 0) ? $request->vehicle_id : 0;
                $user->address = $address;
                $user->license_number = $request->license_number;
                $user->company_corporation_name = $request->company_corporation_name;
                $user->status = $request->status;
                $user->type = 'employee';
                $user->hourly_rate = ($request->hourly_rate > 0) ? $request->hourly_rate : 0;
            // $user->load_per = $request->load_per;
                $user->save();
                if($current_vehicle_id != $registered_vehicle_id) {
                    $end_time = date('Y-m-d h:i:s');
                    $update_data = array('end_time' => $end_time);
                    DB::table('vehicle_assignment_histories')
                    ->where('user_vehicle_type', '=', 'permanent')
                    ->where('end_time', '=' , null)
                    ->update($update_data);
                    $this->update_vehicle_assign_table($id);
                }
                
                // redirect
                Session::flash('message', 'Successfully updated Employee!');
                return redirect()->route("employees.index");
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
        if(Auth::user()->can('viewMenu:ActionEmployee') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            $user = User::find($id);
            $user->delete();
            // redirect
            Session::flash('message', 'Successfully deleted employee!');
			return redirect()->route("employees.index");
        }
		else
		{
			return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
		}
    }
}