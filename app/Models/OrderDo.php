<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDo extends Model
{
    use HasFactory;
    protected $guarded = [
        'id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function item_requested(){
        return $this->belongsTo(Item::class, 'item_requested_id', 'id');
    }

    public function item_requested_from(){
        return $this->belongsTo(Item::class, 'item_requested_from_id', 'id');
    }
}
