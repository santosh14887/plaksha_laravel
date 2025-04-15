<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleAssignmentHistory;
use App\Models\Fuel;
use Auth;
use Validator;
use Session;
use DB;
class VehicleAssignmentHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		die('dead');
		if(Auth::user()->can('viewMenu:Vehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
        $perPage = 15;
        $keyword = $request->get('search');
			if (!empty($keyword)) {
				$vehicles = VehicleAssignmentHistory::where('vehicle_number', 'LIKE', "%$keyword%")
                ->orWhere('start_time', 'LIKE', "%$keyword%")
                ->orWhere('user_name', 'LIKE', "%$keyword%")
                ->orWhere('user_email', 'LIKE', "%$keyword%")
                ->orWhere('user_phone', 'LIKE', "%$keyword%")
					->latest()->paginate($perPage);
			} else {
				$vehicle_assignment_histories = VehicleAssignmentHistory::latest()->paginate($perPage);
			}
            return view('vehicle_assignment_histories.index',compact('vehicle_assignment_histories'));
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
		die('dead');
		if(Auth::user()->can('viewMenu:Vehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			$user_arr = User::where('type','=','employee')->get();
			$vehicle = Vehicle::get()->pluck('vehicle_number','id');
			return view('vehicle_assignment_histories.create',compact('vehicle','user_arr'));
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
		if(Auth::user()->can('viewMenu:ActionVehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			$data  = $request->all();
			$vehicle_id = $request->vehicle_id;
			// validate
			$rules = [
				'start_time' => 'required',
				'vehicle_id'  => 'required',
				'user_id'  => 'required'
			];
			$message = [
				'start_time.required' => 'Start time is required.',
				'user_id.required' => 'User is required.',
				'vehicle_id.required' => 'Vehicle number is required.',
			];
			$validator = Validator::make($data, $rules, $message);

			if ($validator->fails()) {
				return back()->withInput()
					->withErrors($validator);
			} else {
				$error_got = '';
                $vehicle_id = $request->vehicle_id;
                $user_id = $request->user_id;
				$start_time = date("Y-m-d h:i:s", strtotime($request->start_time));
				$end_time = ($request->end_time != '') ? date("Y-m-d h:i:s", strtotime($request->end_time)) : '';
                $on_date_show = date('d M Y',strtotime($start_time));
                $vehicles = Vehicle::find($vehicle_id);
                $vehicle_number = $vehicles->vehicle_number;
                $licence_plate = $vehicles->licence_plate;
                $vin_number = $vehicles->vin_number;
				
				$users = User::find($user_id);
				$user_name = $users->name;
				$user_type = $users->type;
				$user_email = $users->email;
				$user_country_code = $users->country_code;
				$phone = $users->phone;
				$user_phone = $user_country_code.''.$phone;
				// store
				$vehicle_assignment = new VehicleAssignmentHistory;
				$vehicle_assignment->created_by = Auth::user()->id;
				$vehicle_assignment->vehicle_id = $vehicle_id;
				$vehicle_assignment->vehicle_number = $vehicle_number;
				$vehicle_assignment->licence_plate = $licence_plate;
				$vehicle_assignment->vin_number = $vin_number;
				$vehicle_assignment->start_time = $start_time;
				$vehicle_assignment->end_time = $end_time;
				$vehicle_assignment->user_id = $user_id;
				$vehicle_assignment->user_name = $user_name;
				$vehicle_assignment->user_type = $user_type;
				$vehicle_assignment->user_email = $user_email;
				$vehicle_assignment->user_phone = $user_phone;
				$vehicle_assignment->comment = $request->comment;
				$vehicle_assignment->save();
				// redirect
				Session::flash('message', 'Successfully history added!');
				return redirect("/vehicle_assignment_histories")->with('message', 'Successfully history added!');
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
		die('dead');
        if(Auth::user()->can('viewMenu:Vehicle') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
			$vehicle_assignment_histories = VehicleAssignmentHistory::find($id);
			return view('vehicle_assignment_histories.show',compact('vehicle_assignment_histories'));
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
		die('dead');
		if(Auth::user()->can('viewMenu:Vehicle') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
        $vehicle_assignment_histories = VehicleAssignmentHistory::find($id);
		$current_time = date('Y-m-d h:i:s');
		$end_time = $vehicle_assignment_histories->end_time;
		$user_vehicle_type = $vehicle_assignment_histories->user_vehicle_type;
		if($current_time >= $end_time) {
			return redirect("/vehicle_assignment_histories")->with('message', 'End time is expired!');
		}
		if($user_vehicle_type == 'permanent') {
			return redirect("/vehicle_assignment_histories")->with('message', 'Users Permanent vehicle can be updated from users page!');
		}
		$user_arr = User::where('type','=','employee')->get();
		$vehicle = Vehicle::get()->pluck('vehicle_number','id');
		return view('vehicle_assignment_histories.edit',compact('vehicle_assignment_histories','user_arr','vehicle'));
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
        $data  = $request->all();
			$vehicle_id = $request->vehicle_id;
			// validate
			$rules = [
				'start_time' => 'required',
				'vehicle_id'  => 'required',
				'user_id'  => 'required'
			];
			$message = [
				'start_time.required' => 'Start time is required.',
				'user_id.required' => 'User is required.',
				'vehicle_id.required' => 'Vehicle number is required.',
			];
			$validator = Validator::make($data, $rules, $message);
        if ($validator->fails()) {
            return back()->withInput()
                ->withErrors($validator);
        } else {
			$error_got = '';
                $vehicle_id = $request->vehicle_id;
                $user_id = $request->user_id;
				$start_time = date("Y-m-d h:i:s", strtotime($request->start_time));
				$end_time = ($request->end_time != '') ? date("Y-m-d h:i:s", strtotime($request->end_time)) : '';
                $on_date_show = date('d M Y',strtotime($start_time));
                $vehicles = Vehicle::find($vehicle_id);
                $vehicle_number = $vehicles->vehicle_number;
                $licence_plate = $vehicles->licence_plate;
                $vin_number = $vehicles->vin_number;
				
				$users = User::find($user_id);
				$user_name = $users->name;
				$user_type = $users->type;
				$user_email = $users->email;
				$user_country_code = $users->country_code;
				$phone = $users->phone;
				$user_phone = $user_country_code.''.$phone;
				// store
				$vehicle_assignment =  VehicleAssignmentHistory::find($id);
				$vehicle_assignment->created_by = Auth::user()->id;
				$vehicle_assignment->vehicle_id = $vehicle_id;
				$vehicle_assignment->vehicle_number = $vehicle_number;
				$vehicle_assignment->licence_plate = $licence_plate;
				$vehicle_assignment->vin_number = $vin_number;
				$vehicle_assignment->start_time = $start_time;
				$vehicle_assignment->end_time = $end_time;
				$vehicle_assignment->user_id = $user_id;
				$vehicle_assignment->user_name = $user_name;
				$vehicle_assignment->user_type = $user_type;
				$vehicle_assignment->user_email = $user_email;
				$vehicle_assignment->user_phone = $user_phone;
				$vehicle_assignment->comment = $request->comment;
				$vehicle_assignment->save();
            Session::flash('message', 'Successfully assigned history!');
			return redirect("/vehicle_assignment_histories")->with('message', 'Successfully assigned history!');
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
		die('dead');
		if(Auth::user()->can('viewMenu:ActionVehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
		VehicleAssignmentHistory::destroy($id);
        // redirect
        Session::flash('message', 'Successfully deleted history!');
		return redirect("/vehicle_assignment_histories")->with('message', 'Successfully deleted history!');
		}
        else
        {
            return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
        }
    }
}