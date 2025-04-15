<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
Use DB;
use Auth;
use Config;
use Validator;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index(Request $request)
    {
		if(Auth::user()->can('viewMenu:User') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			$keyword = $request->get('search');
			$perPage = 15;

			if (!empty($keyword)) {
				$users = User::where('name', 'LIKE', "%$keyword%")->where('type', '=', "subadmin")->orWhere('email', 'LIKE', "%$keyword%")
					->latest()->paginate($perPage);
			} else {
				$users = User::where('type', '=', "subadmin")->latest()->paginate($perPage);
			}

			return view('users.index', compact('users'));
		}
		else
		{
			return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
		} 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
		if(Auth::user()->can('viewMenu:ActionUser') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			$roles = Role::select('id', 'name', 'label')->get();
			$roles = $roles->pluck('name', 'name');
			return view('users.create', compact('roles'));
		}
		else
		{
			return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
		} 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function store(Request $request)
    {
		
		if(Auth::user()->can('viewMenu:ActionUser') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			$data  = $request->all();
        // validate
        $rules = [
            'name' => 'required',
            'email' => 'required|string|max:255|email|unique:users',
            'password'  => 'required',
            'roles'  => 'required',
        ];
		$message = [
            'name' => 'User name is required.',
			'password' => 'password is required.',
        ];
        $validator = Validator::make($data, $rules, $message);
        if ($validator->fails()) {
            return back()->withInput()
                ->withErrors($validator);
        } else {
			$data = $request->except('password');
			$data['password'] = bcrypt($request->password);
			$data['type'] = 'subadmin';
			$data['password_string'] = $request->password;
			
			$user = User::create($data);
			$registered_user_id =  $user->id;
			foreach ($request->roles as $role) {
				$user->assignRole($role);
			}
			return redirect('users')->with('flash_message', 'User added!');
		}
    } else
	{
		return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
	} 
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function show($id)
    {
		if(Auth::user()->can('viewMenu:User') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			$user = User::findOrFail($id);
			return view('users.show', compact('user'));
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
     *
     * @return void
     */
    public function edit($id)
    {
		if(Auth::user()->can('viewMenu:ActionUser') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			$roles = Role::select('id', 'name', 'label')->get();
			$roles = $roles->pluck('name', 'name');

			$user = User::with('roles')->select('id', 'name', 'email','password_string')->findOrFail($id);
			$user_roles = [];
			foreach ($user->roles as $role) {
				$user_roles[$role->label] = $role->name;
			}
			return view('users.edit', compact('user', 'roles', 'user_roles'));
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
     * @param  int      $id
     *
     * @return void
     */
    public function update(Request $request, $id)
    {
		if(Auth::user()->can('viewMenu:ActionUser') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			$data  = $request->all();
			// validate
			$rules = [
				'name' => 'required',
				'email' => 'required|string|max:255|email|unique:users,email,' . $id,
				'password'  => 'required',
				'roles'  => 'required',
			];
			$message = [
				'name' => 'User name is required.',
				'password' => 'password is required.',
			];
			$validator = Validator::make($data, $rules, $message);
			if ($validator->fails()) {
				return back()->withInput()
					->withErrors($validator);
			} else {
			$data = $request->except('password');
			if ($request->has('password')) {
				$data['password'] = bcrypt($request->password);
				$data['password_string'] = $request->password;
			}
			$data['type'] = 'subadmin';
			$user = User::findOrFail($id);
			$user->update($data);

			$user->roles()->detach();
			foreach ($request->roles as $role) {
				$user->assignRole($role);
			}
			return redirect('users')->with('flash_message', 'User updated!');
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
     *
     * @return void
     */
    public function destroy($id)
    {
		if(Auth::user()->can('viewMenu:ActionUser') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			 DB::table('role_user')->where('user_id',$id)->delete();
			User::destroy($id);

			return redirect('users')->with('flash_message', 'User deleted!');
		}
		else
		{
			return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
		}
    }
}