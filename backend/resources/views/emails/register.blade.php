<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Bienvenido a FilmGalaxy🎬</title>
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif;">
    <div style="max-width: 600px; margin: 50px auto;">
        <div style="background-color: #EAE0D5; border: 2px solid #C8A96E; border-radius: 8px; overflow: hidden;">
            <!-- Header de la card -->
            <div style="background-color: #22223B; padding: 20px; text-align: center;">
                <h1 style="color: #EAE0D5; margin: 0;">Bienvenido a FilmGalaxy🎬</h1>
            </div>
            <!-- Contenido de la card -->
            <div style="padding: 20px; color: #4A4E69;">
                <p>Hola {{ $user->nombre }},</p>
                <p>Gracias por registrarte en FilmGalaxy, el portal donde puedes comprar billetes para las mejores
                    películas. Estamos encantados de tenerte con nosotros.</p>
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ url('http://filmgalaxy.daw.inspedralbes.cat/') }}"
                        style="display: inline-block; padding: 10px 20px; background-color: #C8A96E; color: #22223B; text-decoration: none; border-radius: 4px;">
                        Visita nuestra web
                    </a>
                </div>
                <p>¡Disfruta de una experiencia cinematográfica única!</p>
                <p>Saludos cordiales,</p>
                <p>El equipo de FilmGalaxy</p>
            </div>
        </div>
    </div>
</body>

</html>