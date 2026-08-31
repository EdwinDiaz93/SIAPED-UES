<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud de Promoción Rechazada</title>
</head>
<body style="margin:0; padding:0; background-color:#f3f4f6; font-family:Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:600px; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 10px rgba(0,0,0,0.05);">

                    <tr>
                        <td style="background:#960000; padding:20px; text-align:center;">
                            <h1 style="color:#ffffff; margin:0; font-size:20px;">{{ config('app.name') }}</h1>
                            <p style="color:#ffcccc; margin:4px 0 0; font-size:13px;">Facultad de Ingeniería y Arquitectura</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:30px; color:#111827;">

                            <h2 style="margin-top:0; font-size:22px; color:#991b1b;">
                                Su solicitud de promoción fue rechazada
                            </h2>

                            <p style="font-size:14px; line-height:1.7; color:#374151;">
                                Estimado/a <strong>{{ $solicitud->docente->name }} {{ $solicitud->docente->apellidos }}</strong>,
                            </p>

                            <p style="font-size:14px; line-height:1.7; color:#374151;">
                                Le informamos que su solicitud de promoción escalafonaria de
                                <strong>{{ $solicitud->categoria_actual }}</strong> a
                                <strong>{{ $solicitud->categoria_solicitada }}</strong> ha sido
                                <strong style="color:#991b1b;">rechazada</strong> por el departamento académico.
                            </p>

                            <table width="100%" cellpadding="8" cellspacing="0"
                                style="border-radius:8px; background:#f9fafb; border:1px solid #e5e7eb; margin-bottom:20px; font-size:13px;">
                                <tr>
                                    <td style="color:#6b7280;">Fecha de revisión:</td>
                                    <td style="font-weight:bold; text-align:right;">{{ $solicitud->fecha_revision?->format('d/m/Y') }}</td>
                                </tr>
                            </table>

                            @if ($solicitud->observaciones)
                                <table width="100%" cellpadding="12" cellspacing="0"
                                    style="border-radius:8px; background:#fef2f2; border:1px solid #fecaca; margin-bottom:20px;">
                                    <tr>
                                        <td>
                                            <p style="margin:0 0 4px; font-size:12px; color:#991b1b; text-transform:uppercase; font-weight:bold;">Motivo del rechazo</p>
                                            <p style="margin:0; font-size:14px; color:#374151; line-height:1.6;">{{ $solicitud->observaciones }}</p>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <p style="font-size:14px; line-height:1.7; color:#374151;">
                                Puede corregir lo indicado y volver a solicitar la promoción cuando lo considere oportuno.
                            </p>

                            <div style="margin:30px 0; text-align:center;">
                                <a href="{{ url('/') }}"
                                    style="background:#960000; color:#ffffff; padding:12px 24px; text-decoration:none;
                                           border-radius:8px; font-size:14px; display:inline-block; font-weight:bold;">
                                    Ir al sistema
                                </a>
                            </div>

                            <p style="font-size:12px; color:#9ca3af;">
                                Si tiene alguna duda, contacte al Departamento Académico de la FIA-UES.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background:#f9fafb; padding:16px; text-align:center; font-size:12px; color:#9ca3af;">
                            © {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
