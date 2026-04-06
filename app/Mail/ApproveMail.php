<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApproveMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public $usuario)
    {
        return $this->subject('Aprobación de cuenta')
            ->view('emails.approve', ['usuario' => $usuario]);
    }
}
