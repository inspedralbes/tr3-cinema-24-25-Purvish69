<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Entradas FilmGalaxy</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #e63946;
            padding-bottom: 15px;
        }
        h1 {
            color: #e63946;
            margin: 0;
            font-size: 24px;
        }
        .movie-info {
            margin-bottom: 25px;
            text-align: center;
        }
        .movie-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .session-info {
            margin-bottom: 5px;
            font-size: 16px;
        }
        .tickets-container {
            margin-top: 30px;
        }
        .ticket {
            border: 2px dashed #e63946;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 10px;
            page-break-inside: avoid;
        }
        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .ticket-number {
            font-weight: bold;
            background-color: #e63946;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
        }
        .qr-code {
            text-align: center;
            margin: 15px 0;
        }
        .qr-image {
            margin: 0 auto;
            display: block;
            width: 150px;
            height: 150px;
        }
        .seat-info {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 15px 0;
        }
        .price-info {
            text-align: right;
            font-weight: bold;
            margin-top: 15px;
            color: #e63946;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FilmGalaxy - Entradas de Cine</h1>
    </div>

    <div class="movie-info">
        <div class="movie-title">{{ $sessionInfo['movie']->titulo }}</div>
        <div class="session-info">Fecha: {{ \Carbon\Carbon::parse($sessionInfo['fecha'])->format('d/m/Y') }}</div>
        <div class="session-info">Hora: {{ \Carbon\Carbon::parse($sessionInfo['hora'])->format('H:i') }} h</div>
        <div class="session-info">Sala: 1</div>
        <div class="session-info">Espectador: {{ $user->nombre }} {{ $user->apellidos }}</div>
    </div>

    <div class="tickets-container">
        @foreach($tickets as $index => $ticket)
        <div class="ticket">
            <div class="ticket-header">
                <div class="ticket-number">ENTRADA {{ $index + 1 }} / {{ count($tickets) }}</div>
            </div>
            
            <div class="seat-info">
                Fila: {{ $ticket->seat->fila }} - Asiento: {{ $ticket->seat->numero }}
                @if($ticket->seat->tipo === 'vip')
                    <span style="color: #e63946;">(VIP)</span>
                @endif
            </div>
            
            <div class="qr-code">
                <img src="data:image/png;base64,{{ $ticket->qr_code }}" class="qr-image" alt="QR Code">
            </div>
            
            <div class="price-info">
                Precio: {{ number_format($ticket->precio, 2) }} €
            </div>
        </div>
        @endforeach
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} FilmGalaxy - Entradas oficiales</p>
        <p>Consultas: info@filmgalaxy.com</p>
    </div>
</body>
</html>
