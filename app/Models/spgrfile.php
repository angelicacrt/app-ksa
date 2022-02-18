<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class spgrfile extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];
    protected $table = "spgrfiles";
    public function user(){
        return $this->belongsTo(User::class);
    }
}
