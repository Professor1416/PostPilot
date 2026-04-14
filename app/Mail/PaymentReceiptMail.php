<?php

namespace App\Mail;

use App\Models\Payment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// ─── Payment Receipt ──────────────────────────────────────────────────────────

class PaymentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User    $user,
        public Payment $payment
    ) {}

    public function envelope(): Envelope
    {
        $plan = config("postpilot.plans.{$this->payment->plan}.label");
        return new Envelope(subject: "Payment Confirmed — {$plan} Activated ✓");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment-receipt');
    }
}

// ─── Post Failed Alert ────────────────────────────────────────────────────────

class PostFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Post   $post,
        public string $reason
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "[PostPilot Admin] Post Publish Failed — Post #{$this->post->id}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.post-failed');
    }
}

// ─── Generic Admin Alert ──────────────────────────────────────────────────────

class AdminAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $alertTitle,
        public array  $data
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "[PostPilot Admin] {$this->alertTitle}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin-alert');
    }
}
