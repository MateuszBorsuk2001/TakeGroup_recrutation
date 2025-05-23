<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    protected $fillable = ['tmdb_id'];

    public function movies()
    {
        return $this->belongsToMany(Movie::class);
    }

    public function series()
    {
        return $this->belongsToMany(Serie::class);
    }

    public function translations()
    {
        return $this->morphMany(Translation::class, 'trans');
    }

    public function getName($language = 'en')
    {
        return $this->getTranslation('name', $language);
    }

    protected function getTranslation($field, $language)
    {
        $translation = $this->translations()
            ->where('language', $language)
            ->where('field', $field)
            ->first();

        if (!$translation && $language !== 'en') {
            // Fallback to English if translation not found
            return $this->getTranslation($field, 'en');
        }

        return $translation ? $translation->value : null;
    }
}
