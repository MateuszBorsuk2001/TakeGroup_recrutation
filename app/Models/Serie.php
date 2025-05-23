<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    use HasFactory;

    protected $fillable = [
        'tmdb_id',
        'poster_path',
        'backdrop_path',
        'vote_average',
        'vote_count',
        'first_air_date',
    ];

    protected $casts = [
        'first_air_date' => 'date',
    ];

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    public function translations()
    {
        return $this->morphMany(Translation::class, 'trans');
    }

    public function getName($language = 'en')
    {
        return $this->getTranslation('name', $language);
    }

    public function getOverview($language = 'en')
    {
        return $this->getTranslation('overview', $language);
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
