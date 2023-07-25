<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    // public function service()
    // {
    //     return $this->belongsTo(Service::class);
    // }

    public function BookingService()
    {
        return $this->belongsToMany(Service::class, 'booking_services')->withPivot('service_id');
    }

    public function Transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function BookingLocation()
    {
        return $this->hasOne(BookingLocation::class);
    }
    
    public function Client()
    {
        return $this->belongsTo(User::class);
    }
    
    public function ScheduleBooking()
    {
        return $this->belongsToMany(Scheduler::class, 'scheduler_bookings')->withPivot('date');
    }
    
    public function messageOwner(): HasOneThrough
    {
        return $this->hasOneThrough(Message::class, User::class);
    }
}
