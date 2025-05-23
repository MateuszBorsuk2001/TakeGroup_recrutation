<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Serie;
use Illuminate\Http\Request;

class SerieController extends Controller
{
    public function index(Request $request)
    {
        $language = $request->input('language', 'en');
        $perPage = $request->input('per_page', 15);
        $genreId = $request->input('genre');
        
        $query = Serie::query()->with('genres');
        
        if ($genreId) {
            $query->whereHas('genres', function($q) use ($genreId) {
                $q->where('genres.id', $genreId);
            });
        }
        
        $series = $query->paginate($perPage);
        
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
    
    public function show(Request $request, $id)
    {
        $language = $request->input('language', 'en');
        $serie = Serie::with('genres')->findOrFail($id);
        
        return response()->json([
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
        ]);
    }
}
