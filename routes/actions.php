<?php

use App\Models\BillingData;
use App\Models\Fee;
use App\Models\FeeType;
use App\Models\ServiceProviderSettings;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('max-calls', function() {
    $usIt = \App\Models\Call::select(DB::raw('COUNT(id) as total_calls'), DB::raw('DATE(started_at) as day'))
        ->whereRaw('DATE(started_at) >= ?', ['2021-01-01'])
        ->groupBy('day')->orderBy('total_calls', 'desc')->get()->toArray();

    dd($usIt);
});

Route::get('max-answered', function() {
    $usIt = \App\Models\Call::select(DB::raw('COUNT(id) as total_answered'),
        DB::raw('DATE(started_at) as day'),
//        DB::raw("(SELECT COUNT(id) AS total_answered FROM calls WHERE connected_at IS NOT NULL) as total_answered")
    )
        ->whereRaw('DATE(started_at) >= ?', ['2021-01-01'])
        ->whereNotNull('connected_at')
        ->groupBy('day')->orderBy('total_answered', 'desc')->get()->toArray();

    dd($usIt);
});

Route::get('test', function () {
//    $monthStart = Carbon::now()->startOfMonth()->format('Y-m-d');
//    $today = date('Y-m-d');
//
//    $ok = BillingData::select()->selectRaw("COUNT(id) as calls_count")->whereRaw('DATE(date) >= ?', [$monthStart])->whereRaw('DATE(date) <= ?', [$today])
//        ->where('status', 'AN')->groupBy('company_id')->pluck('calls_count', 'company_id')->toArray();


//    $feeWeCompany = Fee::getCompanyCustomAll()->where('company_id','=', 22)->pluck('fee', 'feeType.slug');
//    dd($feeWeCompany);
//   $add = ServiceProviderSettings::whereIn('slug', FeeType::FEE_TYPES_IN_INSERT)->select('id','service_provider_id', 'name', 'slug', 'value')
//        ->with('feeType')->orderBy('id')->get();
////   dd($add);
//   dd($add->where('service_provider_id',2));
});

Route::get('add_tags_by_names', function () {
    $companyNames = ["Badrumsgruppen i Alvik AB", "Djurgården Hockey AB", "Hotel Diplomat AB", "Lannen Tractors AB", "Liera Consulting AB", "Persson Andersson Bilservice AB", "Picadeli AB", "Rena Fönster Sverige AB", "Sverek AB", "Yrselcenter Geisler Medical AB", "1,6 Miljonerklubben", "Audionomkliniken Sverige AB", "Autoelektra Stjärnservice", "Autoservice huddinge AB", "Badrumsbesiktningar Sverige AB", "Byggbolaget i Vetlanda AB", "D-Link Aktiebolag", "Design by Cement Sweden AB", "Fria Bröd AB", "Fueltech Sweden AB", "Grays American Stores AB", "Gösta Hurtigs bil AB", "Hedda Care AB", "Hedman Naprapater AB", "Hem Gallerian AB", "Installationsservice Niklas Eriksson AB", "Jesper Markusson Bil AB", "Juristfirman Drougge & Partners AB", "Järfälla VA & Byggentreprenad AB", "Karlskrona Bilcenter", "Linds & Källmans", "Maintrac AB", "Martinez Totalentreprenad AB", "MOHV Mäklarsupport AB", "Nordr Sverige AB", "Norra Västerbottens Motor AB", "Servicelösningar i Sverige AB", "Siru Mobile AB", "Sjöbergs Bilverkstad Aktiebolag", "Småländska Bil Vetlanda", "STS Trafikskola", "Städbolag Ett i Täby AB", "Systrarna ODH's hemtjänst AB", "Tyresö Gummi & Motor AB", "Ventilation & Kylservice Norr AB", "Wafab bil AB", "Winpos Sweden AB", "Wiraya Solutions AB", "Advokaterna Melin & Fagerberg Gbg", "Advokatfirman Inter", "AT Installation Aktiebolag", "Autoservice Åhus AB", "Burlin Motor AB", "Burlin Motor Piteå", "Burlin Motor Umeå", "DR. Oetker", "Effektiv Isolering i Sverige AB", "GHP Medicinskt Centrum AB", "Hummeltorp Sverige AB", "Hus-Skötsel PM AB", "Hägersten Svets & VVS AB", "Integrera Information Norden AB", "IQVIA Solutions Sweden AB", "Lexman i Stockholm AB", "Nordiskt Centrum för Kirurgi AB", "Omsorgscompagniet AB", "POC Sweden AB", "Q-Linea AB", "Quickplay Sport", "Relek Produktion AB", "Rörledningsfirma B Erlandsson AB", "Stenbergs Bil Katrineholm AB", "Stockholms Glas & Solskydd AB", "Veidekke Eiendom", "WM Koncernen Mälardalen AB", "WM Koncernen Mälardalen AB", "Holmlunds Bil AB", "Rune Odelius Bil AB", "Svenstigs Bil AB/ePP", "Ulf Nylanders Bilar AB", "Molin Bil AB", "Landrins Bil", "Landrins Bil AB", "Mitsubishi Center Malmö AB", "Olofsson Auto AB", "RH Bilservice", "Veho Bil Gävle", "Veho Bil Karlstad", "Veho Bil Malmö", "Veho Bil Sverige AB", "Veho Bil Örebro", "Veho bil Berglunda", "Veho Bil Västerås", "Barkarby Elinstallationer AB", "BMD Autoservice AB", "ECO Center Bilverkstad Karlskrona", "Elon Hofors", "Global Tak AB", "Hustvättarna i Sverige AB", "Librobäck Fordonsteknik", "Maskincentrum i Bockara", "Parking Partner", "Reaction AB", "Rosenfors Ryggrehab", "SafeAid AB", "Savtec Aktiebolag", "Street Performance Handelsbolag", "Säkerhetsutbildningar", "UNICEF Sverige", "Eksjö Bilaffär AB"];
    $companyNames = array_unique($companyNames);
    $taggableType = 'App\Models\Company';
    $missing = [];
    $tagId = 6;
    $data = [];


    foreach ($companyNames as $name)
    {
        $company = \App\Models\Company::where('name', $name)->first();

        if ($company) {
            $data[] = [
                'tag_id' => $tagId,
                'taggable_id' => $company->id,
                'taggable_type' => $taggableType,
            ];
        } else {
            $missing[] = $name;
        }
    }

    \App\Models\Taggables::insert($data);
    \Illuminate\Support\Facades\Log::info('add_tags_by_names: missing comanies: ' . json_encode($missing));
    dd($missing);
});
