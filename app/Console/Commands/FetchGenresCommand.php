<?php

namespace App\Console\Commands;

use App\Models\Genre;
use App\Models\Translation;
use App\Services\TmdbApiService;
use Illuminate\Support\Facades\DB;

class FetchGenresCommand extends AbstractFetchCommand
{
    protected $signature = 'tmdb:fetch-genres';
    protected $description = 'Fetch genres from TMDB API and store in database';

    public function handle()
    {
        $this->info('Fetching genres...');
        
        $genresData = $this->tmdbService->getGenres();
        if (!$genresData || empty($genresData['genres'])) {
            $this->error('Failed to fetch genres data');
            return 1;
        }

        foreach ($genresData['genres'] as $genreData) {
            DB::transaction(function () use ($genreData) {
                $genre = Genre::updateOrCreate(
                    ['tmdb_id' => $genreData['id']],
                    ['tmdb_id' => $genreData['id']]
                );

                foreach ($this->languages as $language) {
                    $langData = $this->tmdbService->getGenres($language);
                    if (!$langData || empty($langData['genres'])) continue;
                    
                    $genreLang = collect($langData['genres'])->firstWhere('id', $genreData['id']);
                    if ($genreLang) {
                        Translation::updateOrCreate(
                            [
                                'trans_type' => Genre::class,
                                'trans_id' => $genre->id,
                                'language' => $language,
                                'field' => 'name',
                            ],
                            ['value' => $genreLang['name']]
                        );
                    }
                }
            });
        }
        
        $this->info('All genres fetched!');
        return 0;
    }
}