<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'contact_id', 'contact_id');
    }
}
