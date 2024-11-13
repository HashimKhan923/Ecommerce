<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductBackInStock extends Mailable
{
    use Queueable, SerializesModels;

    public $customerEmail;
    public $productImage;
    public $productName;
    public $variantName;
    public $productUrl;

    public function __construct($customerEmail, $productImage, $productName, $variantName = null, $productUrl)
    {
        $this->customerEmail = $customerEmail;
        $this->productImage = $productImage;
        $this->productName = $productName;
        $this->variantName = $variantName;
        $this->productUrl = $productUrl;
    }

    public function build()
    {
        return $this->view('email.product_back_in_stock')
                    ->subject('Product Back in Stock')
                    ->with([
                        'customerEmail' => $this->customerEmail,
                        'productImage' => $this->productImage,
                        'productName' => $this->productName,
                        'variantName' => $this->variantName,
                        'productUrl' => $this->productUrl,
                    ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Product Back In Stock',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
