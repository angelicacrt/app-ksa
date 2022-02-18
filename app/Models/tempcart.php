<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tempcart extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];
    // Protected $fillable = ['tgl_insiden', 'tgl_formclaim' , 'name', 'item' ,
    // 'jenis_incident','no_FormClaim',
    // 'barge','TSI_barge','TSI_Tugboat',
    // 'deductible','amount','surveyor',
    // 'tugBoat','incident','description'];
}
