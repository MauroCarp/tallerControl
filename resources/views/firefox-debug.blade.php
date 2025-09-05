<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Debug Firefox - Push Notifications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        button {
            background: #007cba;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 5px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #005a87;
        }
        .output {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            margin-top: 10px;
            white-space: pre-wrap;
            font-family: monospace;
            max-height: 400px;
            overflow-y: auto;
        }
        .url-info {
            background: #e3f2fd;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🦊 Debug Firefox - Push Notifications</h1>
        
        <div class="url-info">
            <strong>URL actual:</strong> <span id="current-url"></span><br>
            <strong>Pathname:</strong> <span id="current-path"></span><br>
            <strong>User Agent:</strong> <span id="user-agent"></span>
        </div>
        
        <div>
            <h3>Acciones de Diagnóstico:</h3>
            <button onclick="runBasicDiagnosis()">🔍 Diagnóstico Básico</button>
            <button onclick="runFirefoxDiagnosis()">🦊 Diagnóstico Firefox</button>
            <button onclick="testServiceWorkerRegistration()">📝 Test Registro SW</button>
            <button onclick="testPushSubscription()">📤 Test Suscripción Push</button>
            <button onclick="forceReregister()">🔄 Forzar Re-registro</button>
            <button onclick="clearOutput()">🧹 Limpiar Output</button>
        </div>
        
        <div class="output" id="output"></div>
    </div>

    <!-- Incluir el componente del toast -->
    @include('components.simple-notification-toast')

    <script>
        // Mostrar información de la página
        document.getElementById('current-url').textContent = window.location.href;
        document.getElementById('current-path').textContent = window.location.pathname;
        document.getElementById('user-agent').textContent = navigator.userAgent;
        
        function log(message) {
            const output = document.getElementById('output');
            const timestamp = new Date().toLocaleTimeString();
            output.textContent += `[${timestamp}] ${message}\n`;
            output.scrollTop = output.scrollHeight;
            console.log(message);
        }
        
        function clearOutput() {
            document.getElementById('output').textContent = '';
        }
        
        async function runBasicDiagnosis() {
            log('🔍 === DIAGNÓSTICO BÁSICO ===');
            
            // Verificar soporte
            log('ServiceWorker support: ' + ('serviceWorker' in navigator));
            log('PushManager support: ' + ('PushManager' in window));
            log('Notification support: ' + ('Notification' in window));
            log('Notification permission: ' + Notification.permission);
            
            // Verificar token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            log('CSRF Token available: ' + !!csrfToken);
            
            log('=== FIN DIAGNÓSTICO BÁSICO ===\n');
        }
        
        async function runFirefoxDiagnosis() {
            log('🦊 === DIAGNÓSTICO FIREFOX ===');
            
            try {
                // Verificar todas las registraciones
                const registrations = await navigator.serviceWorker.getRegistrations();
                log(`Total registraciones: ${registrations.length}`);
                
                registrations.forEach((reg, index) => {
                    log(`Registración ${index + 1}:`);
                    log(`  - Scope: ${reg.scope}`);
                    log(`  - Active: ${!!reg.active}`);
                    log(`  - Installing: ${!!reg.installing}`);
                    log(`  - Waiting: ${!!reg.waiting}`);
                });
                
                // Verificar registración específica
                const rootReg = await navigator.serviceWorker.getRegistration('/');
                if (rootReg) {
                    log('✅ Registración en "/" encontrada');
                    log(`  - Scope: ${rootReg.scope}`);
                    log(`  - Active: ${!!rootReg.active}`);
                    
                    // Verificar suscripción
                    const subscription = await rootReg.pushManager.getSubscription();
                    if (subscription) {
                        log('✅ Suscripción push encontrada');
                        log(`  - Endpoint: ${subscription.endpoint.substring(0, 60)}...`);
                    } else {
                        log('❌ NO hay suscripción push');
                    }
                } else {
                    log('❌ NO hay registración en "/"');
                }
                
            } catch (error) {
                log('❌ Error en diagnóstico: ' + error.message);
            }
            
            log('=== FIN DIAGNÓSTICO FIREFOX ===\n');
        }
        
        async function testServiceWorkerRegistration() {
            log('📝 === TEST REGISTRO SERVICE WORKER ===');
            
            try {
                log('Registrando Service Worker...');
                const registration = await navigator.serviceWorker.register('/serviceworker.js', {
                    scope: '/'
                });
                
                log('✅ Service Worker registrado');
                log(`  - Scope: ${registration.scope}`);
                
                log('Esperando que esté ready...');
                await navigator.serviceWorker.ready;
                log('✅ Service Worker ready');
                
                // En Firefox, esperar más
                if (navigator.userAgent.includes('Firefox')) {
                    log('🦊 Firefox detectado, esperando activación...');
                    await new Promise(resolve => setTimeout(resolve, 2000));
                }
                
                // Verificar estado final
                const finalReg = await navigator.serviceWorker.getRegistration('/');
                if (finalReg && finalReg.active) {
                    log('✅ Service Worker activo y funcionando');
                } else {
                    log('⚠️ Service Worker registrado pero no activo');
                }
                
            } catch (error) {
                log('❌ Error registrando SW: ' + error.message);
            }
            
            log('=== FIN TEST REGISTRO ===\n');
        }
        
        async function testPushSubscription() {
            log('📤 === TEST SUSCRIPCIÓN PUSH ===');
            
            try {
                // Verificar permisos
                if (Notification.permission !== 'granted') {
                    log('Solicitando permisos de notificación...');
                    const permission = await Notification.requestPermission();
                    log(`Permisos obtenidos: ${permission}`);
                    
                    if (permission !== 'granted') {
                        log('❌ Permisos denegados');
                        return;
                    }
                }
                
                // Obtener registración
                const registration = await navigator.serviceWorker.getRegistration('/');
                if (!registration) {
                    log('❌ No hay Service Worker registrado');
                    return;
                }
                
                // Obtener clave VAPID
                log('Obteniendo clave VAPID...');
                const vapidResponse = await fetch('/push/vapid-public-key');
                const vapidData = await vapidResponse.json();
                log(`✅ Clave VAPID obtenida: ${vapidData.publicKey.substring(0, 20)}...`);
                
                // Crear suscripción
                log('Creando suscripción push...');
                const subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(vapidData.publicKey)
                });
                
                log('✅ Suscripción creada localmente');
                log(`  - Endpoint: ${subscription.endpoint.substring(0, 60)}...`);
                
                // Enviar al servidor
                log('Enviando suscripción al servidor...');
                const response = await fetch('/push-subscriptions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(subscription.toJSON())
                });
                
                if (response.ok) {
                    const result = await response.json();
                    log('✅ Suscripción guardada en servidor');
                    log(`  - ID: ${result.subscription_id}`);
                } else {
                    log('❌ Error guardando en servidor: ' + response.status);
                }
                
            } catch (error) {
                log('❌ Error en suscripción: ' + error.message);
            }
            
            log('=== FIN TEST SUSCRIPCIÓN ===\n');
        }
        
        async function forceReregister() {
            log('🔄 === FORZAR RE-REGISTRO ===');
            
            try {
                // Desregistrar todos
                const registrations = await navigator.serviceWorker.getRegistrations();
                log(`Desregistrando ${registrations.length} Service Workers...`);
                
                for (let reg of registrations) {
                    await reg.unregister();
                    log(`✅ Desregistrado: ${reg.scope}`);
                }
                
                // Limpiar caché
                if ('caches' in window) {
                    const cacheNames = await caches.keys();
                    for (let cacheName of cacheNames) {
                        await caches.delete(cacheName);
                    }
                    log(`✅ ${cacheNames.length} caches eliminados`);
                }
                
                // Esperar
                log('⏳ Esperando 3 segundos...');
                await new Promise(resolve => setTimeout(resolve, 3000));
                
                // Re-registrar
                log('📝 Re-registrando...');
                const registration = await navigator.serviceWorker.register('/serviceworker.js', {
                    scope: '/'
                });
                await navigator.serviceWorker.ready;
                
                log('✅ Re-registro completado');
                log('🔄 Recarga la página para verificar');
                
            } catch (error) {
                log('❌ Error en re-registro: ' + error.message);
            }
            
            log('=== FIN RE-REGISTRO ===\n');
        }
        
        // Función helper para VAPID
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
        
        // Auto-run basic diagnosis
        setTimeout(runBasicDiagnosis, 1000);
    </script>
</body>
</html>
