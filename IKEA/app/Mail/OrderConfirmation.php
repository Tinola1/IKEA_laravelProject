<?php

namespace App\Mail;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmed — #' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT) . ' | IKEA Philippines',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-confirmation',
        );
    }

    public function attachments(): array
    {
        // Generate the PDF from the receipt blade view
        $pdf = Pdf::loadView('emails.receipt', ['order' => $this->order])
            ->setPaper('a4', 'portrait');

        $filename = 'IKEA-Receipt-' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT) . '.pdf';

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                $filename
            )->withMime('application/pdf'),
        ];
    }
}