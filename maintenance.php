<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema en Mantenimiento</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }

        .maintenance-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 60px 40px;
            text-align: center;
            max-width: 600px;
            width: 90%;
            position: relative;
            overflow: hidden;
        }

        .maintenance-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(102, 126, 234, 0.1), transparent);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .maintenance-icon {
            font-size: 80px;
            margin-bottom: 30px;
            color: #667eea;
            position: relative;
            z-index: 1;
        }

        .maintenance-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #2d3748;
            position: relative;
            z-index: 1;
        }

        .maintenance-subtitle {
            font-size: 1.2rem;
            color: #718096;
            margin-bottom: 30px;
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }

        .maintenance-message {
            font-size: 1rem;
            color: #4a5568;
            margin-bottom: 40px;
            line-height: 1.7;
            position: relative;
            z-index: 1;
        }

        .progress-container {
            background: #e2e8f0;
            border-radius: 10px;
            height: 8px;
            margin: 30px 0;
            overflow: hidden;
            position: relative;
            z-index: 1;
        }

        .progress-bar {
            background: linear-gradient(90deg, #667eea, #764ba2);
            height: 100%;
            width: 0%;
            border-radius: 10px;
            animation: progress 2s ease-in-out infinite alternate;
        }

        @keyframes progress {
            0% { width: 30%; }
            100% { width: 70%; }
        }

        .contact-info {
            background: #f7fafc;
            border-radius: 15px;
            padding: 25px;
            margin-top: 30px;
            border-left: 4px solid #667eea;
            position: relative;
            z-index: 1;
        }

        .contact-title {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .contact-details {
            color: #4a5568;
            font-size: 0.95rem;
        }

        .estimated-time {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px 25px;
            border-radius: 25px;
            display: inline-block;
            margin: 20px 0;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .social-links {
            margin-top: 25px;
            position: relative;
            z-index: 1;
        }

        .social-links a {
            display: inline-block;
            margin: 0 10px;
            padding: 10px;
            background: #667eea;
            color: white;
            border-radius: 50%;
            text-decoration: none;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .social-links a:hover {
            transform: translateY(-3px);
            background: #5a67d8;
        }

        @media (max-width: 768px) {
            .maintenance-container {
                padding: 40px 25px;
                margin: 20px;
            }

            .maintenance-title {
                font-size: 2rem;
            }

            .maintenance-icon {
                font-size: 60px;
            }
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
            position: relative;
            z-index: 1;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="maintenance-icon">🔧</div>
        <h1 class="maintenance-title">Sistema en Mantenimiento</h1>
        <p class="maintenance-subtitle">Estamos trabajando para mejorar tu experiencia</p>
        
        <div class="progress-container">
            <div class="progress-bar"></div>
        </div>
        
        <p class="maintenance-message">
            Nuestro equipo técnico está realizando actualizaciones importantes para brindarte un mejor servicio. 
            El sistema estará disponible nuevamente en breve.
        </p>
        
        <div class="estimated-time">
            ⏱️ Tiempo estimado: 2-4 horas
        </div>
        
        <div class="spinner"></div>
    
        
        
        <p style="margin-top: 25px; color: #718096; font-size: 0.9rem; position: relative; z-index: 1;">
            Última actualización: <?php echo date('d/m/Y H:i:s'); ?>
        </p>
    </div>
    
    <script>
        // Auto-refresh cada 5 minutos
        setTimeout(function() {
            location.reload();
        }, 300000);
        
        // Contador dinámico (opcional)
        let startTime = new Date().getTime();
        
        function updateTimer() {
            let now = new Date().getTime();
            let elapsed = now - startTime;
            let seconds = Math.floor(elapsed / 1000);
            let minutes = Math.floor(seconds / 60);
            let hours = Math.floor(minutes / 60);
            
            // Puedes usar este timer si lo necesitas
            console.log(`Tiempo en mantenimiento: ${hours}h ${minutes%60}m ${seconds%60}s`);
        }
        
        setInterval(updateTimer, 1000);
    </script>
</body>
</html>
