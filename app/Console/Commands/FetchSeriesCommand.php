<?php

namespace App\Console\Commands;

use App\Models\Genre;
use App\Models\Serie;
use App\Models\Translation;
use App\Services\TmdbApiService;
use Illuminate\Support\Facades\DB;

class FetchSeriesCommand extends AbstractFetchCommand
{
    protected $signature = 'tmdb:fetch-series {count=10 : Number of series to fetch}';
    protected $description = 'Fetch TV series from TMDB API and store in database';

    public function handle()
    {
        $this->info('Fetching series...');
        $totalSeries = $this->argument('count');
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
        return 0;
    }
}