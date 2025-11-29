<?php

namespace App\Console\Commands;

use Dom\Document;
use Illuminate\Console\Command;
use Modules\Operation\Models\{Document as ModelsDocument, StudentRequisition};

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
        dd(ModelsDocument::latest()->first()->toArray());
    }
}
