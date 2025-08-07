<?php

namespace Modules\WaterSources\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Modules\UsersAndTeams\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\WaterSources\Models\WaterQualityTest;
use Modules\WaterSources\Emails\WaterQualityReportMail;

class SendDetailedReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected WaterQualityTest $test;
    protected User $recipient;
    protected string $locale;

    /**
     * Create a new job instance.
     */
    public function __construct(WaterQualityTest $test, User $recipient, string $locale)
    {
        $this->test = $test;
        $this->recipient = $recipient;
        $this->locale = $locale;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
         App::setLocale($this->locale);
        $email = new WaterQualityReportMail($this->test);
        Mail::to($this->recipient->email)->send($email);
    }
}
