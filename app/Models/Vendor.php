<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use Notifiable;

    protected $table = 'vendors';

    protected $fillable = [
        'name', 'mobile', 'password', 'address', 'email', 'logo', 'category_id', 'active', 'created_at', 'updated_at'
    ];
    protected $hidden = ['category_id', 'password'];

    public function scopeActive($query)
    {

        return $query->where('active', 1);
    }

    public function getActive(){
      return $this ->active == 1 ? 'مفعل' : 'غير مفعل';
    }

    public function getLogoAttribute($val)
    {
        return ($val !== null) ? asset('assets/' . $val) : "";

    }


    public function scopeSelection($query)
    {
        return $query->select('id', 'category_id', 'active', 'name', 'address', 'email', 'logo', 'mobile');
    }

    public function category()
    {

        return $this->belongsTo('App\Models\Main_Category', 'category_id', 'id');
    }
}
