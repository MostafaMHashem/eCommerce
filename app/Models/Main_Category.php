<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\Vendor;
use App\Observers\MainCategoryObserver;
use phpDocumentor\Reflection\Types\Parent_;

class Main_Category extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'main_categories';

    protected $fillable = [
        'translation_lang', 'translation_of', 'name', 'slug', 'photo', 'active', 'created_at', 'updated_at',
    ];

    // overwrite the boot() method in the Model class ,to make it observe the MainCategoryObserver 
    protected static function boot()
    {
        Parent::boot();
        Main_Category::observe(MainCategoryObserver::class);
    }

    // scope is reserved word so we the call the method with active()
    public function scopeActive ($query) {
        return $query -> where('active',1);
    }

    // scope is reserved word so we the call the method with selection()
    public function scopeSelection($query) {
        return $query -> select('id', 'translation_lang', 'name', 'slug', 'photo', 'active', 'translation_of');
    }

    public function getActive() {
        return $this -> active == 1 ? 'مفعل ' : 'غير مغعل ';
    }

    // get & attribute is reserved word all we want is the 'photo' between them 
    public function getPhotoAttribute($val) {
        return ($val !== null) ? asset('assets/' . $val) : '';
    }

    // the relation between the main category and the translation_of, and pass it as foreign key
    public function main_categories_rel() {
        return $this -> hasMany(self::class, 'translation_of');
    }

    // the relation one category has many vendors , and one vender belong to one category
    public function vendors() {
        return $this -> hasMany(Vendor::class,'category_id', 'id');
    } 
}
