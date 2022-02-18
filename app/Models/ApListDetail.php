<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApListDetail extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public function apList(){
        return $this->belongsTo(ApList::class, 'aplist_id');
    }
}
