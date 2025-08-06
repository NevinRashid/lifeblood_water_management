<?php

namespace Modules\WaterSources\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Modules\WaterSources\Emails\LowWaterLevelMail;
use Modules\WaterSources\Models\WaterExtraction;

class CheckWaterLevelAndSendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected WaterExtraction $waterExtraction;
    protected string $locale;
    /**
     * Create a new job instance
     */
    public function __construct(WaterExtraction $waterExtraction, string $locale)
    {
        $this->waterExtraction = $waterExtraction;
        $this->locale = $locale;
    }

    /**
     * Execute the job
     */
    public function handle(): void
    {
        App::setLocale($this->locale);

        $waterSource = $this->waterExtraction->waterSource;

        if (!$waterSource || is_null($waterSource->capacity_per_day) || $waterSource->status !== 'active') {
            return;
        }

        $today = Carbon::today();
        $totalExtractedToday = WaterExtraction::where('water_source_id', $waterSource->id)
            ->whereDate('extraction_date', $today)
            ->sum('extracted');

        $thresholdPercentage = 0.80; // 80%
        $thresholdAmount = $waterSource->capacity_per_day * $thresholdPercentage;

        $extractedBeforeThis = $totalExtractedToday - $this->waterExtraction->extracted;

        // if the total extraction equal or more than 80%, send email
        if ($totalExtractedToday >= $thresholdAmount && $extractedBeforeThis < $thresholdAmount) {
            $emails = ['bshermahayni@gmail.com', 'rayahaneen8@gmail.com', 'nevinalirashid@gmail.com'];
            foreach ($emails as $email) {
                Mail::to($email)->send(new LowWaterLevelMail($waterSource, $totalExtractedToday));
            }
        }
    }
}
