<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Auth;
use Validator;
use Session;
use DB;
use Config;
use App\Models\CredsDetail;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;
use QuickBooksOnline\API\Facades\Customer as quickbookCustomer; 
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(Auth::user()->can('viewMenu:Customer') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
        $perPage = 15;
        $keyword = $request->get('search');
			if (!empty($keyword)) {
				$customers = Customer::where('company_name', 'LIKE', "%$keyword%")
                ->orWhere('address', 'LIKE', "%$keyword%")
                ->orWhere('customer_hst', 'LIKE', "%$keyword%")
                //->orWhere('hourly_rate', 'LIKE', "%$keyword%")
               // ->orWhere('rate_per_load', 'LIKE', "%$keyword%")
					->latest()->paginate($perPage);
			} else {
				$customers = Customer::latest()->paginate($perPage);
			}
            return view('customers.index',compact('customers'));
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->can('viewMenu:ActionCustomer') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') { 
            return view('customers.create');
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
        if(Auth::user()->can('viewMenu:ActionCustomer') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            $data  = $request->all();
            // validate
            $rules = [
                'company_name'  => 'required',
              //  'address'  => 'required',
                'customer_hst'  => 'required',
                'street_line'  => 'required',
                'city'  => 'required',
                'country'  => 'required',
                'country_devision_code'  => 'required',
                'postal_code'  => 'required',
            //  'hourly_rate'  => 'required|numeric|regex:/^\d*(\.\d{2})?$/',
            //  'rate_per_load'  => 'required|numeric|regex:/^\d*(\.\d{2})?$/',
            ];
            $message = [
                'company_name.required' => 'The Company name must be added',
               // 'address.required' => 'The Address must be added',
                'customer_hst.required' => 'The HST must be added',
                'hourly_rate.required' => 'Hourly Rate must be added',
                'rate_per_load.required' => 'The Rate per load must be added',
                'street_line.required' => 'The Street or Line must be added',
                'city.required' => 'The City must be added',
                'country.required' => 'The Country must be added',
                'country_devision_code.required' => 'The Country devision code must be added',
                'postal_code.required' => 'The Postal code must be added',
            ];
            $validator = Validator::make($data, $rules, $message);

            if ($validator->fails()) {
                return back()->withInput()
                    ->withErrors($validator);
            } else {
				$quickbook_res = '';
				$quickbook_id = 0;
				$address = '';
				$company_name = $request->company_name;
				$customer_hst = $request->customer_hst;
				//$address = $request->address;
				$street_line = $request->street_line;
				$city = $request->city;
				$country = $request->country;
				$country_devision_code = $request->country_devision_code;
				$postal_code = $request->postal_code;
				$address = $street_line.', '.$city.', '.$country.'('.$country_devision_code.'), '.$postal_code;
				$quickbook_creds = $this->get_quickbook_creds();
				$quickbook_creds_arr = json_decode($quickbook_creds,true);
                if(isset($quickbook_creds_arr) && !empty($quickbook_creds_arr) && isset($quickbook_creds_arr['api_error'])) {
                    $message = $quickbook_creds_arr['api_error'];
                    $validator->getMessageBag()->add('company_name', $message);
                    return back()->withInput()
					->withErrors($validator);
                } else {}
                
				if(isset($quickbook_creds_arr) && !empty($quickbook_creds_arr)) {
					$dataService = DataService::Configure($quickbook_creds_arr);
					$dataService->throwExceptionOnError(true);
					//Add a new Vendor
                    try {
                        $theResourceObj = quickbookCustomer::create([
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
                                "Address" => ""
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
                        $validator->getMessageBag()->add('company_name', $message);
                        return back()->withInput()
                        ->withErrors($validator);
                     
                    }
				} else {}
                // store
                $customer = new Customer;
                $customer->quickbook_id = $quickbook_id;
                $customer->quickbook_res = $quickbook_res;
                $customer->company_name = $company_name;
                $customer->address = $address;
                $customer->customer_hst = $customer_hst;
                $customer->street_line = $street_line;
                $customer->city = $city;
                $customer->country = $country;
                $customer->country_devision_code = $country_devision_code;
                $customer->postal_code = $postal_code;
                /*$customer->hourly_rate = $request->hourly_rate;
                $customer->rate_per_load = $request->rate_per_load; */
                $customer->save();

                // redirect
                Session::flash('message', 'Successfully created customer!');
                return redirect()->route("customers.index");
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
        if(Auth::user()->can('viewMenu:Customer') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            $customers = Customer::find($id);
            if(null !== $customers) {
                return view('customers.show', compact('customers'));
            } else {
                Session::flash('message', 'customer does not exist!');
                return redirect()->route("customers.index");
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
        if(Auth::user()->can('viewMenu:ActionCustomer') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            $customers = Customer::find($id);
            return view('customers.edit',compact('customers'));
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
        if(Auth::user()->can('viewMenu:ActionCustomer') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
            $data  = $request->all();
            // validate
            $rules = [
                'company_name'  => 'required',
               // 'address'  => 'required',
                'customer_hst'  => 'required',
				'street_line'  => 'required',
                'city'  => 'required',
                'country'  => 'required',
                'country_devision_code'  => 'required',
                'postal_code'  => 'required',
            // 'hourly_rate'  => 'required|numeric|regex:/^\d*(\.\d{2})?$/',
            //  'rate_per_load'  => 'required|numeric|regex:/^\d*(\.\d{2})?$/',
            ];
            $message = [
                'company_name.required' => 'The Company name must be added',
                'address.required' => 'The Address must be added',
                'customer_hst.required' => 'The HST must be added',
                'hourly_rate.required' => 'Hourly Rate must be added',
                'rate_per_load.required' => 'The Rate per load must be added',
				'street_line.required' => 'The Street or Line must be added',
                'city.required' => 'The City must be added',
                'country.required' => 'The Country must be added',
                'country_devision_code.required' => 'The Country devision code must be added',
                'postal_code.required' => 'The Postal code must be added',
            ];
            $validator = Validator::make($data, $rules, $message);
            if ($validator->fails()) {
                return back()->withInput()
                    ->withErrors($validator);
            } else {
                // store
				$company_name = $request->company_name;
				$customer_hst = $request->customer_hst;
				//$address = $request->address;
				$street_line = $request->street_line;
				$city = $request->city;
				$country = $request->country;
				$country_devision_code = $request->country_devision_code;
				$postal_code = $request->postal_code;
				$address = $street_line.', '.$city.', '.$country.'('.$country_devision_code.'), '.$postal_code;
                $customer = Customer::find($id);
				$quickbook_id = $customer->quickbook_id;
				$quickbook_creds = $this->get_quickbook_creds();
				$quickbook_creds_arr = json_decode($quickbook_creds,true);
                if(isset($quickbook_creds_arr) && !empty($quickbook_creds_arr) && isset($quickbook_creds_arr['api_error'])) {
                    $message = $quickbook_creds_arr['api_error'];
                    $validator->getMessageBag()->add('company_name', $message);
                    return back()->withInput()
					->withErrors($validator);
                } else {}
				if(isset($quickbook_creds_arr) && !empty($quickbook_creds_arr) && $quickbook_id > 0) {
					$dataService = DataService::Configure($quickbook_creds_arr);
					$dataService->throwExceptionOnError(true);
                    try {
                        $quickbook_customer = $dataService->FindbyId('customer', $quickbook_id);
                        $theResourceObj = quickbookCustomer::update($quickbook_customer  , [
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
                                "Address" => ""
                            ]
                        ]);

                        $resultingObj = $dataService->Update($theResourceObj);
                        $error = $dataService->getLastError();
                        if ($error) {
                            $quickbook_res = "The Status code is: " . $error->getHttpStatusCode() . "\n";
                            $quickbook_res .= "The Helper message is: " . $error->getOAuthHelperError() . "\n";
                            $quickbook_res .= "The Response message is: " . $error->getResponseBody() . "\n";
                            $customer->quickbook_res = $quickbook_res;
                        }
                    } catch (\Exception $e){
                        $message = $e->getMessage();
                        $validator->getMessageBag()->add('company_name', $message);
                        return back()->withInput()
                        ->withErrors($validator);
                     
                    }
				} else {
					try {
						$dataService = DataService::Configure($quickbook_creds_arr);
						$dataService->throwExceptionOnError(true);
                        $theResourceObj = quickbookCustomer::create([
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
                                "Address" => ""
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
                        $validator->getMessageBag()->add('company_name', $message);
                        return back()->withInput()
                        ->withErrors($validator);
                     
                    }
				}
                $customer->company_name = $company_name;
                $customer->address = $address;
                $customer->customer_hst = $customer_hst;
				 $customer->street_line = $street_line;
                $customer->city = $city;
                $customer->country = $country;
                $customer->country_devision_code = $country_devision_code;
                $customer->postal_code = $postal_code;
            /*  $customer->hourly_rate = $request->hourly_rate;
                $customer->rate_per_load = $request->rate_per_load; */
                $customer->save();

                // redirect
                Session::flash('message', 'Successfully updated customers!');
                return redirect()->route("customers.index");
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
        if(Auth::user()->can('viewMenu:ActionCustomer') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
                $customer = Customer::find($id);
                $customer->delete();
                // redirect
                Session::flash('message', 'Successfully deleted customer!');
                return redirect()->route("customers.index");
            }
            else
            {
                return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
            }
    }
}