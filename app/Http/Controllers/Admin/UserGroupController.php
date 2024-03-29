<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserGroupController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')
            ->withCount('permissions')
            ->get();
        return view('admin.user-group.index')->with([
            'roles'=>$roles
        ]);
    }

    public function create()
    {
        return view('admin.user-group.create');
    }

    public function store(RoleRequest  $request)
    {
        $role = new Role();
        $this->extracted($role,$request);
        $role->save();
        toast('Group create successful','success');
        return redirect()->route('user-group.index');
    }

    public function edit($id)
    {
        $role = Role::where('id',$id)->first();
        if (empty($role)){
            toast('Group not found','error');
            return redirect()->back();
        }
        return view('admin.user-group.edit')
            ->with([
                'role'=>$role
            ]);
    }

    public function update($id, RoleRequest $request)
    {
        $role = Role::where('id',$id)->first();
        if (empty($role)){
            toast('Group not found','error');
            return redirect()->back();
        }
        $this->extracted($role,$request);
        $role->save();
        toast('Group update successful','success');
        return redirect()->route('user-group.index');
    }

    public function delete(Request $request)
    {
        $role = Role::where('id',$request->id)->first();
        if (empty($role)){
            toast('Group not found','error');
            return redirect()->back();
        }
        $role->delete();
        toast('Group delete successful','success');
        return redirect()->route('user-group.index');
    }
    private function extracted(Role $role, Request $request){
        $role->name = $request->name;
    }

    public function access($id)
    {
        $role = Role::where('id',$id)->first();
        if (empty($role)){
            toast('Group not found','error');
            return redirect()->back();
        }
        $permissions = Permission::orderBy('id','ASC')->get();
        $permissionGroups = getAdminPermissionGroupBy();
        return view('admin.user-group.access')
            ->with([
                'role'=>$role,
                'permissions'=>$permissions,
                'permissionGroups'=>$permissionGroups
            ]);
    }

    public function accessUpdate($id,Request $request)
    {
        $role =  Role::findOrFail($id);
        if (!empty($request->permission)){
            $permissions = Permission::whereIn('id',$request->permission)->get();
            $role->syncPermissions($permissions);
        }else{
            $role->syncPermissions([]);
        }
        toast('Permission assign successfully','success');
        return redirect()->back();
    }
}
