<?php

namespace App\Console\Commands;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\Serie;
use App\Models\Translation;
use App\Services\TmdbApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FetchTmdbData extends Command
{
    protected $signature = 'tmdb:fetch';
    protected $description = 'Fetch data from TMDB API and store in database';

    protected $tmdbService;
    protected $languages = ['en', 'pl', 'de'];

    public function __construct(TmdbApiService $tmdbService)
    {
        parent::__construct();
        $this->tmdbService = $tmdbService;
    }

    public function handle()
    {
        $this->info('Starting to fetch data from TMDB API...');

        $this->fetchGenres();
        $this->fetchMovies();
        $this->fetchSeries();

        $this->info('Data fetching completed!');
        return 0;
    }

    protected function fetchGenres()
    {
        $this->info('Fetching genres...');
        
        $genresData = $this->tmdbService->getGenres();
        if (!$genresData || empty($genresData['genres'])) {
            $this->error('Failed to fetch genres data');
            return;
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
    }

    protected function fetchMovies()
    {
        $this->info('Fetching movies...');
        $totalMovies = 50;
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
    }

    protected function fetchSeries()
    {
        $this->info('Fetching series...');
        $totalSeries = 10;
        $currentCount = 0;
        $page = 1;

        while ($currentCount < $totalSeries) {
            $seriesData = $this->tmdbService->getSeries($page);
            if (!$seriesData || empty($seriesData['results'])) {
                $this->error('Failed to fetch series data on page ' . $page);
                break;
            }

            foreach ($seriesData['results'] as $serieData) {
                if ($currentCount >= $totalSeries) break;
                
                DB::transaction(function () use ($serieData) {
                    $serie = Serie::updateOrCreate(
                        ['tmdb_id' => $serieData['id']],
                        [
                            'poster_path' => $serieData['poster_path'],
                            'backdrop_path' => $serieData['backdrop_path'],
                            'vote_average' => $serieData['vote_average'],
                            'vote_count' => $serieData['vote_count'],
                            'first_air_date' => $serieData['first_air_date'],
                        ]
                    );

                    if (!empty($serieData['genre_ids'])) {
                        $genreIds = Genre::whereIn('tmdb_id', $serieData['genre_ids'])->pluck('id');
                        $serie->genres()->sync($genreIds);
                    }

                    foreach ($this->languages as $language) {
                        $serieDetails = $this->tmdbService->getSerieDetails($serieData['id'], $language);
                        if (!$serieDetails) continue;
                        
                        if (!empty($serieDetails['name'])) {
                            Translation::updateOrCreate(
                                [
                                    'trans_type' => Serie::class,
                                    'trans_id' => $serie->id,
                                    'language' => $language,
                                    'field' => 'name',
                                ],
                                ['value' => $serieDetails['name']]
                            );
                        }
                        
                        if (!empty($serieDetails['overview'])) {
                            Translation::updateOrCreate(
                                [
                                    'trans_type' => Serie::class,
                                    'trans_id' => $serie->id,
                                    'language' => $language,
                                    'field' => 'overview',
                                ],
                                ['value' => $serieDetails['overview']]
                            );
                        }
                    }
                });
                
                $currentCount++;
                $this->output->write('.');
            }
            
            $page++;
        }
        
        $this->info("\nFetched {$currentCount} series!");
    }
}
