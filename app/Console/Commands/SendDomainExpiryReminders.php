<?php

namespace App\Console\Commands;

use App\Mail\DomainExpiryReminderMail;
use App\Models\Domain;
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDomainExpiryReminders extends Command
{
    protected $signature = 'domains:send-reminders';

    protected $description = 'Send email reminders for domains expiring soon or today.';

    public function handle(): int
    {
        $today = Carbon::today();

        $this->sendForOffset('30days', $today->copy()->addDays(30));
        $this->sendForOffset('7days', $today->copy()->addDays(7));
        $this->sendForOffset('expired', $today);

        $this->info('Domain expiry reminders processed.');

        return self::SUCCESS;
    }

    protected function sendForOffset(string $type, Carbon $date): void
    {
        $domains = Domain::with('customer')
            ->whereDate('expiry_date', $date)
            ->get();

        foreach ($domains as $domain) {
            if (! $domain->customer || ! $domain->customer->email) {
                continue;
            }

            Mail::to($domain->customer->email)->send(
                new DomainExpiryReminderMail($domain, $type)
            );

            Reminder::create([
                'domain_id' => $domain->id,
                'reminder_type' => $type,
                'sent_at' => now(),
            ]);
        }
    }
}

