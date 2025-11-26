<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Operation\Models\Document;
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
        $a = Document::latest()->first();

        dd($a->full_url);
    }
}
