<?php

namespace App\Mail;

use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DomainExpiryReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Domain $domain,
        public string $type
    ) {
    }

    public function build(): self
    {
        return $this->subject('Domain renewal reminder: '.$this->domain->domain_name)
            ->view('emails.domain_expiry');
    }
}

