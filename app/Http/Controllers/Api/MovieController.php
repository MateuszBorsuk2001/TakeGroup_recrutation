<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index(Request $request)
    {
        $language = $request->input('language', 'en');
        $perPage = $request->input('per_page', 15);
        $genreId = $request->input('genre');

        $query = Movie::query()->with('genres');
        
        if ($genreId) {
            $query->whereHas('genres', function($q) use ($genreId) {
                $q->where('genres.id', $genreId);
            });
        }
        
        $movies = $query->paginate($perPage);
        
        $data = $movies->map(function ($movie) use ($language) {
            return [
                'id' => $movie->id,
                'tmdb_id' => $movie->tmdb_id,
                'title' => $movie->getTitle($language),
                'overview' => $movie->getOverview($language),
                'poster_path' => $movie->poster_path,
                'backdrop_path' => $movie->backdrop_path,
                'vote_average' => $movie->vote_average,
                'vote_count' => $movie->vote_count,
                'release_date' => $movie->release_date,
                'genres' => $movie->genres->map(function ($genre) use ($language) {
                    return [
                        'id' => $genre->id,
                        'name' => $genre->getName($language),
                    ];
                }),
            ];
        });
        
        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $movies->currentPage(),
                'last_page' => $movies->lastPage(),
                'per_page' => $movies->perPage(),
                'total' => $movies->total(),
            ],
        ]);
    }
    
    public function show(Request $request, $id)
    {
        $language = $request->input('language', 'en');
        $movie = Movie::with('genres')->findOrFail($id);
        
        return response()->json([
            'id' => $movie->id,
            'tmdb_id' => $movie->tmdb_id,
            'title' => $movie->getTitle($language),
            'overview' => $movie->getOverview($language),
            'poster_path' => $movie->poster_path,
            'backdrop_path' => $movie->backdrop_path,
            'vote_average' => $movie->vote_average,
            'vote_count' => $movie->vote_count,
            'release_date' => $movie->release_date,
            'genres' => $movie->genres->map(function ($genre) use ($language) {
                return [
                    'id' => $genre->id,
                    'name' => $genre->getName($language),
                ];
            }),
        ]);
    }
}
