<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function index(Request $request)
    {
        $language = $request->input('language', 'en');
        
        $genres = Genre::all();
        
        $data = $genres->map(function ($genre) use ($language) {
            return [
                'id' => $genre->id,
                'tmdb_id' => $genre->tmdb_id,
                'name' => $genre->getName($language),
            ];
        });
        
        return response()->json([
            'data' => $data,
        ]);
    }
    
    public function show(Request $request, $id)
    {
        $language = $request->input('language', 'en');
        $genre = Genre::findOrFail($id);
        
        return response()->json([
            'id' => $genre->id,
            'tmdb_id' => $genre->tmdb_id,
            'name' => $genre->getName($language),
        ]);
    }
    
    public function movies(Request $request, $id)
    {
        $language = $request->input('language', 'en');
        $perPage = $request->input('per_page', 15);
        
        $genre = Genre::findOrFail($id);
        $movies = $genre->movies()->paginate($perPage);
        
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
    
    public function series(Request $request, $id)
    {
        $language = $request->input('language', 'en');
        $perPage = $request->input('per_page', 15);
        
        $genre = Genre::findOrFail($id);
        $series = $genre->series()->paginate($perPage);
        
        $data = $series->map(function ($serie) use ($language) {
            return [
                'id' => $serie->id,
                'tmdb_id' => $serie->tmdb_id,
                'name' => $serie->getName($language),
                'overview' => $serie->getOverview($language),
                'poster_path' => $serie->poster_path,
                'backdrop_path' => $serie->backdrop_path,
                'vote_average' => $serie->vote_average,
                'vote_count' => $serie->vote_count,
                'first_air_date' => $serie->first_air_date,
                'genres' => $serie->genres->map(function ($genre) use ($language) {
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
                'current_page' => $series->currentPage(),
                'last_page' => $series->lastPage(),
                'per_page' => $series->perPage(),
                'total' => $series->total(),
            ],
        ]);
    }
}
