<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessAdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $customer;
    public $products;
    public $payment;

    public function __construct($payment, $customer, $products)
    {
        $this->payment = $payment;
        $this->customer = $customer;
        $this->products = $products;
    }

    public function build()
    {
        return $this->subject('Payment Sukses: ' . $this->payment->id_order)
                    ->view('emails.payment_success_admin');
    }
}
