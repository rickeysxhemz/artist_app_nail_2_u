<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    use HasFactory;

    public function DealServices()
    {
        return $this->belongsToMany(Service::class, 'deal_services')->withPivot('service_id', 'deal_id');
    }
}
