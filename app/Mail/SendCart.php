<?php

namespace App\Mail;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendCart extends Mailable
{
    use Queueable, SerializesModels;


    public function __construct($details)
    {
        $this->details = $details;

    }

    public function build()
    {

        // return $this->view('emails.sendcart')
        // ->with([
        //     'orderName' => $this->order->name,
        //     'orderPrice' => $this->order->email,
        // ]);


        return $this->subject($this->details['subject'])
        ->view('emails.sendcart')
        ->from($this->details['from'], $this->details['from']);


    }
}
