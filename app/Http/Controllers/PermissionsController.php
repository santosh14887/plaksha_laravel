<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Auth;
use Config;
class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index(Request $request)
    {
		if(Auth::user()->can('viewMenu:Permission') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			$keyword = $request->get('search');
			$perPage = 15;

			if (!empty($keyword)) {
				$permissions = Permission::where('name', 'LIKE', "%$keyword%")->orWhere('label', 'LIKE', "%$keyword%")
					->latest()->paginate($perPage);
			} else {
				$permissions = Permission::latest()->paginate($perPage);
			}

			return view('permissions.index', compact('permissions'));
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
		if(Auth::user()->can('viewMenu:ActionPermission') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			return view('permissions.create');
		}
		else
		{
			return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
		}
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function store(Request $request)
    {
		if(Auth::user()->can('viewMenu:ActionPermission') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			$this->validate($request, ['name' => 'required']);
			Permission::create($request->all());
			return redirect('permissions')->with('flash_message', 'Permission added!');
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
		if(Auth::user()->can('viewMenu:Permission') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			$permission = Permission::findOrFail($id);

			return view('permissions.show', compact('permission'));
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
		if(Auth::user()->can('viewMenu:ActionPermission') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			$permission = Permission::findOrFail($id);

			return view('permissions.edit', compact('permission'));
		}
		else
		{
			return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
		}
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return void
     */
    public function update(Request $request, $id)
    {
		if(Auth::user()->can('viewMenu:ActionPermission') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			$this->validate($request, ['name' => 'required']);

			$permission = Permission::findOrFail($id);
			$permission->update($request->all());

			return redirect('permissions')->with('flash_message', 'Permission updated!');
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
		if(Auth::user()->can('viewMenu:ActionPermission') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
		{
			Permission::destroy($id);

			return redirect('permissions')->with('flash_message', 'Permission deleted!');
		}
		else
		{
			return redirect(Config::get('constants.InvalidErrorUrl'))->with(Config::get('constants.InvalidErrorClass'), Config::get('constants.InvalidError'));
		}
    }
}