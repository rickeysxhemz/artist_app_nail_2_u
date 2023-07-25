<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    // public function Bookings()
    // {
    //     return $this->hasMany(Booking::class);
    // }

    public function ServiceBooking(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'booking_services', 'booking_id');
    }

    public function User()
    {
        return $this->belongsTo(User::class);
    }
}
