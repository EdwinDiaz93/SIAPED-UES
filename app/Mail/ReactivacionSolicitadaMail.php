<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReactivacionSolicitadaMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public User $usuario)
    {
        return $this->subject('Solicitud de reactivación de cuenta — ' . config('app.name'))
            ->view('emails.reactivacion-solicitada', ['usuario' => $usuario]);
    }
}
