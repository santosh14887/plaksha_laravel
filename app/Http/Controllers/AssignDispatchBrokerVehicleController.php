<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Dispatch;
use App\Models\AssignDispatch;
use App\Models\AssignDispatchBrokerVehicle;
use App\Models\Customer;
use App\Models\Vehicle;
use Auth;
use Validator;
use Session;
use DB;
use Carbon\Carbon;
class AssignDispatchBrokerVehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		die('here');
    }
	public function assigned_dispatche_broker_vehicles($id){
		$assigned_dispatch = AssignDispatch::find($id);
		$assign_dispatche_broker_vehicles = AssignDispatchBrokerVehicle::where('assign_dispatch_id','=',$id)->get();
		return view('assign_dispatch_broker_vehicles.index',compact('assign_dispatche_broker_vehicles','id','assigned_dispatch'));
	}
	
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
		$assigned_dispatch = AssignDispatch::find($id);
        return view('assign_dispatch_broker_vehicles.create',compact('assigned_dispatch'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		$assign_dispatch_id = $request->assign_dispatch_id;
		$data  = $request->all();
        // validate
        $rules = [
            'driver_name.*'  => 'required',
            'vehicle_number.*'  => 'required',
            'contact_number.*'  => 'required',
        ];
		$message = [
            'driver_name.*.required' => 'Driver name is required.',
			'contact_number.*.required' => 'Contact number is required.',
			'vehicle_number.*.required' => 'Vehicle number is required.',
        ];
        $validator = Validator::make($data, $rules, $message);

        if ($validator->fails()) {
            return back()->withInput()
                ->withErrors($validator);
        } else {
			$error_got = '';
			$contact_number_arr = $request->contact_number;
			$vehicle_number_arr = $request->vehicle_number;
			/*** making contact number as unique ***/
			$unique_contact_number = array_unique($contact_number_arr);
			$duplicate_contacts = array_diff_assoc($contact_number_arr, $unique_contact_number);
			if(!empty($duplicate_contacts)) {
				$error_got = 'yes';
				foreach($duplicate_contacts as $key => $value) {
					$validator->getMessageBag()->add('contact_number.'.$key, 'Contact number already used');
				}	
			}
			/***end making contact number as unique ***/
			/*** making contact number as unique ***/
			$unique_vehicle_number = array_unique($vehicle_number_arr);
			$duplicate_vehicles = array_diff_assoc($vehicle_number_arr, $unique_vehicle_number);
			if(!empty($duplicate_vehicles)) {
				$error_got = 'yes';
				foreach($duplicate_vehicles as $key => $value) {
					$validator->getMessageBag()->add('vehicle_number.'.$key, 'Vehicle number already used');
				}	
			}
			/***end making contact number as unique ***/
			if($error_got == 'yes') {
				return back()->withInput()
                ->withErrors($validator);
			}
			
            // store
			$data_insert = array();
			$driver_name_arr = $request->driver_name;
			$vehicle_number_arr = $request->vehicle_number;
			$contact_number_arr = $request->contact_number;
			foreach($driver_name_arr as $key => $value) {
				$new_arr = array();
				$created_at = Carbon::now()->timestamp;
				$created_by = Auth::user()->id;
				$assign_dispatch_id = $assign_dispatch_id;
				$driver_name = $value;
				$vehicle_number = $vehicle_number_arr[$key];
				$contact_number = $contact_number_arr[$key];
				$new_arr = array('driver_name' => $driver_name,'vehicle_number' => $vehicle_number,'contact_number' => $contact_number,'created_by' => $created_by,'assign_dispatch_id' => $assign_dispatch_id);
				$data_insert[] = $new_arr;
			}
			AssignDispatchBrokerVehicle::insert($data_insert);
            // redirect
            Session::flash('message', 'Successfully assigned broker vehicle!');
			return redirect("assigned_dispatche_broker_vehicles/".$assign_dispatch_id)->with('message', 'Successfully assigned broker vehicle!');
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
		$assigned_dispatch = AssignDispatch::find($id);
        $assign_dispatch_broker_vehicles = AssignDispatchBrokerVehicle::where('assign_dispatch_id','=',$id)->get();
		$driver_name = $vehicle_number = $contact_number = $rows_id = array();
		foreach($assign_dispatch_broker_vehicles as $value) {
			$driver_name[] = $value->driver_name;
			$vehicle_number[] = $value->vehicle_number;
			$contact_number[] = $value->contact_number;
			$rows_id[] = $value->id;
		}
		$assign_dispatch_broker_vehicles->driver_name = json_encode($driver_name);
		$assign_dispatch_broker_vehicles->vehicle_number = json_encode($vehicle_number);
		$assign_dispatch_broker_vehicles->contact_number = json_encode($contact_number);
		$assign_dispatch_broker_vehicles->rows_id = json_encode($rows_id);
		return view('assign_dispatch_broker_vehicles.edit',compact('assign_dispatch_broker_vehicles','assigned_dispatch'));
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
		$assign_dispatch_id = $request->assign_dispatch_id;
		$data  = $request->all();
        // validate
        $rules = [
            'driver_name.*'  => 'required',
            'vehicle_number.*'  => 'required',
            'contact_number.*'  => 'required',
        ];
		$message = [
            'driver_name.*.required' => 'Driver name is required.',
			'contact_number.*.required' => 'Contact number is required.',
			'vehicle_number.*.required' => 'Vehicle number is required.',
        ];
        $validator = Validator::make($data, $rules, $message);
        if ($validator->fails()) {
            return back()->withInput()
                ->withErrors($validator);
        } else {
			$error_got = '';
			$contact_number_arr = $request->contact_number;
			$vehicle_number_arr = $request->vehicle_number;
			/*** making contact number as unique ***/
			$unique_contact_number = array_unique($contact_number_arr);
			$duplicate_contacts = array_diff_assoc($contact_number_arr, $unique_contact_number);
			if(!empty($duplicate_contacts)) {
				$error_got = 'yes';
				foreach($duplicate_contacts as $key => $value) {
					$validator->getMessageBag()->add('contact_number.'.$key, 'Contact number already used');
				}	
			}
			/***end making contact number as unique ***/
			/*** making contact number as unique ***/
			$unique_vehicle_number = array_unique($vehicle_number_arr);
			$duplicate_vehicles = array_diff_assoc($vehicle_number_arr, $unique_vehicle_number);
			if(!empty($duplicate_vehicles)) {
				$error_got = 'yes';
				foreach($duplicate_vehicles as $key => $value) {
					$validator->getMessageBag()->add('vehicle_number.'.$key, 'Vehicle number already used');
				}	
			}
			/***end making contact number as unique ***/
			if($error_got == 'yes') {
				return back()->withInput()
                ->withErrors($validator);
			}
            // store
			$data_insert = array();
			$driver_name_arr = $request->driver_name;
			$vehicle_number_arr = $request->vehicle_number;
			$contact_number_arr = $request->contact_number;
			$status_arr = $request->status;
			$rows_id = $request->rows_id;
			foreach($driver_name_arr as $key => $value) {
				$new_arr = array();
				$updated_by = Auth::user()->id;
				$driver_name = $value;
				$vehicle_number = $vehicle_number_arr[$key];
				$contact_number = $contact_number_arr[$key];
				$row_id = $rows_id[$key];
				$data_array = array('driver_name' => $driver_name,'vehicle_number' => $vehicle_number,'contact_number' => $contact_number,'updated_by' => $updated_by);
				if($row_id > 0) {
					AssignDispatchBrokerVehicle::updateOrCreate(array('id' => $row_id), $data_array);
				} else {
					/* AssignDispatchBrokerVehicle::updateOrCreate(array('vehicle_number' => $vehicle_number,'contact_number' => $contact_number), $data_array); */
				}
				
			}
            // redirect
            Session::flash('message', 'Successfully assigned broker vehicle!');
			return redirect("assigned_dispatche_broker_vehicles/".$assign_dispatch_id)->with('message', 'Successfully updated assigned broker vehicle!');
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
        $dispatch = AssignDispatchBrokerVehicle::find($id);
        $dispatch->delete();
        // redirect
        Session::flash('message', 'Successfully deleted dispatch!');
			return redirect()->route("assign_dispatche_broker_vehicles.index");
    }
}
