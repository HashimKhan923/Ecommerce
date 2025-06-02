<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Campaign;
use App\Models\User;

class CampaignMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $campaign, $recipient;

    public function __construct(Campaign $campaign, User $recipient)
    {
        $this->campaign = $campaign;
        $this->recipient = $recipient;
    }
 
    public function build()
    {
        $content = $this->campaign->content;
        $content = str_replace('{{buyer_name}}', $this->recipient->name, $content);

        $content = app('App\Services\TrackingHelper')->injectTracking($content, $this->campaign->id, $this->recipient->id);

        return $this->subject($this->campaign->subject)
                    ->html($content);
    }
}
