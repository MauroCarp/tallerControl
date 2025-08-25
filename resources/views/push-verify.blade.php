<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verificación de Push Notifications - TallerControl</title>
    @laravelPWA
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .check-item {
            background: #f8f9fa;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }
        .check-item.success {
            border-left-color: #28a745;
            background: #d4edda;
        }
        .check-item.error {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        .check-item.warning {
            border-left-color: #ffc107;
            background: #fff3cd;
        }
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin: 5px;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .actions {
            text-align: center;
            margin: 30px 0;
        }
        .log {
            background: #f1f3f4;
            border: 1px solid #dadce0;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            max-height: 300px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }
        .log-entry {
            margin: 5px 0;
            padding: 2px 0;
        }
        .log-entry.error { color: #d93025; }
        .log-entry.success { color: #137333; }
        .log-entry.warning { color: #ea8600; }
        .log-entry.info { color: #1a73e8; }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <span>🔔</span>
            Verificación de Push Notifications
        </h1>

        <div id="checks">
            <div class="check-item" id="browser-support">
                <strong>🌐 Soporte del navegador:</strong>
                <span id="browser-support-status">Verificando...</span>
            </div>
            
            <div class="check-item" id="service-worker">
                <strong>⚙️ Service Worker:</strong>
                <span id="service-worker-status">Verificando...</span>
            </div>
            
            <div class="check-item" id="notification-permission">
                <strong>🔔 Permisos de notificación:</strong>
                <span id="notification-permission-status">Verificando...</span>
            </div>
            
            <div class="check-item" id="vapid-key">
                <strong>🔑 Clave VAPID:</strong>
                <span id="vapid-key-status">Verificando...</span>
            </div>
            
            <div class="check-item" id="manifest">
                <strong>📋 Manifest PWA:</strong>
                <span id="manifest-status">Verificando...</span>
            </div>
            
            <div class="check-item" id="subscription">
                <strong>📱 Suscripción:</strong>
                <span id="subscription-status">Verificando...</span>
            </div>
        </div>

        <div class="actions">
            <button id="subscribe-btn" class="btn" onclick="subscribeToNotifications()" disabled>
                📝 Suscribirse
            </button>
            <button id="test-btn" class="btn" onclick="sendTestNotification()" disabled>
                🧪 Enviar Prueba
            </button>
            <button id="unsubscribe-btn" class="btn" onclick="unsubscribeFromNotifications()" disabled>
                ❌ Desuscribirse
            </button>
            <button class="btn" onclick="location.reload()">
                🔄 Recargar
            </button>
        </div>

        <div class="log" id="log">
            <div class="log-entry info">🚀 Iniciando verificación...</div>
        </div>
    </div>

    <script src="/js/push-notifications.js"></script>
    <script>
        const log = document.getElementById('log');
        
        function addLog(message, type = 'info') {
            const entry = document.createElement('div');
            entry.className = `log-entry ${type}`;
            entry.textContent = `${new Date().toLocaleTimeString()} - ${message}`;
            log.appendChild(entry);
            log.scrollTop = log.scrollHeight;
        }

        function updateCheckStatus(id, status, message) {
            const element = document.getElementById(id);
            const statusElement = document.getElementById(id + '-status');
            
            element.className = `check-item ${status}`;
            statusElement.textContent = message;
        }

        async function runDiagnostics() {
            addLog('Iniciando diagnósticos completos...', 'info');
            
            // 1. Verificar soporte del navegador
            const isSupported = 'serviceWorker' in navigator && 'PushManager' in window;
            updateCheckStatus('browser-support', 
                isSupported ? 'success' : 'error',
                isSupported ? '✅ Soportado' : '❌ No soportado'
            );
            addLog(`Soporte del navegador: ${isSupported ? 'Soportado' : 'No soportado'}`, isSupported ? 'success' : 'error');

            if (!isSupported) {
                addLog('❌ Tu navegador no soporta push notifications', 'error');
                return;
            }

            // 2. Verificar Service Worker
            try {
                let registration = await navigator.serviceWorker.getRegistration();
                if (!registration) {
                    addLog('Registrando Service Worker...', 'info');
                    registration = await navigator.serviceWorker.register('/serviceworker.js');
                }
                
                updateCheckStatus('service-worker', 'success', '✅ Registrado');
                addLog('Service Worker registrado correctamente', 'success');
            } catch (error) {
                updateCheckStatus('service-worker', 'error', '❌ Error al registrar');
                addLog(`Error con Service Worker: ${error.message}`, 'error');
            }

            // 3. Verificar permisos
            const permission = Notification.permission;
            updateCheckStatus('notification-permission',
                permission === 'granted' ? 'success' : permission === 'denied' ? 'error' : 'warning',
                permission === 'granted' ? '✅ Concedidos' : 
                permission === 'denied' ? '❌ Denegados' : '⚠️ Pendientes'
            );
            addLog(`Permisos de notificación: ${permission}`, permission === 'granted' ? 'success' : 'warning');

            // 4. Verificar clave VAPID
            try {
                const response = await fetch('/push/vapid-public-key');
                const data = await response.json();
                if (data.publicKey) {
                    updateCheckStatus('vapid-key', 'success', '✅ Obtenida correctamente');
                    addLog(`Clave VAPID obtenida: ${data.publicKey.substring(0, 20)}...`, 'success');
                } else {
                    throw new Error('No se recibió la clave VAPID');
                }
            } catch (error) {
                updateCheckStatus('vapid-key', 'error', '❌ Error al obtener');
                addLog(`Error obteniendo clave VAPID: ${error.message}`, 'error');
            }

            // 5. Verificar manifest
            try {
                const manifestResponse = await fetch('/manifest.json');
                const manifest = await manifestResponse.json();
                const hasGcmSenderId = manifest.gcm_sender_id || (manifest.custom && manifest.custom.gcm_sender_id);
                
                updateCheckStatus('manifest', 
                    hasGcmSenderId ? 'success' : 'warning',
                    hasGcmSenderId ? '✅ Con gcm_sender_id' : '⚠️ Sin gcm_sender_id'
                );
                addLog(`Manifest PWA: ${hasGcmSenderId ? 'Completo' : 'Sin gcm_sender_id'}`, hasGcmSenderId ? 'success' : 'warning');
            } catch (error) {
                updateCheckStatus('manifest', 'error', '❌ Error al cargar');
                addLog(`Error cargando manifest: ${error.message}`, 'error');
            }

            // 6. Verificar suscripción actual
            try {
                const isSubscribed = await window.pushManager.isSubscribed();
                updateCheckStatus('subscription',
                    isSubscribed ? 'success' : 'warning',
                    isSubscribed ? '✅ Suscrito' : '⚠️ No suscrito'
                );
                addLog(`Estado de suscripción: ${isSubscribed ? 'Suscrito' : 'No suscrito'}`, isSubscribed ? 'success' : 'info');
                
                // Habilitar botones según el estado
                document.getElementById('subscribe-btn').disabled = isSubscribed;
                document.getElementById('test-btn').disabled = !isSubscribed;
                document.getElementById('unsubscribe-btn').disabled = !isSubscribed;
                
            } catch (error) {
                updateCheckStatus('subscription', 'error', '❌ Error al verificar');
                addLog(`Error verificando suscripción: ${error.message}`, 'error');
            }

            addLog('✅ Diagnósticos completados', 'success');
        }

        async function subscribeToNotifications() {
            try {
                addLog('Iniciando suscripción...', 'info');
                await window.subscribeToPush();
                addLog('✅ Suscripción exitosa', 'success');
                runDiagnostics(); // Refrescar estado
            } catch (error) {
                addLog(`❌ Error en suscripción: ${error.message}`, 'error');
            }
        }

        async function sendTestNotification() {
            try {
                addLog('Enviando notificación de prueba...', 'info');
                const result = await window.sendTestPush('Prueba TallerControl', 'Esta es una notificación de prueba desde la página de verificación');
                addLog(`✅ Notificación enviada: ${result.notifications_sent} enviadas`, 'success');
            } catch (error) {
                addLog(`❌ Error enviando prueba: ${error.message}`, 'error');
            }
        }

        async function unsubscribeFromNotifications() {
            try {
                addLog('Cancelando suscripción...', 'info');
                await window.unsubscribeFromPush();
                addLog('✅ Suscripción cancelada', 'success');
                runDiagnostics(); // Refrescar estado
            } catch (error) {
                addLog(`❌ Error cancelando suscripción: ${error.message}`, 'error');
            }
        }

        // Ejecutar diagnósticos cuando se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            // Esperar a que se cargue el push manager
            setTimeout(runDiagnostics, 1000);
        });
    </script>
</body>
</html>
