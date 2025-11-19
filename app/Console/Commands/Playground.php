<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Operation\Models\Route;
use Modules\Operation\Models\Stop;

class Playground extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'playground';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
//        Route::truncate();
//        Route::factory()->hasStops(5)->create();
        dd(Route::with('firstStop', 'lastStop')->withCount('stops as stops_amount')->first()->toArray());
    }
}
