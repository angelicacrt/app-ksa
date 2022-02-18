<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApList extends Model
{
    use HasFactory;
    protected $guarded = [
        'id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function orderHead(){
        return $this->belongsTo(OrderHead::class, 'order_id');
    }

    public function apListDetail(){
        return $this->hasMany(ApListDetail::class, 'aplist_id');
    }
}
