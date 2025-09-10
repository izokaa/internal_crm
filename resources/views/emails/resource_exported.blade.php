<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Export de la resource {{ $resource }}</title>
</head>
<body style="margin:0; padding:0; font-family: Arial, Helvetica, sans-serif; background-color:#f9f9f9; color:#333;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f9f9f9; padding:40px 0;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); overflow:hidden;">
          <!-- Header -->
          <tr>
            <td align="center" style="padding:30px;">
              <img src="{{ asset('logo.png') }}" alt="Logo" style="height:60px; margin-bottom:20px;">
              <h1 style="font-size:24px; margin:0; color:#222;">Export de la resource {{ $resource }}</h1>
            </td>
          </tr>
          <!-- Body -->
          <tr>
            <td style="padding:20px 40px; font-size:16px; line-height:1.6; color:#555;">
              <p>Bonjour Admin,</p>
              <p></p>
              <p style="font-weight:bold; color:#222;"> la resource {{ $resource }} a été exporté par l'utilisateur {{ $user->name }} - email: {{ $user->email }}</p>
            </td>
          </tr>
          <!-- Footer -->
          <tr>
            <td align="center" style="padding:20px; background:#f1f1f1; font-size:12px; color:#777;">
              © {{ date('Y') }} Votre Société. Tous droits réservés.
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
