<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TranslationController extends Controller
{
    private string $transFile = '/resources/lang/sw.json';

    public function index()
    {
        if (file_exists(base_path() . $this->transFile)) {
            $transFileContent = File::get(base_path() . $this->transFile);
            $words = json_decode($transFileContent, true);

            return view('pages.admin.settings.translations', ['words' => $words]);
        } else {
            return back()->withError(__('Translation file not found.'));
        }
    }

    public function store(Request $request)
    {
        if ($request->keys && $request->values && count($request->keys) === count($request->values)) {
            $data = [];
            for ($i = 0; $i < count($request->keys); $i++) {
                $data[$request->keys[$i]] = $request->values[$i];
            }

            File::put(base_path() . $this->transFile, json_encode($data));

            return back()->withSuccess(__('Translations updated successfully'));
        } else {
            return back()->withError(__('Something went wrong'));
        }
    }

    private string $transFileSp = '/resources/lang/sw-sp.json';

    public function indexSp()
    {
        if (file_exists(base_path() . $this->transFileSp)) {
            $transFileContent = File::get(base_path() . $this->transFileSp);
            $words = json_decode($transFileContent, true);

            return view('pages.admin.settings.translations-sp', ['words' => $words]);
        } else {
            return back()->withError(__('Translation file not found.'));
        }
    }

    public function storeSp(Request $request)
    {
        if ($request->keys && $request->values && count($request->keys) === count($request->values)) {
            $data = [];
            for ($i = 0; $i < count($request->keys); $i++) {
                $data[$request->keys[$i]] = $request->values[$i];
            }

            File::put(base_path() . $this->transFileSp, json_encode($data));

            return back()->withSuccess(__('Translations updated successfully'));
        } else {
            return back()->withError(__('Something went wrong'));
        }
    }
}
