<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\FuelHistory;
use App\Models\Fuel;
use Auth;
use Validator;
use Session;
use DB;
class FuelHistoryController extends Controller
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
				$fuel_histories = FuelHistory::where('vehicle_number', 'LIKE', "%$keyword%")
                ->orWhere('on_date', 'LIKE', "%$keyword%")
                ->orWhere('total_km', 'LIKE', "%$keyword%")
                ->orWhere('source', 'LIKE', "%$keyword%")
					->latest()->paginate($perPage);
			} else {
				$fuel_histories = FuelHistory::latest()->paginate($perPage);
			}
            return view('fuel_histories.index',compact('fuel_histories'));
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
			return view('fuel_histories.create',compact('vehicle'));
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
			// validate
			$rules = [
				'on_date' => 'required',
			   'starting_km' => 'required',
				'vehicle_id'  => 'required',
				'fuel_qty'  => 'required',
				//'fuel_receipt'  => 'required',
			];
			$message = [
				'on_date.required' => 'Date is required.',
				'starting_km.required' => 'Starting Km is required.',
				'vehicle_id.required' => 'Vehicle number is required.',
				'fuel_qty.required' => 'Fuel quantity is required.',
				//'fuel_receipt.required' => 'Fuel receipt is required.',
			];
			$validator = Validator::make($data, $rules, $message);

			if ($validator->fails()) {
				return back()->withInput()
					->withErrors($validator);
			} else {
				$error_got = '';
                $starting_km = $request->starting_km;
				$fuel_qty = $request->fuel_qty;
				$fuel_card_number = $request->fuel_card_number;
                $fuel_id = $per_liter_amount = 0;
				$on_date = date("Y-m-d", strtotime($request->on_date));
                $on_date_show = date('d M Y',strtotime($on_date));
				$fuel_history = FuelHistory::where('vehicle_id',$vehicle_id)->where('on_date',$on_date)->get()->toArray();
				if(isset($fuel_history) && !empty($fuel_history)) {
					$error_got = 'yes';
					$validator->getMessageBag()->add('fuel_qty', 'Fuel quantity is already added for '.$on_date_show);
				} else{}
                $check_starting_km_after_date = FuelHistory::where('vehicle_id',$vehicle_id)->where('on_date','>=',$on_date)->limit(1)->orderBy('on_date','asc')->get()->toArray();
               if(isset($check_starting_km_after_date) && !empty($check_starting_km_after_date)) {
                $check_starting_km_after_date = $check_starting_km_after_date[0];
                    $next_starting_km = $check_starting_km_after_date['starting_km'];
                    if($next_starting_km <= $starting_km) {
                        $error_got = 'yes';
					$validator->getMessageBag()->add('starting_km', 'Starting Km should be less then '.$next_starting_km);
                    }
               } else {}
                $fuel_arr = Fuel::where('on_date',$on_date)->get()->toArray();
                if(isset($fuel_arr) && !empty($fuel_arr)) {
                    $fuel_arr = $fuel_arr['0'];
                    $price_added = 'yes';
                    $fuel_id = $fuel_arr['id'];
                    $amount = $fuel_arr['amount'];
                    $per_liter_amount = $amount;
                   // $km_run = $ending_km - $starting_km;
                    // $amount_paid = ($km_run / 2) * $amount;
                } else {
                    $error_got = 'yes';
					$validator->getMessageBag()->add('fuel_qty', 'Fuel price is not added for '.$on_date_show);
                }
                if($error_got == 'yes') {
                    return back()->withInput()
                    ->withErrors($validator);
                } else{}
                /********  get last date before on date  ***/
                $fuel_last_one_date_history = FuelHistory::where('vehicle_id',$vehicle_id)->where('on_date','<',$on_date)->orderBy('on_date','desc')->limit(1)->get()->toArray();
               if(isset($fuel_last_one_date_history) && !empty($fuel_last_one_date_history)) {
                $fuel_last_one_date_history = $fuel_last_one_date_history[0];
                $dispatch_ticket_id = $fuel_last_one_date_history['dispatch_ticket_id'];
                    $id = $fuel_last_one_date_history['id'];
                    $fuel_history_arr = FuelHistory::find($id);
                    $prev_starting_km = $fuel_last_one_date_history['starting_km'];
                    $prev_fuel_qty = $fuel_last_one_date_history['fuel_qty'];
                    $prev_per_liter_amount = $fuel_last_one_date_history['per_liter_amount'];
                    if($starting_km <= $prev_starting_km) {
                        $error_got = 'yes';
                        $validator->getMessageBag()->add('starting_km', 'Starting km should be greter then '.$prev_starting_km);
                        return back()->withInput()
                         ->withErrors($validator);
                    } else {
                        /***********current starting km is ending for prev record */
                        $ending_km = $starting_km;
                        /***********end current starting km is ending for prev record */
                        $fuel_history_arr->ending_km = $starting_km;
                        $total_km = $ending_km - $prev_starting_km;
                        $fuel_history_arr->total_km = $total_km;
                        $fuel_economy = $total_km / $prev_fuel_qty;
                        $fuel_history_arr->fuel_economy = number_format((float)$fuel_economy, 2, '.', '');
                        $amount_paid = ($total_km / 2) * $prev_per_liter_amount;
                       // $fuel_history_arr->fuel_expense = $amount_paid;
                         $fuel_history_arr->save();
                     } 
               } else {}
               
                /******** end get last date before on date  ***/
                $vehicles = Vehicle::find($vehicle_id);
                $vehicle_number = $vehicles->vehicle_number;
				
				// store
                $meter_history = new FuelHistory;
                if($request->file('fuel_receipt') != null){
					$file = $request->file('fuel_receipt');
					$filename= date('YmdHi').$file->getClientOriginalName();
					$file-> move(public_path('images/fuel_receipt'), $filename);
					$meter_history->fuel_receipt = $filename;
				}
				
				$meter_history->created_by = Auth::user()->id;
				$meter_history->vehicle_id = $vehicle_id;
				$meter_history->on_date = $on_date;
				$meter_history->fuel_tbl_amount_date = $on_date;
				$meter_history->fuel_id = $fuel_id;
				$meter_history->per_liter_amount = $per_liter_amount;
				$meter_history->fuel_qty = $fuel_qty;
				$meter_history->starting_km = $starting_km;
				$meter_history->fuel_expense = $fuel_qty * $per_liter_amount;
				$meter_history->vehicle_number = $vehicle_number;
				$meter_history->fuel_card_number = $fuel_card_number;
				$meter_history->comment = $request->comment;
				$meter_history->source = 'Manual';
				$meter_history->save();
				/******* get records greter then on starting date */
                $get_records_greter_history = FuelHistory::where('vehicle_id',$vehicle_id)->where('on_date','>=',$on_date)->orderBy('on_date','desc')->get()->toArray();
                $total_records = count($get_records_greter_history);
                if($total_records > 0) {
                   $last_top_index = $total_records - 2;
                    for($loop_start = 0; $loop_start <= $last_top_index;$loop_start++) {
                        $loop_next_index = $loop_start + 1;
                        $id = $get_records_greter_history[$loop_next_index]['id'];
                        $fuel_history_arr = FuelHistory::find($id);
                        $current_starting_km = $get_records_greter_history[$loop_start]['starting_km'];
                        $prev_starting_km = $get_records_greter_history[$loop_next_index]['starting_km'];
                        $prev_per_liter_amount = $get_records_greter_history[$loop_next_index]['per_liter_amount'];
                        $prev_fuel_qty = $get_records_greter_history[$loop_next_index]['fuel_qty'];
                        /***********current starting km is ending for prev record */
                        $ending_km = $current_starting_km;
                        /***********end current starting km is ending for prev record */
                        $fuel_history_arr->ending_km = $ending_km;
                        $total_km = $ending_km - $prev_starting_km;
                        $fuel_history_arr->total_km = $total_km;
                        $fuel_economy = $total_km / $prev_fuel_qty;
                        $fuel_history_arr->fuel_economy = number_format((float)$fuel_economy, 2, '.', '');
                        $amount_paid = ($total_km / 2) * $prev_per_liter_amount;
                      //  $fuel_history_arr->fuel_expense = $amount_paid;
                        $fuel_history_arr->save();

                    }
                } else {}
                /******* end get records greter then on starting date */
				// redirect
				Session::flash('message', 'Successfully history added!');
				return redirect("/fuel_histories")->with('message', 'Successfully history added!');
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
        if(Auth::user()->can('viewMenu:Vehicle') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
			$fuel_histories = FuelHistory::find($id);
			return view('fuel_histories.show',compact('fuel_histories'));
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
        $fuel_histories = FuelHistory::find($id);
		$vehicle = Vehicle::get()->pluck('vehicle_number','id');
		
		return view('fuel_histories.edit',compact('fuel_histories','vehicle'));
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
            $fuel_history = FuelHistory::find($id);
            $on_date = $fuel_history->on_date;
            $vehicle_id = $fuel_history->vehicle_id;
            FuelHistory::destroy($id);
            /******* get records greter then on starting date */
            $get_records_greter_history = FuelHistory::where('vehicle_id',$vehicle_id)->where('on_date','>=',$on_date)->orderBy('on_date','desc')->get()->toArray();
            $total_records = count($get_records_greter_history);
            if($total_records > 0) {
               $last_top_index = $total_records - 2;
                for($loop_start = 0; $loop_start <= $last_top_index;$loop_start++) {
                    $loop_next_index = $loop_start + 1;
                    $id = $get_records_greter_history[$loop_next_index]['id'];
                    $fuel_history_arr = FuelHistory::find($id);
                    $current_starting_km = $get_records_greter_history[$loop_start]['starting_km'];
                    $prev_starting_km = $get_records_greter_history[$loop_next_index]['starting_km'];
                    $prev_per_liter_amount = $get_records_greter_history[$loop_next_index]['per_liter_amount'];
                    $prev_fuel_qty = $get_records_greter_history[$loop_next_index]['fuel_qty'];
                    /***********current starting km is ending for prev record */
                    $ending_km = $current_starting_km;
                    /***********end current starting km is ending for prev record */
                    $fuel_history_arr->ending_km = $ending_km;
                    $total_km = $ending_km - $prev_starting_km;
                    $fuel_history_arr->total_km = $total_km;
                    $fuel_economy = $total_km / $prev_fuel_qty;
                    $fuel_history_arr->fuel_economy = number_format((float)$fuel_economy, 2, '.', '');
                    $amount_paid = ($total_km / 2) * $prev_per_liter_amount;
                  //  $fuel_history_arr->fuel_expense = $amount_paid;
                    $fuel_history_arr->save();

                }
            } else {}
            /******* end get records greter then on starting date */
            // redirect
            Session::flash('message', 'Successfully deleted history!');
            return redirect("/fuel_histories")->with('message', 'Successfully deleted history!');
		}
        else
        {
            return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
        }
    }
}