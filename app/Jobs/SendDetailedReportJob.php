<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use App\Mail\WaterQualityReportMail;
use Illuminate\Queue\SerializesModels;
use Modules\UsersAndTeams\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\WaterSources\Models\WaterQualityTest;

class SendDetailedReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected WaterQualityTest $test;
    protected User $recipient;

    /**
     * Create a new job instance.
     */
    public function __construct(WaterQualityTest $test, User $recipient)
    {
        $this->test = $test;
        $this->recipient = $recipient;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $email = new WaterQualityReportMail($this->test);
        Mail::to($this->recipient->email)->send($email);
    }
}
