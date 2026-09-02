<?php
    $destino = auth()->check() && auth()->user()->hasAnyRole(['admin', 'comite'])
        ? route('dashboard')
        : (auth()->check() ? route('account.details') : route('login'));
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>{{ __('Página no encontrada') }} - {{ config('app.name', 'SIAPED-UES') }}</title>
        <meta http-equiv="refresh" content="5;url={{ $destino }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <style>
            body {
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: #f5f5f4;
                font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
                padding: 1.5rem;
            }
            .card {
                width: 100%;
                max-width: 28rem;
                background: #ffffff;
                border: 1px solid #e5e5e4;
                border-radius: 1rem;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
                padding: 2.5rem 2rem;
                text-align: center;
            }
            .icon {
                width: 4rem;
                height: 4rem;
                margin: 0 auto 1.25rem;
                border-radius: 9999px;
                background-color: rgba(150, 0, 0, 0.1);
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .icon svg {
                width: 2rem;
                height: 2rem;
                color: #960000;
            }
            h1 {
                font-size: 1.25rem;
                font-weight: 600;
                color: #960000;
                margin: 0 0 0.5rem;
            }
            p {
                color: #57534e;
                font-size: 0.95rem;
                line-height: 1.5;
                margin: 0 0 1.75rem;
            }
            a.button {
                display: inline-block;
                background-color: #960000;
                color: #ffffff;
                text-decoration: none;
                font-weight: 500;
                font-size: 0.9rem;
                padding: 0.65rem 1.5rem;
                border-radius: 0.5rem;
                transition: opacity 0.15s ease;
            }
            a.button:hover {
                opacity: 0.9;
            }
        </style>
    </head>
    <body>
        <div class="card">
            <div class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75c0-1.24 1.01-2.25 2.25-2.25s2.25 1.01 2.25 2.25c0 .9-.53 1.4-1.06 1.86-.53.46-1.19 1-1.19 1.89v.25M12 17.25h.008v.008H12v-.008ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <h1>{{ __('Página no encontrada') }}</h1>
            <p>{{ __('La página que buscas no existe o fue movida.') }}</p>
            <a href="{{ $destino }}" class="button">{{ __('Volver al inicio') }}</a>
        </div>
    </body>
</html>
