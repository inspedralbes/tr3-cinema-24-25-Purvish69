<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tus Entradas de FilmGalaxy</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            background-color: #e63946;
            color: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 3px 5px rgba(0,0,0,0.1);
        }
        h1 {
            margin: 0;
            font-size: 24px;
        }
        .movie-info {
            margin-bottom: 20px;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border-left: 4px solid #e63946;
        }
        .movie-title {
            font-size: 20px;
            font-weight: bold;
            color: #e63946;
            margin-bottom: 10px;
        }
        .ticket-count {
            font-weight: bold;
            font-size: 18px;
            color: #e63946;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed #eee;
        }
        .note {
            margin-top: 30px;
            font-size: 14px;
            color: #666;
            background-color: #f1f1f1;
            padding: 15px;
            border-radius: 5px;
            border-left: 3px solid #e63946;
        }
        .footer {
            margin-top: 40px;
            font-size: 12px;
            text-align: center;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .cta-button {
            display: block;
            width: 200px;
            margin: 25px auto;
            padding: 12px 15px;
            background-color: #e63946;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            box-shadow: 0 3px 5px rgba(230,57,70,0.3);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>¡Tus entradas están listas!</h1>
    </div>

    <p>Hola {{ $user->nombre }},</p>

    <p>¡Gracias por tu compra en FilmGalaxy! Adjunto encontrarás un PDF con tus entradas para la sesión.</p>

    <div class="movie-info">
        <div class="movie-title">{{ $sessionInfo['movie']->titulo }}</div>
        <p>
            <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($sessionInfo['fecha'])->format('d/m/Y') }}<br>
            <strong>Hora:</strong> {{ \Carbon\Carbon::parse($sessionInfo['hora'])->format('H:i') }} h<br>
            <strong>Sala:</strong> {{ $sessionInfo['sala'] }}
        </p>
        <div class="ticket-count">
            Número de entradas: {{ count($tickets) }}
        </div>
    </div>

    <p>Puedes imprimir tus entradas o mostrarlas en tu dispositivo móvil al llegar al cine.</p>

    <a href="#" class="cta-button">Ver mis entradas</a>

    <div class="note">
        <p><strong>Información importante:</strong></p>
        <ul>
            <li>Recuerda llegar con al menos 15 minutos de antelación.</li>
            <li>Disfruta de descuentos especiales presentando tus entradas.</li>
            <li>Mantén tu móvil en silencio durante la proyección.</li>
        </ul>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} FilmGalaxy - Todos los derechos reservados</p>
        <p>Para cualquier duda, contáctanos en: <a href="mailto:info@filmgalaxy.com">info@filmgalaxy.com</a></p>
    </div>
</body>
</html>
