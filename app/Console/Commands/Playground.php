<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Operation\Models\Document;
use Modules\Operation\Models\Route;
use Modules\Operation\Models\Stop;
use Modules\Operation\Models\StudentRequisition;

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
        $a = StudentRequisition::first();
            dd($a->toArray());
        // $docs = Document::factory()
        // // ->hasRequisitions(10)
        // ->count(3)
        // ->create([
        //     'student_id' => $a->student_id,
        // ]);

        // $docs->each(function ($doc) use ($a) {
        //     $a->documents()->attach($doc->id);
        // });
    }
}
