<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\AgentUpdate;
use App\Http\Requests\User\UserPermissionActions;
use App\Http\Requests\User\UserRoleActions;
use App\Http\Requests\User\SaveRequest;
use App\Models\ImportedUser;
use App\Models\ServiceProvider;
use App\Models\ServiceProviderUser;
use App\Models\Setting;
use App\Models\User;
use App\Services\RolePermissionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use mysql_xdevapi\Exception;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public RolePermissionService $rolePermissionService;

    function __construct(RolePermissionService $rolePermissionService)
    {
//        $this->middleware('permission:role-list|role-create|role-edit|role-delete');
//        $this->middleware('permission:role-create', ['only' => ['create','store']]);
//        $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
//        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
        $this->rolePermissionService = $rolePermissionService;
    }

    public function index()
    {
        return view('pages.admin.users.index', [
            'users' => $this->getUsers(),
            'agents' => $this->getAgents(),
            'providerUsers' => $this->getProviderUsers(),
            'providers' => ServiceProvider::all(),
            'mainPoint' => Setting::where('slug', 'main_point')->first()->value,
        ]);
    }

    //users
    public function store(SaveRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($request->password);
        User::create($data);
        return redirect()->route('admin.users.index', ['#admin-users'])
            ->with(['users' => $this->getUsers(), 'success' => __('User data updated successfully')]);
    }

    public function edit(User $user)
    {
        return response()->json(['user' => $user]);
    }

    public function update(SaveRequest $request, User $user)
    {
        $user->update($request->validated());
        return redirect()->route('admin.users.index', ['#admin-users'])
            ->with(['users' => $this->getUsers(), 'success' => __('User data updated successfully')]);
    }

    public function destroy(User $user)
    {
        if ($user->email === 'admin@gmail.com' || $user->email === 'niklas.beg@personligtsvar.se')
            return redirect()->route('admin.users.index', ['#admin-users'])
                ->with(['error' => __('You can\'t delete admin')]);

        $user->delete();
        return redirect()->route('admin.users.index', ['#admin-users'])
            ->with(['success' => __('User deleted successfully')]);
    }

    private function getUsers()
    {
        return User::where('id', '<>', auth()->user()->id)->get();
    }

    //agents(imported_users)
    private function getAgents()
    {
        return ImportedUser::whereNotNull('servit_id')->get();
    }

    public function editAgent(ImportedUser $agent)
    {
        $agent = $agent->append('main_point');

        return response()->json(['agent' => $agent]);
    }

    public function updateAgent(AgentUpdate $request, ImportedUser $agent)
    {
        $mainPoint = Setting::where('slug', 'main_point')->first()->value;
        $agentPoint = $request->agent_point == $mainPoint ? null : $request->agent_point;
        $agent->update([
            'agent_point' => $agentPoint
        ]);
        return redirect()->route('admin.users.index', ['#agents'])
            ->with(['success' => __('Agent data updated successfully')]);
    }

    private function getProviderUsers()
    {
        return ServiceProviderUser::with('serviceProvider')->get();
    }

    //    Roles & Permissions Methods
    public function getRolesPermissions(User $user): \Illuminate\Http\JsonResponse
    {
        $this->rolePermissionService->setType($user, 'web');
        $res = $this->rolePermissionService->userRolesPermissions(true);
        $dontBelongRoles = $res['dontBelongRoles'];
        $dontBelongPermissions = $res['dontBelongPermissions'];
        return response()
            ->json([
                'view' => view('modals.users.attachRole', compact('dontBelongRoles', 'user', 'dontBelongPermissions'))->render(),
                'dontBelongRoles' => $dontBelongRoles,
                'dontBelongPermissions' => $dontBelongPermissions,
            ]);
    }

    public function storeRole(UserRoleActions $request, User $user)
    {
        try {
            $user->assignRole($request->role);
            return redirect()->route('admin.users.index', ['#admin-users'])
                ->with(['success' => __('Role(s) attached successfully to user')]);
        } catch (\Exception $exception) {
            return redirect()->route('admin.users.index', ['#admin-users'])
                ->with(['error' => __('Something went wrong.')]);
        }
    }

    public function deleteRole(UserRoleActions $request, User $user): \Illuminate\Http\RedirectResponse
    {
        try {
            $userDetachedRoles = $user->roles()->whereIn('id', $request->role)->pluck('id')->toArray();
            $user->roles()->detach($userDetachedRoles);
            return redirect()->route('admin.users.index', ['#admin-users'])
                ->with(['success' => __('Role(s) detached successfully to user')]);
        } catch (\Exception $exception) {
            return redirect()->route('admin.users.index', ['#admin-users'])
                ->with(['error' => __('Something went wrong.')]);
        }

    }

    public function storePermission(UserPermissionActions $request, User $user): \Illuminate\Http\RedirectResponse
    {

        try {
            $permissions = Permission::whereIn('id',$request->permission)->get();
            $user->givePermissionTo($permissions);
            return redirect()->route('admin.users.index', ['#admin-users'])
                ->with(['success' => __('Permission(s) attached successfully to user')]);
        } catch (\Exception $exception) {
            return redirect()->route('admin.users.index', ['#admin-users'])
                ->with(['error' => __('Something went wrong.')]);
        }
    }

    public function deletePermission(UserPermissionActions $request, User $user)
    {
        try {
            $userDetachedPermissions = $user->permissions()->whereIn('id', $request->permission)->pluck('id')->toArray();
            $user->permissions()->detach($userDetachedPermissions);
            return redirect()->route('admin.users.index', ['#admin-users'])
                ->with(['success' => __('Permission(s) detached successfully to user')]);
        } catch (\Exception $exception) {
            return redirect()->route('admin.users.index', ['#admin-users'])
                ->with(['error' => __('Something went wrong.')]);
        }
    }

// End Roles methods

}
