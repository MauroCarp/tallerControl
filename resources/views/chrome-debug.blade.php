<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Diagnóstico Chrome - Push Notifications</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background: #d4edda; border-color: #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .warning { background: #fff3cd; border-color: #ffeaa7; color: #856404; }
        .info { background: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        button { padding: 10px 15px; margin: 5px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnóstico Chrome - Push Notifications</h1>
        
        <div class="test-section">
            <h3>Información del Navegador</h3>
            <div id="browser-info">
                <p><strong>User Agent:</strong> <span id="userAgent"></span></p>
                <p><strong>Es Chrome:</strong> <span id="isChrome"></span></p>
                <p><strong>Versión Chrome:</strong> <span id="chromeVersion"></span></p>
                <p><strong>Es HTTPS:</strong> <span id="isHttps"></span></p>
                <p><strong>Host:</strong> <span id="hostInfo"></span></p>
            </div>
        </div>

        <div class="test-section">
            <h3>Soporte de APIs</h3>
            <div id="api-support">
                <p><strong>Service Worker:</strong> <span id="swSupport"></span></p>
                <p><strong>Push Manager:</strong> <span id="pushSupport"></span></p>
                <p><strong>Notifications:</strong> <span id="notificationSupport"></span></p>
                <p><strong>Estado de Permisos:</strong> <span id="permissionState"></span></p>
            </div>
        </div>

        <div class="test-section">
            <h3>Pruebas Paso a Paso</h3>
            <button class="btn-primary" onclick="testServiceWorkerRegistration()">1. Probar Service Worker</button>
            <button class="btn-primary" onclick="testPermissionRequest()">2. Solicitar Permisos</button>
            <button class="btn-primary" onclick="testSubscription()">3. Crear Suscripción</button>
            <button class="btn-success" onclick="testNotification()">4. Enviar Notificación</button>
            <button class="btn-danger" onclick="clearEverything()">🧹 Limpiar Todo</button>
        </div>

        <div class="test-section">
            <h3>Logs de Depuración</h3>
            <div id="debug-log" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #f8f9fa;">
                <em>Los logs aparecerán aquí...</em>
            </div>
            <button class="btn-primary" onclick="clearLogs()">Limpiar Logs</button>
        </div>

        <div class="test-section">
            <h3>Estado del Service Worker</h3>
            <div id="sw-details"></div>
            <button class="btn-primary" onclick="checkServiceWorkerDetails()">Verificar Detalles</button>
        </div>
    </div>

    <script src="/js/push-notifications.js"></script>
    <script>
        // Función para logging
        function log(message, type = 'info') {
            const timestamp = new Date().toLocaleTimeString();
            const logDiv = document.getElementById('debug-log');
            const logEntry = document.createElement('div');
            logEntry.innerHTML = `<span style="color: #666;">[${timestamp}]</span> <span style="color: ${getLogColor(type)};">${message}</span>`;
            logDiv.appendChild(logEntry);
            logDiv.scrollTop = logDiv.scrollHeight;
            console.log(`[${timestamp}] ${message}`);
        }

        function getLogColor(type) {
            switch(type) {
                case 'success': return '#28a745';
                case 'error': return '#dc3545';
                case 'warning': return '#ffc107';
                default: return '#17a2b8';
            }
        }

        function clearLogs() {
            document.getElementById('debug-log').innerHTML = '<em>Los logs aparecerán aquí...</em>';
        }

        // Información del navegador
        function initBrowserInfo() {
            const ua = navigator.userAgent;
            const isChrome = /Chrome/.test(ua) && /Google Inc/.test(navigator.vendor);
            const chromeMatch = ua.match(/Chrome\/(\d+)/);
            const chromeVersion = chromeMatch ? chromeMatch[1] : 'N/A';
            
            document.getElementById('userAgent').textContent = ua;
            document.getElementById('isChrome').textContent = isChrome ? 'Sí' : 'No';
            document.getElementById('chromeVersion').textContent = chromeVersion;
            document.getElementById('isHttps').textContent = location.protocol === 'https:' ? 'Sí' : 'No';
            document.getElementById('hostInfo').textContent = location.host;

            if (!isChrome) {
                log('⚠️ Este diagnóstico está optimizado para Chrome', 'warning');
            }
            if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                log('⚠️ Chrome requiere HTTPS para notificaciones push (excepto localhost)', 'warning');
            }
        }

        // Soporte de APIs
        function checkAPISupport() {
            const swSupported = 'serviceWorker' in navigator;
            const pushSupported = 'PushManager' in window;
            const notificationSupported = 'Notification' in window;
            const permissionState = notificationSupported ? Notification.permission : 'no disponible';

            document.getElementById('swSupport').textContent = swSupported ? '✅ Soportado' : '❌ No soportado';
            document.getElementById('pushSupport').textContent = pushSupported ? '✅ Soportado' : '❌ No soportado';
            document.getElementById('notificationSupport').textContent = notificationSupported ? '✅ Soportado' : '❌ No soportado';
            document.getElementById('permissionState').textContent = permissionState;

            if (!swSupported || !pushSupported || !notificationSupported) {
                log('❌ Alguna API requerida no está soportada', 'error');
            } else {
                log('✅ Todas las APIs necesarias están soportadas', 'success');
            }
        }

        // Pruebas
        async function testServiceWorkerRegistration() {
            log('🔄 Registrando Service Worker...', 'info');
            try {
                const registration = await navigator.serviceWorker.register('/serviceworker.js');
                log(`✅ Service Worker registrado: ${registration.scope}`, 'success');
                
                // Esperar a que esté activo
                if (registration.installing) {
                    log('⏳ Service Worker instalándose...', 'info');
                    await new Promise((resolve) => {
                        registration.installing.addEventListener('statechange', (e) => {
                            log(`📱 Estado SW: ${e.target.state}`, 'info');
                            if (e.target.state === 'activated') {
                                resolve();
                            }
                        });
                    });
                }

                if (registration.active) {
                    log('✅ Service Worker activo y listo', 'success');
                } else {
                    log('⚠️ Service Worker registrado pero no activo', 'warning');
                }

                checkServiceWorkerDetails();
            } catch (error) {
                log(`❌ Error registrando Service Worker: ${error.message}`, 'error');
            }
        }

        async function testPermissionRequest() {
            log('🔄 Solicitando permisos de notificación...', 'info');
            try {
                const permission = await Notification.requestPermission();
                log(`📋 Permiso otorgado: ${permission}`, permission === 'granted' ? 'success' : 'error');
                
                // Actualizar estado
                document.getElementById('permissionState').textContent = permission;
                
                if (permission !== 'granted') {
                    log('❌ Sin permisos, las notificaciones no funcionarán', 'error');
                }
            } catch (error) {
                log(`❌ Error solicitando permisos: ${error.message}`, 'error');
            }
        }

        async function testSubscription() {
            log('🔄 Creando suscripción push...', 'info');
            try {
                const registration = await navigator.serviceWorker.getRegistration();
                if (!registration) {
                    throw new Error('Service Worker no registrado');
                }

                // Verificar si ya hay una suscripción
                const existingSubscription = await registration.pushManager.getSubscription();
                if (existingSubscription) {
                    log('⚠️ Ya existe una suscripción, eliminándola...', 'warning');
                    await existingSubscription.unsubscribe();
                }

                // Obtener clave VAPID
                log('🔑 Obteniendo clave VAPID...', 'info');
                const response = await fetch('/push/vapid-public-key');
                const { publicKey } = await response.json();
                log(`🔑 Clave VAPID obtenida: ${publicKey.substring(0, 20)}...`, 'info');

                // Crear nueva suscripción
                log('📝 Creando nueva suscripción...', 'info');
                const subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(publicKey)
                });

                log(`✅ Suscripción creada: ${subscription.endpoint.substring(0, 50)}...`, 'success');

                // Enviar al servidor
                log('📤 Enviando suscripción al servidor...', 'info');
                const saveResponse = await fetch('/push/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(subscription.toJSON())
                });

                if (saveResponse.ok) {
                    const result = await saveResponse.json();
                    log(`✅ Suscripción guardada en servidor: ID ${result.subscription_id}`, 'success');
                } else {
                    throw new Error(`Error del servidor: ${saveResponse.status}`);
                }

            } catch (error) {
                log(`❌ Error en suscripción: ${error.message}`, 'error');
            }
        }

        async function testNotification() {
            log('🔄 Enviando notificación de prueba...', 'info');
            try {
                const response = await fetch('/push/send-test', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        title: 'Prueba Chrome',
                        message: 'Esta es una prueba específica para Chrome',
                        user_id: null
                    })
                });

                if (response.ok) {
                    const result = await response.json();
                    log(`✅ Notificación enviada a ${result.notifications_sent} dispositivo(s)`, 'success');
                    
                    if (result.notifications_sent === 0) {
                        log('⚠️ No se enviaron notificaciones. Verifica la suscripción.', 'warning');
                    }
                } else {
                    throw new Error(`Error del servidor: ${response.status}`);
                }
            } catch (error) {
                log(`❌ Error enviando notificación: ${error.message}`, 'error');
            }
        }

        async function checkServiceWorkerDetails() {
            try {
                const registration = await navigator.serviceWorker.getRegistration();
                const detailsDiv = document.getElementById('sw-details');
                
                if (!registration) {
                    detailsDiv.innerHTML = '<p style="color: red;">❌ No hay Service Worker registrado</p>';
                    return;
                }

                let html = '<div style="font-family: monospace; font-size: 12px;">';
                html += `<p><strong>Scope:</strong> ${registration.scope}</p>`;
                html += `<p><strong>Installing:</strong> ${registration.installing ? '✅' : '❌'}</p>`;
                html += `<p><strong>Waiting:</strong> ${registration.waiting ? '✅' : '❌'}</p>`;
                html += `<p><strong>Active:</strong> ${registration.active ? '✅' : '❌'}</p>`;
                
                if (registration.active) {
                    html += `<p><strong>State:</strong> ${registration.active.state}</p>`;
                    html += `<p><strong>Script URL:</strong> ${registration.active.scriptURL}</p>`;
                }

                // Verificar suscripción
                const subscription = await registration.pushManager.getSubscription();
                html += `<p><strong>Suscripción:</strong> ${subscription ? '✅ Activa' : '❌ No activa'}</p>`;
                
                if (subscription) {
                    html += `<p><strong>Endpoint:</strong> ${subscription.endpoint.substring(0, 100)}...</p>`;
                }

                html += '</div>';
                detailsDiv.innerHTML = html;

            } catch (error) {
                document.getElementById('sw-details').innerHTML = `<p style="color: red;">❌ Error: ${error.message}</p>`;
            }
        }

        async function clearEverything() {
            log('🧹 Limpiando todo...', 'info');
            try {
                // Desregistrar service worker
                const registrations = await navigator.serviceWorker.getRegistrations();
                for (let registration of registrations) {
                    await registration.unregister();
                    log('🗑️ Service Worker desregistrado', 'info');
                }

                // Limpiar cache
                const cacheNames = await caches.keys();
                for (let cacheName of cacheNames) {
                    await caches.delete(cacheName);
                    log(`🗑️ Cache eliminado: ${cacheName}`, 'info');
                }

                log('✅ Todo limpiado. Recarga la página para empezar de nuevo.', 'success');
                
                setTimeout(() => {
                    window.location.reload();
                }, 2000);

            } catch (error) {
                log(`❌ Error limpiando: ${error.message}`, 'error');
            }
        }

        // Utility function para convertir VAPID key
        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding)
                .replace(/-/g, '+')
                .replace(/_/g, '/');

            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);

            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }

        // Inicializar
        window.addEventListener('load', () => {
            initBrowserInfo();
            checkAPISupport();
            checkServiceWorkerDetails();
            log('🚀 Diagnóstico iniciado. Usa los botones para probar paso a paso.', 'info');
        });
    </script>
</body>
</html>
