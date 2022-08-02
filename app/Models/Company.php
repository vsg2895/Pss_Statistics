<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Company extends Model
{
    protected $guarded = [];

    public function calls(): HasMany
    {
        return $this->hasMany(Call::class, 'company_number', 'company_id');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function getTagIdsAttribute()
    {
        return $this->tags->pluck('id')->toArray();
    }

    public function serviceProvider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class);
    }

    public function fixedFees(): HasMany
    {
        return $this->hasMany(FixedFees::class, 'company_id', 'company_id')->whereNull('fixed_fees.service_provider_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'company_id', 'company_id');
    }

    public function departments()
    {
        return $this->hasMany(Department::class, 'company_id', 'company_id');
    }

    //---------------- - - scopes - - ---------------
    public function scopeWithoutProviderFixed($query)
    {
        return $query->whereNull('service_provider_id')->doesntHave('fixedFees');
    }

    public function chats()
    {
        return $this->hasManyThrough(DailyChat::class, Department::class, 'company_id', 'department_id', 'company_id', 'department_id');
    }

    public function getChatsByDate($start, $end)
    {
        return $this->chats()->whereBetween('date', [$start, $end])
            ->with('department')->with('user');
    }

    public function announcement(): MorphOne
    {
        return $this->morphOne(Announcement::class, 'announcementable');
    }
}
