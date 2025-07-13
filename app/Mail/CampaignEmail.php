<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Campaign;
use App\Models\Prospect;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class CampaignEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Campaign $campaign,
        public readonly Prospect $prospect,
        public readonly string $trackingUrl
    ) {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->campaign->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.campaign',
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
