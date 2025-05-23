<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TmdbApiService
{
    protected $baseUrl;
    protected $apiKey;
    protected $supportedLanguages = ['en', 'pl', 'de'];

    public function __construct()
    {
        $this->baseUrl = env('TMDB_API_URL', 'https://api.themoviedb.org/3');
        $this->apiKey = env('TMDB_API_KEY');
    }

    public function getMovies($page = 1)
    {
        return $this->makeRequest('/movie/popular', [
            'page' => $page,
        ]);
    }

    public function getMovieDetails($movieId, $language = 'en')
    {
        return $this->makeRequest("/movie/{$movieId}", [
            'language' => $language,
        ]);
    }

    public function getSeries($page = 1)
    {
        return $this->makeRequest('/tv/popular', [
            'page' => $page,
        ]);
    }

    public function getSerieDetails($serieId, $language = 'en')
    {
        return $this->makeRequest("/tv/{$serieId}", [
            'language' => $language,
        ]);
    }

    public function getGenres($language = 'en')
    {
        $movieGenres = $this->makeRequest('/genre/movie/list', [
            'language' => $language,
        ]);
        
        $tvGenres = $this->makeRequest('/genre/tv/list', [
            'language' => $language,
        ]);
        
        // Merge and remove duplicates
        $allGenres = collect(array_merge(
            $movieGenres['genres'] ?? [],
            $tvGenres['genres'] ?? []
        ))->unique('id')->values()->all();
        
        return ['genres' => $allGenres];
    }

    protected function makeRequest($endpoint, $params = [])
    {
        try {
            $response = Http::get($this->baseUrl . $endpoint, array_merge([
                'api_key' => $this->apiKey,
            ], $params));

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('TMDB API Error', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('TMDB API Exception', [
                'endpoint' => $endpoint,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function getSupportedLanguages()
    {
        return $this->supportedLanguages;
    }
}