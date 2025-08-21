<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Prueba de Toast de Notificaciones</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 40px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        
        .controls {
            margin-bottom: 30px;
        }
        
        .btn {
            padding: 12px 24px;
            margin: 5px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a67d8;
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }
        
        .btn-secondary:hover {
            background: #cbd5e0;
        }
        
        .status {
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .status.info {
            background: #ebf8ff;
            border: 1px solid #90cdf4;
            color: #2b6cb0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔔 Prueba de Toast de Notificaciones Push</h1>
        
        <div class="status info">
            <strong>Estado del Navegador:</strong>
            <div id="browser-status">Verificando...</div>
        </div>
        
        <div class="controls">
            <button class="btn btn-primary" onclick="showNotificationToast()">
                Mostrar Toast
            </button>
            <button class="btn btn-secondary" onclick="showNotificationToast('Mensaje personalizado para prueba')">
                Toast con Mensaje Custom
            </button>
            <button class="btn btn-secondary" onclick="checkNotificationStatus()">
                Verificar Estado
            </button>
            <button class="btn btn-secondary" onclick="testManualNotification()">
                Probar Notificación Manual
            </button>
        </div>
        
        <div id="log" style="background: #f7fafc; padding: 20px; border-radius: 8px; font-family: monospace; font-size: 14px; max-height: 300px; overflow-y: auto;">
            <strong>Log de eventos:</strong><br>
        </div>
    </div>

    <!-- Incluir el componente toast simple -->
    @include('components.simple-notification-toast')

    <script>
        // Override console.log para mostrar en la página también
        const originalLog = console.log;
        console.log = function(...args) {
            originalLog.apply(console, args);
            const logDiv = document.getElementById('log');
            const time = new Date().toLocaleTimeString();
            logDiv.innerHTML += `[${time}] ${args.join(' ')}<br>`;
            logDiv.scrollTop = logDiv.scrollHeight;
        };

        // Verificar estado inicial
        document.addEventListener('DOMContentLoaded', function() {
            updateBrowserStatus();
        });

        function updateBrowserStatus() {
            const statusDiv = document.getElementById('browser-status');
            let status = [];
            
            if ('serviceWorker' in navigator) {
                status.push('✅ Service Worker soportado');
            } else {
                status.push('❌ Service Worker NO soportado');
            }
            
            if ('PushManager' in window) {
                status.push('✅ Push Manager soportado');
            } else {
                status.push('❌ Push Manager NO soportado');
            }
            
            status.push(`🔔 Permisos: ${Notification.permission}`);
            
            statusDiv.innerHTML = status.join('<br>');
            console.log('Estado del navegador actualizado:', status.join(', '));
        }

        async function testManualNotification() {
            try {
                console.log('🧪 Probando notificación manual...');
                
                const response = await fetch('/push/test', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        title: 'Prueba de Toast',
                        message: 'Esta es una notificación de prueba desde el toast',
                        user_id: 6 // Cambiar por el ID del usuario actual si es necesario
                    })
                });
                
                if (response.ok) {
                    const result = await response.json();
                    console.log('✅ Notificación enviada:', result);
                } else {
                    console.log('❌ Error al enviar notificación:', response.status);
                }
            } catch (error) {
                console.error('❌ Error:', error);
            }
        }

        // Override de la función checkNotificationStatus para que no se ejecute automáticamente
        const originalCheckNotificationStatus = checkNotificationStatus;
        checkNotificationStatus = function() {
            console.log('🔍 Verificando estado de notificaciones...');
            updateBrowserStatus();
            return originalCheckNotificationStatus();
        };
    </script>
</body>
</html>
