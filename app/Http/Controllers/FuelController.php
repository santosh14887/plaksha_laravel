<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fuel;
use Auth;
use Validator;
use Session;
use DB;
use Config;
class FuelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(Auth::user()->can('viewMenu:Fuel') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
             $perPage = 15;
             $keyword = $request->get('search');
			if (!empty($keyword)) {
				$fuels = Fuel::where('on_date', 'LIKE', "%$keyword%")
                ->orWhere('amount', 'LIKE', "%$keyword%")->latest()->paginate($perPage);
			} else {
				$fuels = Fuel::latest()->paginate($perPage);
			}
            return view('fuels.index',compact('fuels'));
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
        if(Auth::user()->can('viewMenu:Fuel') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            return view('fuels.create');
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
        if(Auth::user()->can('viewMenu:ActionFuel') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            $data  = $request->all();
            // validate
            $rules = [
            'on_date'  => "required|unique:fuels",
            'amount'  => 'required|numeric',
			];
			$message = [
				'on_date.required' => 'Date must be added',
				'on_date.unique' => 'The date has already been taken.',
				'amount.required' => 'Amount must be added',
			];
            $validator = Validator::make($data, $rules, $message);

            if ($validator->fails()) {
                return back()->withInput()
                    ->withErrors($validator);
            } else {
                // store
                $amount = ($request->amount > 0 ) ? $request->amount : 0;
                if(isset($request->on_date) && $request->on_date != '') {
                    $on_date = $request->on_date;
                    $on_date = date("Y-m-d H:i:s", strtotime($on_date));
                }
                $fuel = new Fuel;
                $fuel->amount = $amount;
                $fuel->on_date = $on_date;
                $fuel->save();

                // redirect
                Session::flash('message', 'Successfully created fuel!');
                return redirect()->route("fuels.index");
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
        if(Auth::user()->can('viewMenu:ActionFuel') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            $fuels = Fuel::find($id);
            return view('fuels.edit',compact('fuels'));
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
        if(Auth::user()->can('viewMenu:ActionFuel') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
        $data  = $request->all();
        // validate
        $rules = [
            'on_date'  => "required|unique:fuels,on_date,$id",
            'amount'  => 'required|numeric',
        ];
		$message = [
            'on_date.required' => 'Date must be added',
            'on_date.unique' => 'The date has already been taken.',
            'amount.required' => 'Amount must be added',
        ];
        $validator = Validator::make($data, $rules, $message);
        if ($validator->fails()) {
            return back()->withInput()
                ->withErrors($validator);
        } else {
            // store
			$amount = ($request->amount > 0 ) ? $request->amount : 0;
			if(isset($request->on_date) && $request->on_date != '') {
				$on_date = $request->on_date;
				$on_date = date("Y-m-d H:i:s", strtotime($on_date));
			}
            $fuel = Fuel::find($id);
			$fuel->amount = $amount;
            $fuel->on_date = $on_date;
            $fuel->save();

            // redirect
            Session::flash('message', 'Successfully updated fuel!');
			return redirect()->route("fuels.index");
        }
        }
        else
        {
            return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
        }
    }
	 public function show($id)
    {
        if(Auth::user()->can('viewMenu:Fuel') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
            $fuels = Fuel::find($id);
            if(null !== $fuels) {
                return view('fuels.show', compact('fuels'));
            } else {
                Session::flash('message', 'Fuel does not exist!');
                return redirect()->route("fuels.index");
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
        if(Auth::user()->can('viewMenu:ActionFuel') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
        $fuel = Fuel::find($id);
        $fuel->delete();
        // redirect
        Session::flash('message', 'Successfully deleted fuel!');
			return redirect()->route("fuels.index");
        }
        else
        {
            return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
        }
    }
}