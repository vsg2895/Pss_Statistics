<?php

namespace App\Http\Controllers\Api\ServiceProviderApi;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class AttachmentController extends Controller
{
    public function providerFiles(Attachment $fileId, Request $request)
    {
        try {
            $hashedToken = Str::replace('Bearer ', '', $request->header('authorization'));
            $token = PersonalAccessToken::findToken($hashedToken);
            $user = $token->tokenable;
            $issetFile = $user->serviceProvider->attachments()->where('id', $fileId->id)->first();
            if (is_null($issetFile)) {
                return response()->error('You do not have access to this resource', 403);
            }
            $path = str_replace('storage', '', $issetFile->path);
            $file = Storage::get($path);
            return response()->success('', $file, 200, ['Content-Type' => $path], false);
        } catch (FileNotFoundException $exception) {
            return response()->error($exception->getMessage(), 400);
        }


    }
}
