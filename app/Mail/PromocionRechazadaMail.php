<?php

namespace App\Mail;

use App\Models\SolicitudPromocion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PromocionRechazadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SolicitudPromocion $solicitud) {}

    public function build(): static
    {
        return $this->subject('Solicitud de Promoción Escalafonaria Rechazada — ' . config('app.name'))
            ->view('emails.promocion-rechazada');
    }
}
