<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Cartemail extends Mailable
{
    use Queueable, SerializesModels;

    public $array;

    public function __construct($array)
    {
        $this->array = $array;
    }


    public function build()
    {

        return $this->view($this->array['view'])
        ->from($this->array['from'], env('MAIL_FROM_NAME'))
        ->subject($this->array['subject']);


     }
}
