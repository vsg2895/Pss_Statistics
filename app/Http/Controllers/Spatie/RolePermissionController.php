<?php

namespace App\Http\Controllers\Spatie;

use App\Http\Controllers\Controller;
use App\Http\Requests\RolePermission\AttachPermission;
use App\Http\Requests\RolePermission\DetachPermission;
use App\Http\Requests\RolePermission\PermissionStore;
use App\Http\Requests\RolePermission\RoleStore;
use App\Services\RolePermissionService;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RolePermissionController extends Controller
{
    public RolePermissionService $rolePermissionService;
    public $permission = 'ressssssss';

    function __construct(RolePermissionService $rolePermissionService)
    {
//        $this->middleware("permission:$this->permission", ['only' => ['index', 'store']]);
//        $this->middleware('permission:role-create', ['only' => ['create', 'store']]);
//        $this->middleware('permission:role-edit', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
        $this->rolePermissionService = $rolePermissionService;
    }

    public function index()
    {
        $res = $this->rolePermissionService->getAllRolesPermissions();

        return view('pages.admin.users.roles-permissions', [
            'res' => $res
        ]);
    }

    public function store(RoleStore $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $this->rolePermissionService->storeRole($request->role, $request->permission);
            return redirect()->back()->with(['success' => __('Role added successfully')]);
        } catch (RoleAlreadyExists $exception) {
            return redirect()->back()->with(['error' => $exception->getMessage()]);
        }
    }

    public function storePermission(PermissionStore $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $res = $this->rolePermissionService->storePermission($request->permission, $request->role);
            return $res ? redirect()->back()->with(['success' => __('Permission added successfully')])
                : redirect()->back()->with(['error' => __('Please use Roles from the same type.')]);
        } catch (RoleAlreadyExists $exception) {
            return redirect()->back()->with(['error' => $exception->getMessage()]);
        }
    }

    public function getRolePermissions(Role $role): \Illuminate\Http\JsonResponse
    {
        $this->rolePermissionService->setType([], $role->guard_name);
        $dontAvailableRolePermissions = $this->rolePermissionService->getRolePermission($role);
        $availablePermissions = $role->permissions;
        return response()->json(['role' => $role, 'availablePermissions' => $availablePermissions, 'dontAvailablePermissions' => $dontAvailableRolePermissions]);

    }

    public function getGuardRoles($guard): \Illuminate\Http\JsonResponse
    {
        $currRoles = $this->rolePermissionService->getAllRolesPermissions($guard)['roles'];
        return response()
            ->json([
                'view' => view('modals.users.guardRoles', compact('currRoles'))->render(),
                'currRoles' => $currRoles,
            ]);
    }

    public function attachPermissionToRole(Role $role, AttachPermission $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $permissions = Permission::whereIn('id', $request->permission)->get();
            $this->rolePermissionService->attachPermissionToRole($role, $permissions);
            return redirect()->back()->with(['success' => __('Permission(s) attached to Role successfully')]);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => 'Something went wrong.']);
        }
    }

    public function detachPermissionToRole(Role $role, DetachPermission $request)
    {
        try {
            $permissions = !is_null($request->permission)
                ? Permission::whereIn('id', $request->permission)->get() : null;
            $this->rolePermissionService->detachPermissionToRole($role, $permissions, $request->all ? true : null);
            return redirect()->back()->with(['success' => __('Permission(s) detached to Role successfully')]);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => 'Something went wrong.']);
        }
    }

    public function destroy(Role $role): \Illuminate\Http\RedirectResponse
    {
        try {
            $role->delete();
            return redirect()->back()->with(['success' => __('Role deleted successfully')]);
        } catch (\Exception $exception) {
            return redirect()->back()->with(['error' => __('Something went wrong.')]);
        }
    }

    public function deletePermission(Permission $permission): \Illuminate\Http\RedirectResponse
    {
        try {
            $permission->delete();
            return redirect()->back()->with(['success' => __('Permission deleted successfully')]);
        } catch (\Exception $exception) {
            return redirect()->back()->with(['error' => __('Something went wrong.')]);
        }
    }
}
