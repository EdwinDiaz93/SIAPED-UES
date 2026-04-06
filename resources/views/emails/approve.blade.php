<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cuenta aprobada</title>
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
                                Hola {{ $usuario->name ?? 'Usuario' }} 👋
                            </h2>

                            <p style="font-size:14px; line-height:1.6; color:#374151;">
                                Nos alegra informarte que tu cuenta ha sido
                                <strong style="color:#16a34a;">aprobada exitosamente</strong>.
                            </p>

                            <p style="font-size:14px; line-height:1.6; color:#374151;">
                                Ya puedes acceder a la plataforma y comenzar a utilizar todas las funcionalidades disponibles.
                            </p>

                            <!-- Button -->
                            <div style="margin:30px 0; text-align:center;">
                                <a href="{{ url('/login') }}"
                                   style="background:#960000; color:#ffffff; padding:12px 20px; text-decoration:none; border-radius:8px; font-size:14px; display:inline-block;">
                                    Ir al sistema
                                </a>
                            </div>

                            <p style="font-size:12px; color:#6b7280;">
                                Si tienes alguna duda, Contacta a Academica.
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
