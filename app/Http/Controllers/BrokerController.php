<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Validator;
use Session;
use DB;
use Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\BrokerRegisterMail;
use Twilio\Rest\Client;
use Config;
class BrokerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(Auth::user()->can('viewMenu:Broker') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            $perPage = 15;
            $keyword = $request->get('search');
                if (!empty($keyword)) {
                    $brokers = User::where(function($query) use ($keyword) {
                        $query->where('type', '=','broker');
                        })->where(function($query) use ($keyword) {
                        $query->where('first_name', 'LIKE', "%$keyword%")
                        ->orWhere('last_name', 'LIKE', "%$keyword%")
                        ->orWhere('address', 'LIKE', "%$keyword%")
                        ->orWhere('phone', 'LIKE', "%$keyword%")
                        ->orWhere('email', 'LIKE', "%$keyword%")
                        ->orWhere('zip_code', 'LIKE', "%$keyword%")
                        ->orWhere('available_unit', 'LIKE', "%$keyword%")
                        ->orWhere('wsib_quarterly', 'LIKE', "%$keyword%")
                        ->orWhere('incorporation_name', 'LIKE', "%$keyword%")
                        ->orWhere('hourly_rate', 'LIKE', "%$keyword%")
                        ->orWhere('status', 'LIKE', "%$keyword%")
                        ->orWhere('hst', 'LIKE', "%$keyword%");					
                        })->latest()->orderBy('id', 'desc')->paginate($perPage);
                } else {
                    $brokers = User::where(['type' => 'broker'])->latest()->paginate($perPage);
                }
            
            return view('brokers.index',compact('brokers'));
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
        if(Auth::user()->can('viewMenu:ActionBroker') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            return view('brokers.create');
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
        if(Auth::user()->can('viewMenu:ActionBroker') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            $data  = $request->all();
            $user = new User;
            // validate
            $rules = [
                'first_name'  => 'required',
                'last_name'  => 'required',
                'email'  => 'required|email|unique:users',
                'phone'  => 'required|unique:users',
                'available_unit'  => 'required|numeric',
                'address'  => 'required',
                'hourly_rate'  => 'sometimes|nullable|numeric|regex:/^\d*(\.\d{2})?$/',
            ];
            $message = [
                'first_name.required' => 'First name must be added',
                'last_name.required' => 'Last name must be added',
                'email.required' => 'Email must be added',
                'phone.required' => 'Phone must be added',
                'available_unit.required' => 'Unit number must be added',
                'address.required' => 'Address must be added',
            ];
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
                $country_code = $request->country_code;
                /****send mail if username and password exist **********/
                if($password != '') {
                    /***send sms to employee *****************/
                    $mobile_no = $request->phone;
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
                    $mail_res = Mail::to($email)->send(new BrokerRegisterMail($mailData));
                    $user->register_email_sent = 'yes';	
                        } catch (\Exception $e){
                        }	
                } else{}
                /****end send mail if username and password exist **********/
                $user->first_name = $first_name;
                $user->last_name = $last_name;
                $user->name = $name;
                $user->email = $request->email;
                $user->password = $password;
                $user->password_string = $password_string;
                $user->phone = $request->phone;
                $user->zip_code = $request->zip_code;
                $user->country_code = $country_code;
                $user->available_unit = $request->available_unit;
                $user->address = $request->address;
                $user->status = $request->status;
                $user->wsib_quarterly = $request->wsib_quarterly;
                $user->hourly_rate = ($request->hourly_rate > 0) ? $request->hourly_rate : 0;
                $user->incorporation_name = $request->incorporation_name;
                $user->type = 'broker';
                $user->save();
                // redirect
                Session::flash('message', 'Successfully created Broker!');
                return redirect()->route("brokers.index");
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
        if(Auth::user()->can('viewMenu:Broker') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            $brokers = User::find($id);
            if(null !== $brokers) {
                return view('brokers.show', compact('brokers'));
            } else {
                Session::flash('message', 'Broker does not exist!');
                return redirect()->route("brokers.index");
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
        if(Auth::user()->can('viewMenu:Broker') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            $brokers = User::find($id);
            return view('brokers.edit',compact('brokers'));
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
        if(Auth::user()->can('viewMenu:ActionBroker') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            $data  = $request->all();
            // validate
            $rules = [
                'first_name'  => 'required',
                'last_name'  => 'required',
                'email'  => "required|email|unique:users,email,$id",
                'phone'  => "required|unique:users,phone,$id",
            // 'zip_code'  => 'required',
                'available_unit'  => 'required|numeric',
                'address'  => 'required',
                'hourly_rate'  => 'sometimes|nullable|numeric|regex:/^\d*(\.\d{2})?$/',
            ];
            $message = [
                'first_name.required' => 'First name must be added',
                'last_name.required' => 'Last name must be added',
                'email.required' => 'Email must be added',
                'phone.required' => 'Phone must be added',
            //  'zip_code.required' => 'Zip code must be added',
                'available_unit.required' => 'Unit number must be added',
                'address.required' => 'Address must be added',
            ];
            $validator = Validator::make($data, $rules, $message);
            if ($validator->fails()) {
                return back()->withInput()
                    ->withErrors($validator);
            } else {
                // store
                $user = User::find($id);
                $register_email_sent = $user->register_email_sent;
                $register_sms_sent = $user->register_sms_sent;
                $existing_email = $user->email;
                $password_string = $password =  $request->password;
                $email = $request->email;
                if($password != '') {
                    $password = Hash::make($password);
                } else{}
                
                $first_name = $request->first_name;
                $last_name = $request->last_name;
                $name = $first_name.' '.$last_name;
                $country_code = $request->country_code;
                /***send sms to employee *****************/
                $mobile_no = $request->phone;
                if($mobile_no != '' && ($register_sms_sent == 'no' || $existing_email != $email)) {
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
                        $mail_res = Mail::to($email)->send(new BrokerRegisterMail($mailData));	
                        $user->register_email_sent = 'yes';
                    } catch (\Exception $e){
                    }
                } else{}
                /****end send mail if username and password exist **********/
                
                $user->first_name = $first_name;
                $user->last_name = $last_name;
                $user->name = $name;
                $user->email = $email;
                $user->password = $password;
                $user->password_string = $password_string;
                $user->phone = $request->phone;
                $user->zip_code = $request->zip_code;
                $user->country_code = $country_code;
                $user->available_unit = $request->available_unit;
                $user->address = $request->address;
                $user->status = $request->status;
                $user->wsib_quarterly = $request->wsib_quarterly;
                $user->hourly_rate = ($request->hourly_rate > 0) ? $request->hourly_rate : 0;
                $user->incorporation_name = $request->incorporation_name;
                $user->type = 'broker';
                $user->save();
                // redirect
                Session::flash('message', 'Successfully updated Broker!');
                return redirect()->route("brokers.index");
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
        if(Auth::user()->can('viewMenu:ActionBroker') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            $user = User::find($id);
            $user->delete();
            // redirect
            Session::flash('message', 'Successfully deleted broker!');
			return redirect()->route("brokers.index");
        }
        else
        {
            return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
        }
    }
}