<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitud de reactivación de cuenta</title>
</head>
<body style="margin:0; padding:0; background-color:#f3f4f6; font-family:Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding: 40px 0;">
        <tr>
            <td align="center">

                <!-- Card -->
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 10px rgba(0,0,0,0.05);">

                    <!-- Header -->
                    <tr>
                        <td style="background:#960000; padding:20px; text-align:center;">
                            <h1 style="color:#ffffff; margin:0; font-size:20px;">
                                {{ config('app.name') }}
                            </h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px; text-align:left; color:#111827;">

                            <h2 style="margin-top:0; font-size:18px;">
                                Solicitud de reactivación de cuenta
                            </h2>

                            <p style="font-size:14px; line-height:1.6; color:#374151;">
                                Un usuario con cuenta deshabilitada solicitó que se reactive su acceso al sistema. Estos son sus datos:
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin:20px 0; font-size:14px; color:#374151;">
                                <tr>
                                    <td style="padding:6px 0; width:140px;"><strong>Nombre:</strong></td>
                                    <td style="padding:6px 0;">{{ $usuario->name }} {{ $usuario->apellidos }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0;"><strong>Correo:</strong></td>
                                    <td style="padding:6px 0;">{{ $usuario->email }}</td>
                                </tr>
                                @if ($usuario->telefono)
                                <tr>
                                    <td style="padding:6px 0;"><strong>Teléfono:</strong></td>
                                    <td style="padding:6px 0;">{{ $usuario->telefono }}</td>
                                </tr>
                                @endif
                            </table>

                            <p style="font-size:14px; line-height:1.6; color:#374151;">
                                Si corresponde, puedes habilitar la cuenta desde el panel de administración de usuarios.
                            </p>

                            <!-- Button -->
                            <div style="margin:30px 0; text-align:center;">
                                <a href="{{ route('manage.users') }}"
                                   style="background:#960000; color:#ffffff; padding:12px 20px; text-decoration:none; border-radius:8px; font-size:14px; display:inline-block;">
                                    Ir a administrar usuarios
                                </a>
                            </div>

                            <p style="font-size:12px; color:#6b7280;">
                                Este correo se envió automáticamente porque la cuenta anterior tiene el acceso deshabilitado.
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f9fafb; padding:20px; text-align:center; font-size:12px; color:#9ca3af;">
                            © {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>
