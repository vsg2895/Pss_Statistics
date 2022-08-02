<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        $user = User::create([
            'name' => 'Hardccccik Savaniccc',
            'email' => 'admillln@gffxxmail.com',
            'password' => bcrypt('123456')
        ]);

        $role = Role::create(['name' => 'Admin']);

        $permissions = Permission::pluck('id', 'id')->all();
//Add permission in role
        $role->syncPermissions($permissions);
//Add only permission in user
        $user->givePermissionTo([$permissions[6]]);
//Add only role in user
        $user->assignRole([$role->id]);
        DB::commit();
    }
}
