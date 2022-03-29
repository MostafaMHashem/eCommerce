<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Main_Category extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'main_categories';

    protected $fillable = [
        'translation_lang', 'translation_of', 'name', 'slug', 'photo', 'active', 'created_at', 'updated_at',
    ];

    public function scopeActive ($query) {
        return $query -> where('active',1);
    }

    public function scopeSelection($query) {
        return $query -> select('id', 'translation_lang', 'name', 'slug', 'photo', 'active', 'translation_of');
    }

    public function getActive() {
        return $this -> active == 1 ? 'مفعل ' : 'غير مغعل ';
    }

    public function getPhotoAttribute($val) {
        return ($val !== null) ? asset('assets/' . $val) : '';
    }

    // the relation between the main category and the translation_of, and pass it as foreign key
    public function main_categories_rel() {
        return $this -> hasMany(self::class, 'translation_of');
    }
}
