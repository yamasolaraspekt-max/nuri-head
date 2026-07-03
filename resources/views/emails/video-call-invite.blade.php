<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Einladung zum Video-Call</title>
</head>
<body style="margin:0; padding:0; background:#f4f5f7; font-family:Arial,Helvetica,sans-serif; color:#1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f5f7; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden; max-width:560px;">
                    <tr>
                        <td style="background:#93c21c; padding:20px 28px;">
                            <span style="color:#ffffff; font-size:18px; font-weight:bold;">Solar Aspekt</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <p style="margin:0 0 12px; font-size:16px;">Hallo{{ $invite->name ? ' ' . $invite->name : '' }},</p>
                            <p style="margin:0 0 20px; font-size:15px; line-height:1.5;">
                                Sie sind zu einem Video-Call eingeladen. Der Beitritt erfolgt direkt im Browser —
                                ohne Installation und ohne Anmeldung.
                            </p>
                            <p style="margin:0 0 28px;">
                                <a href="{{ $guestUrl }}"
                                   style="display:inline-block; background:#93c21c; color:#ffffff; text-decoration:none; padding:12px 22px; border-radius:6px; font-size:15px; font-weight:bold;">
                                    Am Video-Call teilnehmen
                                </a>
                            </p>
                            <p style="margin:0 0 8px; font-size:13px; color:#6b7280;">
                                Falls der Button nicht funktioniert, kopieren Sie diesen Link in Ihren Browser:
                            </p>
                            <p style="margin:0 0 20px; font-size:12px; color:#6b7280; word-break:break-all;">
                                {{ $guestUrl }}
                            </p>
                            <p style="margin:0; font-size:12px; color:#9ca3af;">
                                Der Link ist zeitlich begrenzt gültig.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
