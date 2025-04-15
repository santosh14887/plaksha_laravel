<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use Config;
class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index(Request $request)
    {
		if(Auth::user()->can('viewMenu:Roles') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			$keyword = $request->get('search');
			$perPage = 15;

			if (!empty($keyword)) {
				$roles = Role::where('name', 'LIKE', "%$keyword%")->orWhere('label', 'LIKE', "%$keyword%")
					->latest()->paginate($perPage);
			} else {
				$roles = Role::latest()->paginate($perPage);
			}

			return view('roles.index', compact('roles'));
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
		if(Auth::user()->can('viewMenu:ActionRoles') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			$permissions = Permission::select('label')->orderBy('id', 'asc')->groupBy('label')->get()->pluck('label', 'label');
		   // return view('roles.create');
			return view('roles.create', compact('permissions'));
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
		if(Auth::user()->can('viewMenu:ActionRoles') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			$this->validate($request, ['name' => 'required']);
			$role = Role::create($request->all());
			$role->permissions()->detach();

			if ($request->has('permissions')) {
				foreach ($request->permissions as $permission_name) {
					$permission = Permission::whereName($permission_name)->first();
					$role->givePermissionTo($permission);
				}
			}

			return redirect('roles')->with('flash_message', 'Role added!');
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
     *
     * @return void
     */
    public function show($id)
    {
		if(Auth::user()->can('viewMenu:Roles') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			$permissions = Permission::select('label')->orderBy('id', 'asc')->groupBy('label')->get()->pluck('label', 'label');
			$role = Role::findOrFail($id);
			return view('roles.show', compact('role','permissions'));
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
		if(Auth::user()->can('viewMenu:ActionRoles') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			$role = Role::findOrFail($id);
			$permissions = Permission::select('label')->orderBy('id', 'asc')->groupBy('label')->get()->pluck('label', 'label');
			$role_permission = Role::find($id)->permissions->pluck('name', 'name')->toArray();
			return view('roles.edit', compact('role', 'permissions','role_permission'));
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
     *
     * @return void
     */
    public function update(Request $request, $id)
    {
		if(Auth::user()->can('viewMenu:ActionRoles') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			$this->validate($request, ['name' => 'required']);

			$role = Role::findOrFail($id);
			$role->update($request->all());
			$role->permissions()->detach(); 
			if ($request->has('permissions')) {
				foreach ($request->permissions as $permission_name) {
					$permission = Permission::whereName($permission_name)->first();
					$role->givePermissionTo($permission);
				}
			}

			return redirect('roles')->with('flash_message', 'Role updated!');
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
		if(Auth::user()->can('viewMenu:ActionRoles') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			Role::destroy($id);

			return redirect('roles')->with('flash_message', 'Role deleted!');
		}
		else
		{
			return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
		}
    }
}