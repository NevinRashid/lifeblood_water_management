<?php

namespace Modules\WaterSources\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\App;
use Modules\WaterSources\Models\WaterSource;

class LowWaterLevelMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public WaterSource $waterSource;
    public float $totalExtractedToday;

    /**
     * Create a new message instance.
     */
    public function __construct(WaterSource $waterSource, float $totalExtractedToday)
    {
        $this->waterSource = $waterSource;
        $this->totalExtractedToday = $totalExtractedToday;
    }

    /**
     * Get the message envelope
     */
    public function envelope(): Envelope
    {
        $sourceName = $this->waterSource->getTranslation('name', App::getLocale());
        return new Envelope(
            subject: __('mail.low_water.subject', ['sourceName' => $sourceName]),
        );
    }

    /**
     * Get the message content definition
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'watersources::emails.water.low_level',
            with: [
                'waterSource' => $this->waterSource,
                'totalExtractedToday' => $this->totalExtractedToday,
            ],
        );
    }

    /**
     * Get the attachments for the message
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
