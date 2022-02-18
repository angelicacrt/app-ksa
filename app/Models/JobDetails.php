<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobDetails extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    // Protected $fillable = ['user_id', 'jasa_id'];
    // Protected $hidden =['user_id','created_at','updated_at' , 'jasa_id'];
    Protected $hidden =['user_id','created_at','updated_at','lokasi' ,'job_State'
    , 'jasa_id' , 'cabang' , 'tugName' , 'bargeName' , 'supplier'];
}