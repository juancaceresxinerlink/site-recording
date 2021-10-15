<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use \Log;

class deleteRecording implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $recordingPath;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($recordingPath)
    {
        //
        $this->recordingPath = $recordingPath;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $recordingPath = $this->recordingPath;
        Log::debug("El recording path es ".$recordingPath);
        //exec("D:\Codigos\Codigos\XinerLink\laravel-crud\docker\deleteRecording.py {$recordingPath} ");
        //PRODUCTIVO
        exec("python3.8 /deleteRecording.py  {$recordingPath} ");
        
    }
}
