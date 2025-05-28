<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FetchTmdbData extends Command
{
    protected $signature = 'tmdb:fetch';
    protected $description = 'Fetch all data from TMDB API and store in database';

    public function handle()
    {
        $this->info('Starting to fetch data from TMDB API...');

        $this->call('tmdb:fetch-genres');
        $this->call('tmdb:fetch-movies');
        $this->call('tmdb:fetch-series');

        $this->info('Data fetching completed!');
        return 0;
    }
}
