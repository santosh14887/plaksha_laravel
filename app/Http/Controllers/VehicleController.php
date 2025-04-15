<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleService;
use Auth;
use Validator;
use Session;
use DB;
use Config;
class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(Auth::user()->can('viewMenu:Vehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
             $perPage = 15;
             $keyword = $request->get('search');
			if (!empty($keyword)) {
				$vehicles = Vehicle::where('vehicle_number', 'LIKE', "%$keyword%")
                ->orWhere('service_due_every_km', 'LIKE', "%$keyword%")
                ->orWhere('air_filter_after_days', 'LIKE', "%$keyword%")
					->latest()->paginate($perPage);
			} else {
				$vehicles = Vehicle::latest()->paginate($perPage);
			}
            return view('vehicles.index',compact('vehicles'));
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
        if(Auth::user()->can('viewMenu:Vehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            return view('vehicles.create');
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

    public function generate_vehicle_des_arr($vin_number) {
        $vehicle_des_arr = array();
        $url = "https://vpic.nhtsa.dot.gov/api/vehicles/decodevinvalues/".$vin_number."?format=json";
            $crl = curl_init();
            curl_setopt($crl, CURLOPT_URL, $url);
            curl_setopt($crl, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
            
            $response = curl_exec($crl);
            if(!$response){
                return null;
            } 
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
		$vehicle_des_arr['ErrorCode'] = $res['ErrorCode'];  
		$vehicle_desc = json_encode($vehicle_des_arr);
		return $vehicle_desc;
    }
    public function store(Request $request)
    {
        if(Auth::user()->can('viewMenu:ActionVehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            $data  = $request->all();
            // validate
            $rules = [
                'vehicle_number'  => 'required|unique:vehicles',
                'service_due_every_km'  => 'required|numeric',
                'air_filter_after_days'  => 'required|numeric',
            ];
            $message = [
                'vehicle_number.required' => 'The Vehicle number must be added',
                'service_due_every_km.required' => 'The Service due must be added',
                'air_filter_after_days.required' => 'The Service due must be added',
            ];
            $validator = Validator::make($data, $rules, $message);

            if ($validator->fails()) {
                return back()->withInput()
                    ->withErrors($validator);
            } else {
                // store
                $last_air_filter_date = null;
                $due_air_filter_date = null;
                $vehicle_desc = null;
                $vin_number = $request->vin_number;
                $total_km = ($request->total_km > 0 ) ? $request->total_km : 0;
                $air_filter_after_days = $request->air_filter_after_days;
                if(isset($request->last_air_filter_date) && $request->last_air_filter_date != '') {
                    $last_air_filter = $request->last_air_filter_date;
                    $last_air_filter_date = date("Y-m-d H:i:s", strtotime($last_air_filter));
                    $due_air_filter_date = date('Y-m-d', strtotime($last_air_filter. ' + '.$air_filter_after_days.' days'));
                }
                if($vin_number != '') {
                    $vehicle_desc = $this->generate_vehicle_des_arr($vin_number);
                }
                $vehicle = new Vehicle;
                $vehicle->vehicle_number = $request->vehicle_number;
                $vehicle->service_due_every_km = $request->service_due_every_km;
                $vehicle->air_filter_after_days = $air_filter_after_days;
                $vehicle->due_air_filter_date = $due_air_filter_date;
                $vehicle->licence_plate = $request->licence_plate;
                $vehicle->vin_number = $vin_number;
                $vehicle->annual_safty_renewal = $request->annual_safty_renewal;
                $vehicle->licence_plate_sticker = $request->licence_plate_sticker;
                $vehicle->total_km = $total_km;
                $vehicle->last_air_filter_date = $last_air_filter_date;
                $vehicle->vehicle_desc = $vehicle_desc;
                $vehicle->save();

                // redirect
                Session::flash('message', 'Successfully created vehicle!');
                return redirect()->route("vehicles.index");
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
        if(Auth::user()->can('viewMenu:ActionVehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            $vehicles = Vehicle::find($id);
            return view('vehicles.edit',compact('vehicles'));
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
        if(Auth::user()->can('viewMenu:ActionVehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
        $data  = $request->all();
        // validate
        $rules = [
            'vehicle_number'  => "required|unique:vehicles,vehicle_number,$id",
            'service_due_every_km'  => 'required|numeric',
            'air_filter_after_days'  => 'required|numeric',
        ];
		$message = [
            'vehicle_number.required' => 'The Vehicle number must be added',
            'service_due_every_km.required' => 'The Service due must be added',
			'air_filter_after_days.required' => 'The air filter must be added',
        ];
        $validator = Validator::make($data, $rules, $message);
        if ($validator->fails()) {
            return back()->withInput()
                ->withErrors($validator);
        } else {
            // store
			$last_air_filter_date = null;
			$due_air_filter_date = null;
			$total_km = ($request->total_km > 0 ) ? $request->total_km : 0;
			$air_filter_after_days = $request->air_filter_after_days;
			if(isset($request->last_air_filter_date) && $request->last_air_filter_date != '') {
				$last_air_filter = $request->last_air_filter_date;
				$last_air_filter_date = date("Y-m-d H:i:s", strtotime($last_air_filter));
				$due_air_filter_date = date('Y-m-d', strtotime($last_air_filter. ' + '.$air_filter_after_days.' days'));
			}
            $vehicle = Vehicle::find($id);
            $vin_number = $request->vin_number;
            $pre_vehicle_desc = $vehicle->vehicle_desc;
            $vin_number = $request->vin_number;
            if(($pre_vehicle_desc == '' || $pre_vehicle_desc == null) && $vin_number != ''){
                $pre_vehicle_desc = $this->generate_vehicle_des_arr($vin_number);
            } else {
				$vehicle_desc = $pre_vehicle_desc;
				if($vehicle_desc != '' && $vehicle_desc != null) {
					$vehicle_desc_arr = array(); 
					$vehicle_desc = json_decode($vehicle_desc);
					foreach($vehicle_desc as $vehicle_parm => $vehicle_desc_val) {
						$vehicle_desc_arr[$vehicle_parm] = $request->$vehicle_parm;
					}
					$vehicle_desc_arr['ErrorText'] = $vehicle_desc->ErrorText;
					$vehicle_desc_arr['VIN'] = $vehicle_desc->VIN;
					$vehicle_desc_arr['ErrorCode'] = $vehicle_desc->ErrorCode;
					$pre_vehicle_desc = json_encode($vehicle_desc_arr);
				}
			}
			
            $vehicle->vehicle_number = $request->vehicle_number;
            $vehicle->service_due_every_km = $request->service_due_every_km;
            $vehicle->air_filter_after_days = $request->air_filter_after_days;
			$vehicle->licence_plate = $request->licence_plate;
            $vehicle->vin_number = $request->vin_number;
            $vehicle->annual_safty_renewal = $request->annual_safty_renewal;
            $vehicle->licence_plate_sticker = $request->licence_plate_sticker;
			$vehicle->total_km = $total_km;
            $vehicle->last_air_filter_date = $last_air_filter_date;
            $vehicle->due_air_filter_date = $due_air_filter_date;
            $vehicle->vehicle_desc = $pre_vehicle_desc;
            $vehicle->save();

            // redirect
            Session::flash('message', 'Successfully updated vehicle!');
			return redirect()->route("vehicles.index");
        }
        }
        else
        {
            return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
        }
    }
	 public function show($id)
    {
        if(Auth::user()->can('viewMenu:Vehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            $vehicles = Vehicle::find($id);
            if(null !== $vehicles) {
                $due_after_km = $total_run_km = $service_on_km = 0;
                $vehicle_ser = VehicleService::select('vehicle_id', DB::raw('MAX(on_km) as max_km'))->where('service_type','regular_service')->where('vehicle_id',$id)->groupBy('vehicle_id')->with('getVehicle')->get()->toArray();
                if(isset($vehicle_ser) && !empty($vehicle_ser)) {
                    foreach($vehicle_ser as $vehicle_ser_val) {
                        $prev_km = $vehicle_ser_val['max_km'];
                        $vehicle_number = $vehicle_ser_val['get_vehicle']['vehicle_number'];
                        $vin_number = $vehicle_ser_val['get_vehicle']['vin_number'];
                        $due_after_km = $vehicle_ser_val['get_vehicle']['service_due_every_km'];
                        $total_run_km = $vehicle_ser_val['get_vehicle']['total_km'];
                        $service_on_km = $prev_km +  $due_after_km;
                    }
                }
                return view('vehicles.show', compact('vehicles','due_after_km','total_run_km','service_on_km'));
            } else {
                Session::flash('message', 'Vehicle does not exist!');
                return redirect()->route("vehicles.index");
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
        if(Auth::user()->can('viewMenu:ActionVehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
        $vehicle = Vehicle::find($id);
        $vehicle->delete();
        // redirect
        Session::flash('message', 'Successfully deleted vehicle!');
			return redirect()->route("vehicles.index");
        }
        else
        {
            return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
        }
    }
}