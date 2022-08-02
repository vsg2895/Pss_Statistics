<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceProvider extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function serviceProviderUsers(): HasMany
    {
        return $this->hasMany(ServiceProviderUser::class);
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class);
    }

    public function fixedFees(): HasMany
    {
        return $this->hasMany(FixedFees::class);
    }

    public function fixedFeesWithoutCompany(): HasMany
    {
        return $this->hasMany(FixedFees::class)->whereNull('fixed_fees.company_id');
    }
    public function scopeWithoutFixed($query)
    {
        return $query->doesntHave('fixedFeesWithoutCompany');
    }

    public function defaultFees(): HasMany
    {
        return $this->hasMany(ServiceProviderSettings::class);
    }

    public function getDefaultFeeBySlug($slug)
    {
        return $this->defaultFees()->where('slug', $slug)->pluck('value')->first();
    }

    public function announcement(): MorphOne
    {
        return $this->morphOne(Announcement::class, 'announcementable');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable')
            ->where('type', 'provider-file');
    }

    public function medias(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable')
            ->where('type', 'provider-media');
    }

    public function attachmentsForType($type): \Illuminate\Database\Eloquent\Collection
    {
        return $this->attachments()->where('type', $type)->get();
    }
}
