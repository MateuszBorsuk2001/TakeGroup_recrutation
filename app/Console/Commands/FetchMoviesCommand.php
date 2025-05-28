<?php

namespace App\Console\Commands;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\Translation;
use App\Services\TmdbApiService;
use Illuminate\Support\Facades\DB;

class FetchMoviesCommand extends AbstractFetchCommand
{
    protected $signature = 'tmdb:fetch-movies {count=50 : Number of movies to fetch}';
    protected $description = 'Fetch movies from TMDB API and store in database';

    public function handle()
    {
        $this->info('Fetching movies...');
        $totalMovies = $this->argument('count');
        $perPage = 20;
        $currentCount = 0;
        $page = 1;

        while ($currentCount < $totalMovies) {
            $moviesData = $this->tmdbService->getMovies($page);
            if (!$moviesData || empty($moviesData['results'])) {
                $this->error('Failed to fetch movies data on page ' . $page);
                break;
            }

            foreach ($moviesData['results'] as $movieData) {
                if ($currentCount >= $totalMovies) break;
                
                DB::transaction(function () use ($movieData) {
                    $movie = Movie::updateOrCreate(
                        ['tmdb_id' => $movieData['id']],
                        [
                            'poster_path' => $movieData['poster_path'],
                            'backdrop_path' => $movieData['backdrop_path'],
                            'vote_average' => $movieData['vote_average'],
                            'vote_count' => $movieData['vote_count'],
                            'release_date' => $movieData['release_date'],
                        ]
                    );

                    if (!empty($movieData['genre_ids'])) {
                        $genreIds = Genre::whereIn('tmdb_id', $movieData['genre_ids'])->pluck('id');
                        $movie->genres()->sync($genreIds);
                    }

                    foreach ($this->languages as $language) {
                        $movieDetails = $this->tmdbService->getMovieDetails($movieData['id'], $language);
                        if (!$movieDetails) continue;
                        
                        if (!empty($movieDetails['title'])) {
                            Translation::updateOrCreate(
                                [
                                    'trans_type' => Movie::class,
                                    'trans_id' => $movie->id,
                                    'language' => $language,
                                    'field' => 'title',
                                ],
                                ['value' => $movieDetails['title']]
                            );
                        }
                        
                        if (!empty($movieDetails['overview'])) {
                            Translation::updateOrCreate(
                                [
                                    'trans_type' => Movie::class,
                                    'trans_id' => $movie->id,
                                    'language' => $language,
                                    'field' => 'overview',
                                ],
                                ['value' => $movieDetails['overview']]
                            );
                        }
                    }
                });
                
                $currentCount++;
                $this->output->write('.');
            }
            
            $page++;
        }
        
        $this->info("\nFetched {$currentCount} movies!");
        return 0;
    }
}