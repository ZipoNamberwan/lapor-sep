<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BsEdcod extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false;
    protected $table = 'bs_edcod';

    public function bs()
    {
        return $this->belongsTo(Bs::class, 'bs_id');
    }
}
