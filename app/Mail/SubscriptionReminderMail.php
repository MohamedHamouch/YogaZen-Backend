<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function build()
    {
        return $this->subject('Your YogaZen Academy subscription expires soon')
                    ->view('emails.subscription-reminder')
                    ->with([
                        'studentName' => $this->subscription->student->name,
                        'expiresAt' => $this->subscription->expires_at,
                        'subscriptionType' => $this->subscription->type
                    ]);
    }
}