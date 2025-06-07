<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>403 · Acceso Denegado</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* Pantalla completa */
        html, body {
            height: 100%;
            margin: 0;
            background: #f4f4f4;
            font-family: 'Segoe UI', sans-serif;
            color: #333;
        }
        .full-screen {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            text-align: center;
            padding: 1rem;
        }

        /* Mensaje de error */
        .error-code {
            font-size: 6rem;
            font-weight: bold;
            color: #dc3545;
        }
        .error-message {
            font-size: 1.5rem;
            margin-top: 0.5rem;
        }

        /* Botón “volver” en esquina */
        .back-corner {
            position: fixed;
            top: 20px;
            left: 20px;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(90deg, #6D5BFF, #A986FF);
            color: #fff;
            font-size: 1.4rem;
            text-decoration: none;
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: transform .2s, opacity .2s;
            z-index: 1000;
        }
        .back-corner:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="full-screen">
        <div class="error-code">403</div>
        <div class="error-message">No tienes permisos para acceder a esta página</div>
        <a href="{{ route('sistema') }}" class="back-corner" title="Volver al Sistema">←</a>
    </div>
</body>
</html>
