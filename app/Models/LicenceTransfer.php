<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenceTransfer extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function licence(){
       return $this->belongsTo(Licence::class);
    }

    public function old_company()
    {
       return $this->belongsTo(Company::class,'licence_transfer','old_company_id','licence_id')->withPivot('status','slug','date');
    }
}
