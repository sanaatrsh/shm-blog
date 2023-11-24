<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'image_url',
    ];

    protected static function booted()
    {
        static::creating(function (Article $article) {
            $article->slug = Article::make_slug($article->title);
        });
    }

    public function getRouteKeyName(){
        return 'slug';
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return '';
        }
        if (Str::startsWith($this->image, ['http://', 'https://'])) {

            return $this->image;
        }
        return asset('storage/' . $this->image);
    }

   public static function make_slug($string = null, $separator = "-") {
        if (is_null($string)) {
            return "";
        }
    
        $string = trim($string);
        $string = mb_strtolower($string, "UTF-8");;
        $string = preg_replace("/[^a-z0-9_\sءاأإآؤئبتثجحخدذرزسشصضطظعغفقكلمنهويةى]#u/", "", $string);
        $string = preg_replace("/[\s-]+/", " ", $string);
        $string = preg_replace("/[\s_]/", $separator, $string);
    
        return $string;
    }
}
