<?php

namespace Modules\Sensors\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Sensors\Models\SensorReading;

class SendAbnormalReading implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public SensorReading $reading ,
        public string $abnormalityType,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void {}
}
