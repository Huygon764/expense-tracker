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
        $subjects = [
            'budget_50' => 'Bạn đã dùng 50% ngân sách tháng này',
            'budget_80' => 'Bạn đã dùng 80% ngân sách tháng này',
            'budget_100' => 'Bạn đã vượt ngân sách tháng này!',
        ];

        $subject = $subjects[$this->notification->type] ?? 'Thông báo ngân sách';

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.budget_alert',
            with: [
                'message' => $this->notification->message,
            ],
        );
    }
}
