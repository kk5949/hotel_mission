<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'address','room'
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function getReservationCountsAttribute()
    {
        return $this->reservations
            ->groupBy('step')
            ->map(function ($group) {
                return $group->count();
            })
            ->toArray();
    }

}
