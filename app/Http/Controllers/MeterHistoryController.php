<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\MeterHistory;
use Auth;
use Validator;
use Session;
use DB;
class MeterHistoryController extends Controller
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
				$meter_histories = MeterHistory::where('vehicle_number', 'LIKE', "%$keyword%")
                ->orWhere('on_date', 'LIKE', "%$keyword%")
                ->orWhere('total_km', 'LIKE', "%$keyword%")
                ->orWhere('source', 'LIKE', "%$keyword%")
					->latest()->paginate($perPage);
			} else {
				$meter_histories = MeterHistory::latest()->paginate($perPage);
			}
            return view('meter_histories.index',compact('meter_histories'));
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
			$vehicle = Vehicle::get()->pluck('vehicle_number','id');
			return view('meter_histories.create',compact('vehicle'));
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
			$vehicle_id = $request->vehicle_id;
			
			$data  = $request->all();
			$vehicle_id = $request->vehicle_id;
			$vehicles = Vehicle::find($vehicle_id);
			// validate
			$rules = [
				'on_date' => 'required',
			   'total_km' => 'required',
				'vehicle_id'  => 'required',
			];
			$message = [
				'on_date.required' => 'Date is required.',
				'total_km.required' => 'Km is required.',
				'vehicle_id.required' => 'Vehicle number is required.',
			];
			$validator = Validator::make($data, $rules, $message);

			if ($validator->fails()) {
				return back()->withInput()
					->withErrors($validator);
			} else {
				$error_got = '';
				$total_km = $request->total_km;
				$prev_total_km = $vehicles->total_km;
				$vehicle_number = $vehicles->vehicle_number;
				if($total_km <= $prev_total_km) {
					$error_got = 'yes';
					$validator->getMessageBag()->add('total_km', 'Please add km greater then '.$prev_total_km);
				}
				if($error_got == 'yes') {
					return back()->withInput()
					->withErrors($validator);
				}
				// store
				$on_date = date("Y-m-d", strtotime($request->on_date));
				$meter_history = new MeterHistory;
				$meter_history->created_by = Auth::user()->id;
				$meter_history->vehicle_id = $vehicle_id;
				$meter_history->on_date = $on_date;
				$meter_history->total_km = $total_km;
				$meter_history->vehicle_number = $vehicle_number;
				$meter_history->comment = $request->comment;
				$meter_history->source = 'Manual';
				$meter_history->save();
				/*** update vehicle km */
				$vehicles->total_km = $total_km;
				$vehicles->save();
				/*** end update vehicle km */
				// redirect
				Session::flash('message', 'Successfully history added!');
				return redirect("/meter_histories")->with('message', 'Successfully history added!');
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
        die('dead');
        $meter_histories = MeterHistory::find($id);
		$vehicle = Vehicle::get()->pluck('vehicle_number','id');
		
		return view('meter_histories.edit',compact('meter_histories','vehicle'));
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
        die('dead');
        
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
		MeterHistory::destroy($id);
        // redirect
        Session::flash('message', 'Successfully deleted history!');
		return redirect("/meter_histories")->with('message', 'Successfully deleted history!');
		}
        else
        {
            return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
        }
    }
}