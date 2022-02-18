<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class formclaims extends Model
{
    protected $table = "formclaim";
    use HasFactory;
    protected $guarded = [
        'id'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    Protected $hidden =['header_id','user_id','created_at','updated_at','tgl_insiden', 'tgl_formclaim' 
    ,'name', 'no_FormClaim','barge', 'tugBoat' ,'TSI_barge','TSI_TugBoat','incident','surveyor' , 'mata_uang_TSI'];
   
    
}
 