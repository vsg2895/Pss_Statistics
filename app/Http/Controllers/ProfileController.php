<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateImage;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\PasswordRequest;
use App\Models\Attachment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit');
    }

    public function update(ProfileRequest $request)
    {
        auth()->user()->update($request->all());

        return back()->withSuccess(__('Profile successfully updated.'));
    }

    public function password(PasswordRequest $request)
    {
        auth()->user()->update(['password' => Hash::make($request->get('password'))]);

        return back()->withSuccess(__('Password successfully updated.'));
    }

    public function updateImage(UpdateImage $request)
    {
        $isAdmin = auth()->guard('web')->check();
        $fileName = $request->image->getClientOriginalName();

        //delete previous image
        $oldAttachment = auth()->user()->attachment;
        if ($oldAttachment) {
            Storage::disk('public')->delete(str_replace('storage/', '', $oldAttachment->path));
        }

        //store new image
        $filePath = $request->image->storeAs($isAdmin ? '/images/profile/user' : '/images/profile/agent', Str::random(10) . $fileName, 'public');
        $attachableModel = $isAdmin ? 'App\Models\User' : 'App\Models\ImportedUser';
        Attachment::updateOrCreate([
            'attachable_id' => auth()->id(),
            'attachable_type' => $attachableModel,
        ], [
            'name' => $fileName,
            'path' => 'storage/' . $filePath,
        ]);

        return back()->with('success', __('Profile image updated successfully.'));
    }
}
