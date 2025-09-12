<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Invitation à s'inscrire</title>
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
              <h1 style="font-size:24px; margin:0; color:#222;">Invitation à rejoindre notre plateforme</h1>
            </td>
          </tr>
          <!-- Body -->
          <tr>
            <td style="padding:20px 40px; font-size:16px; line-height:1.6; color:#555;">
              <p>Bonjour,</p>
              <p>Vous avez été invité par notre administrateur à rejoindre notre plateforme avec l’adresse email suivante :</p>
              <p style="font-weight:bold; color:#222;">{{ $email }}</p>
              <p>Pour créer votre compte et commencer à utiliser nos services, cliquez simplement sur le bouton ci-dessous :</p>
              <div style="text-align:center; margin:30px 0;">
                <a href="{{ $acceptUrl }}"
                   style="display:inline-block; background-color:#007BFF; color:#ffffff; text-decoration:none; padding:12px 24px; font-size:16px; border-radius:5px; font-weight:bold;">
                  Créer mon compte
                </a>
              </div>
              <p style="font-size:14px; color:#888;">
                Cet email vous est envoyé car vous avez été invité directement par notre administrateur.
                Si vous pensez qu’il s’agit d’une erreur, vous pouvez ignorer ce message.
              </p>
            </td>
          </tr>
          <!-- Footer -->
          <tr>
            <td align="center" style="padding:20px; background:#f1f1f1; font-size:12px; color:#777;">
              © {{ date('Y') }} Plénitude Groupe. Tous droits réservés.
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
