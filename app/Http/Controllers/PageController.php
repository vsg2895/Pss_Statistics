<?php

namespace App\Http\Controllers;

use App\Models\Setting;

class PageController extends Controller
{
    public function index($page)
    {
        $settings = Setting::whereIn('slug', [$page, 'footer_text'])->pluck('value', 'slug')->toArray();

        return view('external-access.pages.'.$page, [
            'variable' => $settings[$page],
            'footerText' => $settings['footer_text']
        ]);
    }
}
