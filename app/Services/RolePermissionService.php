<?php

namespace App\Services;

use App\Models\FeeType;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionService
{

    public $user;
    public $userClass;
    public $guard;

    public function setType($user = [], $guard = 'web'): void
    {
        $classUser = !is_array($user) ? get_class($user) : null;
        if (!is_null($classUser)) {
            $this->userClass = new $classUser;
        }
        $this->user = $user;
        $this->guard = $guard;
    }


    public function storeRole($roleName, $permissions = null): void
    {
        DB::beginTransaction();
        $role = Role::create(['name' => $roleName]);
        if (!is_null($permissions)) {
            $role->syncPermissions($permissions);
        }
        DB::commit();
    }

    public function storePermission($permissionName, $roles = null): bool
    {
        DB::beginTransaction();
        $permission = Permission::create(['name' => $permissionName]);
        if (!is_null($roles)) {
            $roles = Role::whereIn('id', $roles)->get();
            if (count(array_keys($roles->groupBy('guard_name')->toArray())) > 1) {
                return false;
            }
            foreach ($roles as $role) {
                $role->givePermissionTo($permission);
            }
        }
        DB::commit();

        return true;
    }

    public function attachPermissionToRole($role, $permissions): void
    {
        $role->givePermissionTo($permissions);
    }

    public function detachPermissionToRole($role, $permissions, $all = null): void
    {
        is_null($all) ? $role->permissions()->detach($permissions)
            : $role->syncPermissions([]);
    }

    public function getRolePermission($role)
    {
        return Permission::whereNotIn('id', $role->permissions()->pluck('id')->toArray())
            ->where('guard_name', $this->guard)->get();
    }

    public function getAllRolesPermissions($guard = null): array
    {
        $roles = Role::with('permissions')->orderBy('id', 'DESC')->get();
        $groupedRoles = !is_null($guard) ? $roles->where('guard_name', $guard) : $roles->groupBy('guard_name');
        $availableGuards = array_intersect(array_unique($roles->pluck('guard_name')->toArray()),
            array_keys(config('auth')['guards']));
        $availableGuards = array_intersect_key(FeeType::GUARD_TYPE_CORRESPOND,
            array_intersect_key(array_flip($availableGuards), FeeType::GUARD_TYPE_CORRESPOND));
        $permissions = Permission::orderBy('id', 'DESC')->get();

        return [
            'roles' => $groupedRoles,
            'permissions' => $permissions,
            'guards' => $availableGuards
        ];
    }

    public function userRolesPermissions($userCrud = false): array
    {
        if ($userCrud) {
            $userRoles = $this->user->roles->pluck('id')->toArray();
            $userRolesRelatePermissions = DB::table('role_has_permissions')->whereIn('role_id', $userRoles)->get()->pluck('permission_id')->toArray();
            $dontBelongRoles = Role::whereNotIn('id', $userRoles)->where('guard_name', $this->guard)->orderBy('id', 'DESC')->get();
            $userPermissions = $this->user->permissions->pluck('id')->toArray();
            $userPermissions = array_unique(array_merge($userPermissions, $userRolesRelatePermissions));
            $dontBelongPermissions = Permission::whereNotIn('id', $userPermissions)->where('guard_name', $this->guard)->orderBy('id', 'DESC')->get();

            return [
                'dontBelongRoles' => $dontBelongRoles,
                'dontBelongPermissions' => $dontBelongPermissions
            ];
        } else {
            return [
                'roles' => Role::where('guard_name', $this->guard)->orderBy('id', 'DESC')->get(),
                'permissions' => Permission::where('guard_name', $this->guard)->orderBy('id', 'DESC')->get()
            ];
        }

    }

}
