<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LastUpdate extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'last_update';
}
