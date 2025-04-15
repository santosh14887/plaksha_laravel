<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\Transaction;
use Auth;
use Validator;
use Session;
use DB;
use Hash;
use Config;
class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        die();
    }
	public function all_transaction(Request $request,$id) {
        if(Auth::user()->can('viewMenu:Transaction') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
		$perPage = 15;
        $keyword = $request->get('search');
		$user_data = User::where('id','=',$id)->get()->toArray();
		$user_data = $user_data[0];
		$query =  Transaction::query()->where(['user_id' => $id])->where('user_type', 'employee')->with('getUser')->with('dispatches');
			if (!empty($keyword)) {
                /*$transactions = $query->where(function($query) use ($keyword) {
					$query->where('type', '=','employee');
					})->orWhereHas('getVehicle',function ($query)use($keyword)
                    {
                        $query->where('vehicles.vehicle_number','Like','%'.$keyword.'%');
                    })->where(function($query) use ($keyword) {
					$query->where('first_name', 'LIKE', "%$keyword%")
                    ->orWhere('hst', 'LIKE', "%$keyword%");					
					})->latest()->orderBy('id', 'desc')->paginate($perPage); */
			} else {
				$transactions = $query->latest()->paginate($perPage);
			}
			return view('transactions.index',compact('transactions','user_data'));
        }
        else
        {
            return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
        }
	}
	public function all_customer_transaction(Request $request,$id) {
        if(Auth::user()->can('viewMenu:Transaction') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') {
		$perPage = 15;
        $keyword = $request->get('search');
		$user_data = Customer::where('id','=',$id)->get()->toArray();
		$user_data = $user_data[0];
		$query =  Transaction::query()->where(['user_id' => $id])->where('user_type', 'customer')->with('getCustomer')->with('dispatches');
			if (!empty($keyword)) {
                /*$transactions = $query->where(function($query) use ($keyword) {
					$query->where('type', '=','employee');
					})->orWhereHas('getVehicle',function ($query)use($keyword)
                    {
                        $query->where('vehicles.vehicle_number','Like','%'.$keyword.'%');
                    })->where(function($query) use ($keyword) {
					$query->where('first_name', 'LIKE', "%$keyword%")
                    ->orWhere('hst', 'LIKE', "%$keyword%");					
					})->latest()->orderBy('id', 'desc')->paginate($perPage); */
			} else {
				$transactions = $query->latest()->paginate($perPage);
			}
			return view('transactions.index_customer',compact('transactions','user_data'));
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
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	
    public function store(Request $request)
    {
		
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
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
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    }
}