<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'email',
    ];

    protected $appends = [
        'absolute_cv_url',
        'absolute_image_url'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function identities()
    {
        return $this->hasMany(SocialIdentity::class);
    }

    public function reviews()
    {
        return $this->hasMany(Rating::class, 'artist_id');
    }

    public function setting()
    {
        return $this->hasOne(Setting::class, 'user_id');
    }

    public function jobs()
    {
        return $this->hasMany(Booking::class, 'artist_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'artist_id');
    }

    public function transections()
    {
        return $this->hasMany(Transaction::class, 'receiver_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'client_id');
    }

    public function getAbsoluteCvUrlAttribute()
    {
        //return url($this->attributes['cv_url']);
    }

    public function getAbsoluteImageUrlAttribute()
    {
        return url($this->attributes['image_url']);
    }
}
