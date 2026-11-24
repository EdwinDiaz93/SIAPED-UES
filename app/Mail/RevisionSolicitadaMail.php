<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RevisionSolicitadaMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public User $docente)
    {
        return $this->subject('Docente listo para revisión — ' . config('app.name'))
            ->view('emails.revision-solicitada', ['docente' => $docente]);
    }
}
