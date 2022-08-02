<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class LanguageController extends Controller
{
    private const LOCALES = ['en', 'sw'];

    public function setLocale($locale): RedirectResponse
    {
        $locale = in_array($locale, self::LOCALES) ? $locale : 'en';
        return back()->cookie("locale", $locale, 45000);
    }
}
