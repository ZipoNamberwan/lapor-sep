<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sample extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function bs()
    {
        return $this->belongsTo(Bs::class, 'bs_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function commodities()
    {
        return $this->hasMany(Commodity::class, 'sample_id');
    }

    public function replacement()
    {
        return $this->belongsTo(Sample::class, 'sample_id');
    }

    public function replacing()
    {
        return $this->hasMany(Sample::class, 'sample_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
