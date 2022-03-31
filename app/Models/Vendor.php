<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\Main_Category;

class Vendor extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'vendors';

    protected $fillable = [
        'name', 'logo', 'mobile', 'address', 'email', 'password', 'active', 'category_id', 'created_at', 'updated_at'
    ];

    protected $hidden = ['category_id', 'password'];

    public function scopeActive ($query) {
        return $query -> where('active',1);
    }

    public function scopeSelection($query) {
        return $query -> select('id', 'category_id', 'name', 'email', 'address', 'logo', 'mobile', 'active');
    }

    public function getActive() {
        return $this -> active == 1 ? 'مفعل ' : 'غير مغعل ';
    }

    public function getLogoAttribute($val) {
        return ($val !== null) ? asset('assets/' . $val) : '';
    }

    public function setPasswordAttribute($password)
    {
        if (!empty($password)) {
            $this->attributes['password'] = bcrypt($password);
        }
    }

    // the relation one category has many vendors , and one vender belong to one category
    public function main_category_rel() {
        return $this -> belongsTo(Main_Category::class,'category_id', 'id');
    } 

}
