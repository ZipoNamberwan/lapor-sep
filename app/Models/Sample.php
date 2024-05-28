<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sample extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false;

    public function bs()
    {
        return $this->belongsTo(Bs::class, 'bs_id');
    }
}
