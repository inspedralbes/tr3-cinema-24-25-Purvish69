<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tus Entradas de FilmGalaxy</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #1C1C1C;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #F2E9E4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            background-color: #22223B;
            color: #F2E9E4;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 3px 5px rgba(0,0,0,0.1);
        }
        h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-content {
            background-color: #EAE0D5;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .cta-button {
            display: block;
            width: 200px;
            margin: 25px auto;
            padding: 12px 15px;
            background-color: #22223B;
            color: #F2E9E4;
            text-align: center;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            box-shadow: 0 3px 5px rgba(34,34,59,0.3);
        }
        .footer {
            margin-top: 40px;
            font-size: 12px;
            text-align: center;
            color: #4A4E69;
            border-top: 1px solid #9A8C98;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>¡Tus billetes de entrada están listos!</h1>
    </div>

    <div class="email-content">
        <p>Hola {{ $user->nombre }},</p>

        <p>Tus billetes para la película están adjuntos en PDF.</p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} FilmGalaxy - Todos los derechos reservados</p>
        <p>Consultas: <a href="mailto:info@filmgalaxy.com" style="color: #22223B;">info@filmgalaxy.com</a></p>
    </div>
</body>
</html>