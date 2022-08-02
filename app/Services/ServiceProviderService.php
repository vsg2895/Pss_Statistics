<?php

namespace App\Services;

use App\Models\AgentLog;
use App\Models\Attachment;
use App\Models\Fee;
use App\Models\FeeType;
use App\Models\ServiceProvider;
use App\Models\ServiceProviderSettings;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceProviderService extends BaseService
{
    public function deleteAttachment($attachment, $document = true, $all = false): void
    {
        DB::beginTransaction();
        if ($document) {
            $path = $all ? Attachment::find($attachment[0])->path
                : $attachment->path;
//            dd(str_replace('storage/', '', $path));
            Storage::delete(str_replace('storage/', '', $path));
        }
        !is_array($attachment) ? $attachment->delete() : Attachment::whereIn('id', $attachment)->delete();
        DB::commit();
    }

    public function uploadAttachment($attachment, $fileType, $filePath = false, $document = true, $all = false, $provider = null): void
    {
        $serviceProviders = ServiceProvider::all()->pluck('id')->toArray();
        $inserted = [];
        if ($document) {
            $name = $attachment->getClientOriginalName();
            $savePath = $filePath;
            $link = $attachment->storeAs($savePath . '/', Str::random(10) . $name);
        } else {
            $link = multiple_replace_youtube(FeeType::YOUTUBE_REPLACED_EMBED_URL, $attachment);
            $name = Str::random(10);
        }
        if ($all) {
            foreach ($serviceProviders as $provider) {
                $inserted[] = [
                    'attachable_id' => $provider,
                    'attachable_type' => 'App\Models\ServiceProvider',
                    'name' => $name,
                    'path' => $document ? 'storage/' . $link : $link,
                    'type' => $fileType,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ];
            }
            Attachment::insert($inserted);
        } else {
            $provider->attachments()->create([
                'name' => $name,
                'path' => $document ? 'storage/' . $link : $link,
                'type' => $fileType
            ]);
        }


    }

    public function getShowData($start, $end, $serviceProvider, $cdr, $updateDateFeesService)
    {
        $companies = $serviceProvider->companies->pluck('company_id')->toArray();
        $fees = Fee::getProviderCustom($serviceProvider->id);
        $companiesDepartment = $serviceProvider->companies()->with('departments')->get();
        $departmentIds = [];
        $companiesDepartment->pluck('departments')->map(function ($el) use (&$departmentIds) {
            $el->map(function ($department) use (&$departmentIds) {
                $departmentIds[] = $department->department_id;
            });
        });
        $monthStart = $start ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $today = $end ?? Carbon::now()->format('Y-m-d');
        $cdr->setParams($companies, 'provider_companies', $monthStart, $today);
        $updateDateFeesService->setParams($companies, 'update_provider', $start, $end);

        return [
            'fees' => $fees,
            'companies' => $companies,
            'departmentIds' => $departmentIds,
        ];
    }

    public function storeProvider($validated): void
    {
        DB::beginTransaction();
        $sp = ServiceProvider::create($validated);
        ServiceProviderSettings::storeDefaultSettings($sp->id);
        DB::commit();
    }
}
