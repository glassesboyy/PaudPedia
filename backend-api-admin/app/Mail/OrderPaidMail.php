<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pembayaran Berhasil - ' . $this->order->order_number,
        );
    }

    public function content(): Content
    {
        $this->order->loadMissing(['items.item', 'user']);

        $frontendUrl = config('app.frontend_url', 'http://localhost:3000');

        return new Content(
            view: 'emails.order-paid',
            with: [
                'order'       => $this->order,
                'userName'    => $this->order->user->name,
                'frontendUrl' => $frontendUrl,
            ],
        );
    }
}
