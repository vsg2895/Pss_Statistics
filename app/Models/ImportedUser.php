<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ImportedUser extends Authenticatable
{
    protected $guarded = [];

    public function scopeLiveagentUsers()
    {
        return $this->where('liveagent_id', '<>', null);
    }

    public function scopeServitUsers()
    {
        return $this->where('servit_id', '<>', null);
    }

    public function getMainPointAttribute()
    {
        return $this->agent_point ?: Setting::where('slug', 'main_point')->first()->value;
    }

    //------------- - - relations - - --------------
    public function calls(): HasMany
    {
        return $this->hasMany(Call::class, 'agent_id', 'servit_id');
    }

    public function attachment(): MorphOne
    {
        return $this->morphOne(Attachment::class, 'attachable');
    }

    public function latestAgentLog(): HasOne
    {
        return $this->hasOne(AgentLog::class, 'agent_id', 'servit_id')->latestOfMany();
    }
    //------------- - - /relations - - --------------

}
