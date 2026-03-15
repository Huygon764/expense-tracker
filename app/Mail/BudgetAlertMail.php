<?php

namespace App\Mail;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BudgetAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Notification $notification
    ) {}

    public function envelope(): Envelope
    {
        $subject = match ($this->notification->type) {
            'budget_50' => __('messages.budget_alert_50'),
            'budget_80' => __('messages.budget_alert_80'),
            'budget_100' => __('messages.budget_alert_100'),
            'budget_weekly_50' => __('messages.budget_alert_weekly_50'),
            'budget_weekly_80' => __('messages.budget_alert_weekly_80'),
            'budget_weekly_100' => __('messages.budget_alert_weekly_100'),
            default => __('messages.email_subject_budget_default'),
        };

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.budget_alert',
            with: [
                'alertMessage' => $this->notification->message,
            ],
        );
    }
}
