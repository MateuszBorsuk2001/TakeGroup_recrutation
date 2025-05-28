<?php

namespace App\Console\Commands;

use App\Services\TmdbApiService;
use Illuminate\Console\Command;

abstract class AbstractFetchCommand extends Command
{
    protected $tmdbService;
    protected $languages = ['en', 'pl', 'de'];

    public function __construct(TmdbApiService $tmdbService)
    {
        parent::__construct();
        $this->tmdbService = $tmdbService;
    }

    abstract public function handle();
}