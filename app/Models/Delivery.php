<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_pengiriman', 'courier_id', 'school_id', 'status',
        'total_portions', 'notes', 'started_at', 'arrived_at',
        'completed_at', 'delivery_date'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'arrived_at' => 'datetime',
        'completed_at' => 'datetime',
        'delivery_date' => 'date',
    ];

    public static $statusLabels = [
        'dimasak'        => 'Sedang Dimasak',
        'dikemas'        => 'Sedang Dikemas',
        'dalam_perjalanan' => 'Dalam Perjalanan',
        'sudah_sampai'   => 'Sudah Sampai',
        'diterima_guru'  => 'Diterima Guru',
        'diterima_murid' => 'Diterima Murid',
        'selesai'        => 'Selesai',
    ];

    public static $statusColors = [
        'dimasak'          => 'yellow',
        'dikemas'          => 'orange',
        'dalam_perjalanan' => 'blue',
        'sudah_sampai'     => 'green',
        'diterima_guru'    => 'teal',
        'diterima_murid'   => 'purple',
        'selesai'          => 'gray',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::$statusColors[$this->status] ?? 'gray';
    }

    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function trackings()
    {
        return $this->hasMany(DeliveryTracking::class)->orderBy('recorded_at', 'desc');
    }

    public function latestTracking()
    {
        return $this->hasOne(DeliveryTracking::class)->latestOfMany('recorded_at');
    }

    public function confirmations()
    {
        return $this->hasMany(Confirmation::class);
    }

    public static function generateKode(): string
    {
        $prefix = 'MBG';
        $date = now()->format('Ymd');
        $last = static::whereDate('created_at', today())->count() + 1;
        return $prefix . $date . str_pad($last, 4, '0', STR_PAD_LEFT);
    }
}
