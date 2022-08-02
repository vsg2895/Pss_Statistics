<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $guarded = [];

    public function bookingMeta()
    {
        return $this->hasOne(BookingMeta::class, 'booking_id', 'booking_id');
    }
}
