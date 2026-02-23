<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Városok Exportálása</title>
    <style>
        body { font-family: sans-serif; line-height: 1.5; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .footer { margin-top: 30px; font-size: 12px; color: #777; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Kedves Felhasználó!</h2>
        <p>A kérésednek megfelelően a rendszer legenerálta a keresett városok listáját.</p>
        <p>A kért adatokat a jelen e-mailhez csatolt <strong>PDF fájlban</strong> találod.</p>
        
        <div class="footer">
            <p>Ez egy automatikusan generált e-mail, kérjük, ne válaszolj rá.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }} Rendszer</p>
        </div>
    </div>
</body>
</html>