<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="UTF-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>Document supplémentaire requis</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin=""/>
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Work+Sans:400,400i,700,700i,900,900i"/>
<style>
    img { -ms-interpolation-mode: bicubic; }
    table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    p, a, li, td, body, table, blockquote { -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; }
    body { height: 100%; margin: 0; padding: 0; width: 100%; background: #ffffff; font-family: "Work Sans", sans-serif; }
    p { margin: 0; padding: 0; }
    table { border-collapse: collapse; }
    td, p, a { word-break: break-word; }
    img, a img { border: 0; height: auto; outline: none; text-decoration: none; }
    .wrapper { max-width: 660px; margin: 0 auto; background-color: #f1e7da; }
    .header img { display: block; width: 100%; }
    .content { padding: 24px; color: #000000; font-size: 16px; line-height: 1.5; }
    .content p { margin-bottom: 16px; }
    .footer { padding: 16px; text-align: center; background-color: #f1e7da; color: #333333; font-size: 12px; }
    .footer a { color: #333333; }
    .divider { border-top: 1px solid #000000; margin: 16px 0; }
    .logo { text-align: center; padding: 12px 0; }
    .logo img { width: 63px; height: auto; }
</style>
</head>
<body>
<center>
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#ffffff;">
<tbody>
<tr>
<td align="center" valign="top">
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:660px;" class="wrapper">
<tbody>

<!-- Header image -->
<tr>
<td class="header">
    <img src="https://mcusercontent.com/b4e0f5c1ee667bb09e7ace873/images/7c773455-29b9-e381-8dce-a1543c3be9d7.png" width="660" alt="Meet People" style="display:block;width:100%;"/>
</td>
</tr>

<!-- Body -->
<tr>
<td class="content">
    <p>Bonjour <strong>{{ $username }}</strong>,</p>

    <p>Nous avons bien reçu votre demande concernant votre expérience <strong>{{ $experienceTitle }}</strong>.</p>

    <p>Afin de finaliser la vérification de votre expérience, nous avons besoin de <strong>documents supplémentaires</strong> de votre part.</p>

    <p>Merci de vous connecter à l'application Meet People et de soumettre les documents demandés dans les plus brefs délais.</p>

    <p>Si vous avez des questions, n'hésitez pas à nous contacter à : <a href="mailto:contact@meetpe.fr">contact@meetpe.fr</a> 🧡</p>

    <p>À très vite,<br/>L'équipe Meet People</p>
</td>
</tr>

<!-- Divider -->
<tr>
<td style="padding: 0 16px;">
    <div class="divider"></div>
</td>
</tr>

<!-- Logo -->
<tr>
<td class="logo">
    <img src="https://mcusercontent.com/b4e0f5c1ee667bb09e7ace873/images/fc792195-a44a-a57f-084a-33f65c528efb.png" width="63" alt="Meet People"/>
</td>
</tr>

<!-- Footer -->
<tr>
<td class="footer">
    <p>
        <em><span>Copyright &copy; {{ $currentYear }} Meet People. {!! __('general.right_reserved') !!}</span></em><br/>
        <span>{!! __('general.our_address') !!}</span><br/>
        <span>Meet People &bull; 18 Rue Drouot &bull; Paris 75009 &bull; France</span>
    </p>
</td>
</tr>

</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</center>
</body>
</html>
