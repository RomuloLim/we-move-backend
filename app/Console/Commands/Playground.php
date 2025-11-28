<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Operation\Models\{Student, StudentRequisition};

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
        StudentRequisition::factory()->count(10)->create();

        $a = StudentRequisition::first();
        dd($a->toArray());
    }
}
