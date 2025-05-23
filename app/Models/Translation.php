<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'trans_type',
        'trans_id',
        'language',
        'field',
        'value',
    ];

    public function trans()
    {
        return $this->morphTo();
    }
}
