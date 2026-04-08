<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Promoción Aprobada</title>
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

                            <h2 style="margin-top:0; font-size:22px; color:#16a34a;">
                                ¡Felicidades! Su promoción fue aprobada 🎓
                            </h2>

                            <p style="font-size:14px; line-height:1.7; color:#374151;">
                                Estimado/a <strong>{{ $solicitud->docente->name }} {{ $solicitud->docente->apellidos }}</strong>,
                            </p>

                            <p style="font-size:14px; line-height:1.7; color:#374151;">
                                Nos complace informarle que su solicitud de promoción escalafonaria ha sido
                                <strong style="color:#16a34a;">aprobada</strong> por el departamento académico.
                            </p>

                            <!-- Cuadro de promoción -->
                            <table width="100%" cellpadding="12" cellspacing="0"
                                style="border-radius:8px; background:#f0fdf4; border:1px solid #86efac; margin:20px 0;">
                                <tr>
                                    <td style="text-align:center; border-right:1px solid #86efac;">
                                        <p style="margin:0; font-size:12px; color:#6b7280; text-transform:uppercase;">Categoría anterior</p>
                                        <p style="margin:4px 0 0; font-size:24px; font-weight:bold; color:#374151;">
                                            {{ $solicitud->categoria_actual }}
                                        </p>
                                    </td>
                                    <td style="text-align:center; font-size:24px; color:#16a34a; width:40px;">→</td>
                                    <td style="text-align:center; border-left:1px solid #86efac;">
                                        <p style="margin:0; font-size:12px; color:#6b7280; text-transform:uppercase;">Nueva categoría</p>
                                        <p style="margin:4px 0 0; font-size:24px; font-weight:bold; color:#16a34a;">
                                            {{ $solicitud->categoria_solicitada }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="8" cellspacing="0"
                                style="border-radius:8px; background:#f9fafb; border:1px solid #e5e7eb; margin-bottom:20px; font-size:13px;">
                                <tr>
                                    <td style="color:#6b7280;">Puntaje obtenido:</td>
                                    <td style="font-weight:bold; text-align:right;">{{ number_format($solicitud->puntaje_obtenido, 2) }} pts</td>
                                </tr>
                                <tr>
                                    <td style="color:#6b7280;">Puntaje requerido:</td>
                                    <td style="font-weight:bold; text-align:right;">{{ number_format($solicitud->puntaje_requerido, 2) }} pts</td>
                                </tr>
                                <tr>
                                    <td style="color:#6b7280;">Fecha de aprobación:</td>
                                    <td style="font-weight:bold; text-align:right;">{{ $solicitud->fecha_revision?->format('d/m/Y') }}</td>
                                </tr>
                                @if ($solicitud->observaciones)
                                <tr>
                                    <td style="color:#6b7280;">Observaciones:</td>
                                    <td style="text-align:right;">{{ $solicitud->observaciones }}</td>
                                </tr>
                                @endif
                            </table>

                            <p style="font-size:14px; line-height:1.7; color:#374151;">
                                Su nueva categoría ya está registrada en el sistema. Puede ingresar para
                                verificar su perfil actualizado.
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
