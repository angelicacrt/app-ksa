<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class documentrpk extends Model
{
    use HasFactory;
    protected $table = "rpkdocuments";
    protected $dates = ['created_at'];
    protected $guarded = [
        'id'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
