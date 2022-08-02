<?php

namespace App\Services;


use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AnnouncementService extends BaseService
{

    public function addRecord($data): void
    {
        $insert = [
            'author_id' => Auth::id(),
            'text' => $data['announcement'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];
        switch ($this->type) {
            case "company":
                $insert['announcementable_id'] = $this->company->id;
                $insert['announcementable_type'] = get_class($this->company);
                $this->company->announcement()
                    ->updateOrCreate([], $insert);
                break;
            case "provider":
                $insert['announcementable_id'] = $this->provider->id;
                $insert['announcementable_type'] = get_class($this->provider);
                $this->provider->announcement()
                    ->updateOrCreate([], $insert);
                break;

        }

    }
}
