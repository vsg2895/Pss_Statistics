<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceProvider\SaveUser;
use App\Models\ServiceProviderUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ServiceProviderUserController extends Controller
{
    public function store(SaveUser $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($request->password);
        ServiceProviderUser::create($data);
        return redirect()->route('admin.users.index', ['#service-provider-users'])
            ->with(['success' => __('Provider User created successfully')]);
    }

    public function edit(ServiceProviderUser $serviceProviderUser)
    {
        return response()->json(['user' => $serviceProviderUser]);
    }

    public function update(SaveUser $request, ServiceProviderUser $serviceProviderUser)
    {
        $serviceProviderUser->update($request->validated());
        return redirect()->route('admin.users.index', ['#service-provider-users'])
            ->with(['success' => __('Provider User updated successfully')]);

    }

    public function destroy(ServiceProviderUser $serviceProviderUser)
    {
        $serviceProviderUser->delete();
        return redirect()->route('admin.users.index', ['#service-provider-users'])
            ->with(['success' => __('Provider User deleted successfully')]);

    }

}
