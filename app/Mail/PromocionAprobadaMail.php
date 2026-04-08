<?php

namespace App\Mail;

use App\Models\SolicitudPromocion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PromocionAprobadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SolicitudPromocion $solicitud) {}

    public function build(): static
    {
        return $this->subject('Promoción Escalafonaria Aprobada — ' . config('app.name'))
            ->view('emails.promocion-aprobada');
    }
}
