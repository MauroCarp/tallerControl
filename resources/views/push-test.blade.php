<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Push Notifications Test - TallerControl</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .status {
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            font-weight: bold;
        }
        .status.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .status.info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        button:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }
        .form-group {
            margin: 20px 0;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        textarea {
            height: 100px;
            resize: vertical;
        }
        .feature-section {
            margin: 30px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f8f9fa;
        }
        .button-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔔 Push Notifications Test</h1>
        <p style="text-align: center; color: #666; margin-bottom: 30px;">
            Prueba las notificaciones push de TallerControl
        </p>

        <div id="status" class="status info">
            Verificando soporte para notificaciones push...
        </div>

        <div class="feature-section">
            <h3>🔧 Configuración</h3>
            <p><strong>⚠️ IMPORTANTE:</strong> Debes completar todos estos pasos EN ORDEN para que funcionen las notificaciones:</p>
            <div class="button-group">
                <button id="checkSupport" onclick="checkNotificationSupport()">
                    1️⃣ Verificar Soporte
                </button>
                <button id="requestPermission" onclick="requestNotificationPermission()" disabled>
                    2️⃣ Solicitar Permisos
                </button>
                <button id="clearSubscriptions" onclick="clearAllSubscriptions()" style="background: #dc3545;">
                    🧹 Limpiar Suscripciones
                </button>
                <button id="subscribeBtn" onclick="subscribeToNotifications()" disabled>
                    3️⃣ Suscribirse
                </button>
                <button id="unsubscribeBtn" onclick="unsubscribeFromNotifications()" disabled>
                    ❌ Desuscribirse
                </button>
            </div>
            <div class="status-info" style="margin-top: 15px; padding: 10px; background: #e9ecef; border-radius: 5px;">
                <p><strong>Estado actual:</strong></p>
                <ul>
                    <li>🔑 Permisos: <span id="permissionStatus">Verificando...</span></li>
                    <li>📡 Suscripción: <span id="subscriptionStatus">Verificando...</span></li>
                    <li>⚙️ Service Worker: <span id="swStatus">Verificando...</span></li>
                </ul>
            </div>
        </div>

        <div class="feature-section">
            <h3>📢 Enviar Notificación de Prueba</h3>
            <p><strong>⚠️ Nota:</strong> Solo funcionará si completaste los pasos de configuración arriba.</p>
            <form id="testForm" onsubmit="sendTestNotification(event)">
                <div class="form-group">
                    <label for="title">Título de la Notificación:</label>
                    <input type="text" id="title" name="title" value="¡Hola desde TallerControl!" required>
                </div>
                
                <div class="form-group">
                    <label for="message">Mensaje:</label>
                    <textarea id="message" name="message" required>Esta es una notificación de prueba del sistema TallerControl. ¡Todo funciona correctamente!</textarea>
                </div>
                
                <div class="form-group">
                    <label for="userId">ID de Usuario (opcional - dejar vacío para enviar a todos):</label>
                    <input type="number" id="userId" name="userId" placeholder="Ej: 1">
                </div>

                <button type="submit" id="sendBtn" disabled>
                    4️⃣ Enviar Notificación
                </button>
                <p style="font-size: 14px; color: #666; margin-top: 10px;">
                    Si ves "enviada a 0 dispositivos", significa que no estás suscrito. Completa los pasos de arriba primero.
                </p>
            </form>
        </div>

        <div class="feature-section">
            <h3>ℹ️ Información del Sistema</h3>
            <div id="systemInfo">
                <p><strong>Navegador:</strong> <span id="browserInfo"></span></p>
                <p><strong>Permisos:</strong> <span id="permissionStatus"></span></p>
                <p><strong>Estado de Suscripción:</strong> <span id="subscriptionStatus"></span></p>
                <p><strong>Service Worker:</strong> <span id="swStatus"></span></p>
            </div>
        </div>
    </div>

    <script src="/js/push-notifications.js"></script>
    <script>
        let isSubscribed = false;

        // Inicializar cuando se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            checkNotificationSupport();
            updateSystemInfo();
            
            // Verificar estado cada 2 segundos
            setInterval(updateSystemInfo, 2000);
        });

        async function checkNotificationSupport() {
            const statusDiv = document.getElementById('status');
            
            if (!window.pushManager || !window.pushManager.isSupported) {
                statusDiv.className = 'status error';
                statusDiv.innerHTML = '❌ Tu navegador no soporta push notifications';
                return;
            }

            statusDiv.className = 'status success';
            statusDiv.innerHTML = '✅ Push notifications soportadas';
            
            // Verificar si el service worker está registrado
            try {
                const registration = await navigator.serviceWorker.getRegistration();
                const swStatus = document.getElementById('swStatus');
                
                if (registration) {
                    swStatus.textContent = '✅ Registrado';
                    swStatus.style.color = 'green';
                } else {
                    swStatus.textContent = '⚠️ No registrado';
                    swStatus.style.color = 'orange';
                    
                    // Intentar registrar el service worker
                    try {
                        await window.pushManager.registerServiceWorker();
                        swStatus.textContent = '✅ Registrado automáticamente';
                        swStatus.style.color = 'green';
                    } catch (regError) {
                        console.error('Error registrando SW:', regError);
                        swStatus.textContent = '❌ Error al registrar';
                        swStatus.style.color = 'red';
                    }
                }
            } catch (error) {
                console.error('Error verificando SW:', error);
                document.getElementById('swStatus').textContent = '❌ Error';
            }
            
            document.getElementById('requestPermission').disabled = false;
            
            // Verificar si ya está suscrito
            checkSubscriptionStatus();
        }

        async function requestNotificationPermission() {
            try {
                const permission = await window.pushManager.requestPermission();
                const statusDiv = document.getElementById('status');
                
                if (permission === 'granted') {
                    statusDiv.className = 'status success';
                    statusDiv.innerHTML = '✅ Permisos concedidos';
                    document.getElementById('subscribeBtn').disabled = false;
                } else {
                    statusDiv.className = 'status error';
                    statusDiv.innerHTML = '❌ Permisos denegados';
                }
            } catch (error) {
                document.getElementById('status').className = 'status error';
                document.getElementById('status').innerHTML = '❌ Error al solicitar permisos: ' + error.message;
            }
        }

        async function subscribeToNotifications() {
            try {
                const statusDiv = document.getElementById('status');
                statusDiv.className = 'status info';
                statusDiv.innerHTML = '🔄 Suscribiendo...';

                await window.subscribeToPush();
                
                statusDiv.className = 'status success';
                statusDiv.innerHTML = '✅ Suscripción exitosa';
                
                isSubscribed = true;
                updateButtonStates();
            } catch (error) {
                document.getElementById('status').className = 'status error';
                document.getElementById('status').innerHTML = '❌ Error al suscribirse: ' + error.message;
            }
        }

        async function unsubscribeFromNotifications() {
            try {
                await window.unsubscribeFromPush();
                
                document.getElementById('status').className = 'status info';
                document.getElementById('status').innerHTML = '📴 Desuscripción exitosa';
                
                isSubscribed = false;
                updateButtonStates();
            } catch (error) {
                document.getElementById('status').className = 'status error';
                document.getElementById('status').innerHTML = '❌ Error al desuscribirse: ' + error.message;
            }
        }

        async function clearAllSubscriptions() {
            try {
                const statusDiv = document.getElementById('status');
                statusDiv.className = 'status info';
                statusDiv.innerHTML = '🧹 Limpiando suscripciones...';

                // Limpiar service worker
                if ('serviceWorker' in navigator) {
                    const registrations = await navigator.serviceWorker.getRegistrations();
                    for (let registration of registrations) {
                        // Desuscribirse de push notifications
                        const subscription = await registration.pushManager.getSubscription();
                        if (subscription) {
                            await subscription.unsubscribe();
                        }
                        // Desregistrar service worker
                        await registration.unregister();
                    }
                }

                // Limpiar datos locales
                if ('localStorage' in window) {
                    localStorage.clear();
                }
                if ('sessionStorage' in window) {
                    sessionStorage.clear();
                }

                statusDiv.className = 'status success';
                statusDiv.innerHTML = '✅ Todas las suscripciones limpiadas. Recarga la página para empezar de nuevo.';
                
                // Deshabilitar botones
                document.getElementById('subscribeBtn').disabled = true;
                document.getElementById('unsubscribeBtn').disabled = true;
                document.getElementById('sendBtn').disabled = true;
                
                isSubscribed = false;
                
                // Sugerir recargar la página
                setTimeout(() => {
                    if (confirm('¿Quieres recargar la página para empezar limpio?')) {
                        window.location.reload();
                    }
                }, 2000);
                
            } catch (error) {
                document.getElementById('status').className = 'status error';
                document.getElementById('status').innerHTML = '❌ Error limpiando suscripciones: ' + error.message;
            }
        }

        async function sendTestNotification(event) {
            event.preventDefault();
            
            const title = document.getElementById('title').value;
            const message = document.getElementById('message').value;
            const userIdValue = document.getElementById('userId').value.trim();
            const userId = userIdValue === '' ? null : parseInt(userIdValue);
            
            console.log('Enviando notificación con:', { title, message, userId, userIdValue });
            
            try {
                const statusDiv = document.getElementById('status');
                statusDiv.className = 'status info';
                statusDiv.innerHTML = '📤 Enviando notificación...';

                // Primero verificar si realmente estamos suscrito
                const registration = await navigator.serviceWorker.getRegistration();
                if (!registration) {
                    throw new Error('Service Worker no registrado');
                }
                
                const subscription = await registration.pushManager.getSubscription();
                if (!subscription) {
                    throw new Error('No hay suscripción activa. Completa el paso 3 primero.');
                }
                
                console.log('Enviando desde endpoint:', subscription.endpoint);

                const result = await window.sendTestPush(title, message, userId);
                
                statusDiv.className = 'status success';
                statusDiv.innerHTML = `✅ Notificación enviada a ${result.notifications_sent} de ${result.total_subscriptions || 'N/A'} dispositivo(s)`;
                
                // Si se envió a 0 dispositivos, mostrar información adicional
                if (result.notifications_sent === 0) {
                    if (result.total_subscriptions === 0) {
                        statusDiv.innerHTML += '<br><small>⚠️ No hay suscripciones en la base de datos. Completa el paso 3 (Suscribirse).</small>';
                    } else {
                        statusDiv.innerHTML += '<br><small>⚠️ Error enviando a las suscripciones existentes. Verifica los logs del servidor.</small>';
                    }
                }
            } catch (error) {
                document.getElementById('status').className = 'status error';
                document.getElementById('status').innerHTML = '❌ Error al enviar notificación: ' + error.message;
            }
        }

        async function checkSubscriptionStatus() {
            try {
                // Verificar directamente con el Service Worker
                const registration = await navigator.serviceWorker.getRegistration();
                if (registration) {
                    const subscription = await registration.pushManager.getSubscription();
                    isSubscribed = !!subscription;
                    
                    if (subscription) {
                        console.log('Suscripción encontrada:', subscription.endpoint);
                    } else {
                        console.log('No hay suscripción activa');
                    }
                } else {
                    isSubscribed = false;
                    console.log('Service Worker no registrado');
                }
                
                updateButtonStates();
            } catch (error) {
                console.error('Error verificando suscripción:', error);
                isSubscribed = false;
                updateButtonStates();
            }
        }

        function updateButtonStates() {
            document.getElementById('subscribeBtn').disabled = isSubscribed;
            document.getElementById('unsubscribeBtn').disabled = !isSubscribed;
            document.getElementById('sendBtn').disabled = !isSubscribed;
        }

        async function updateSystemInfo() {
            // Información del navegador
            document.getElementById('browserInfo').textContent = navigator.userAgent.split(' ').slice(-2).join(' ');
            
            // Estado de permisos
            if ('Notification' in window) {
                const permission = Notification.permission;
                const permStatus = document.getElementById('permissionStatus');
                permStatus.textContent = permission === 'granted' ? 'Concedidos ✅' : 
                                       permission === 'denied' ? 'Denegados ❌' : 
                                       'Pendientes ⚠️';
                permStatus.style.color = permission === 'granted' ? 'green' : 
                                        permission === 'denied' ? 'red' : 'orange';
            } else {
                document.getElementById('permissionStatus').textContent = 'No soportado ❌';
                document.getElementById('permissionStatus').style.color = 'red';
            }
            
            // Estado de suscripción - verificar en tiempo real
            try {
                const registration = await navigator.serviceWorker.getRegistration();
                if (registration) {
                    const subscription = await registration.pushManager.getSubscription();
                    const subStatus = document.getElementById('subscriptionStatus');
                    subStatus.textContent = subscription ? 'Suscrito ✅' : 'No suscrito ❌';
                    subStatus.style.color = subscription ? 'green' : 'red';
                    isSubscribed = !!subscription;
                } else {
                    document.getElementById('subscriptionStatus').textContent = 'SW no registrado ❌';
                    document.getElementById('subscriptionStatus').style.color = 'red';
                    isSubscribed = false;
                }
            } catch (error) {
                document.getElementById('subscriptionStatus').textContent = 'Error ❌';
                document.getElementById('subscriptionStatus').style.color = 'red';
                isSubscribed = false;
            }
            
            // Estado del Service Worker
            if ('serviceWorker' in navigator) {
                try {
                    const registration = await navigator.serviceWorker.getRegistration();
                    const swStatus = document.getElementById('swStatus');
                    if (registration) {
                        if (registration.active) {
                            swStatus.textContent = 'Activo ✅';
                            swStatus.style.color = 'green';
                        } else if (registration.installing) {
                            swStatus.textContent = 'Instalando ⚠️';
                            swStatus.style.color = 'orange';
                        } else {
                            swStatus.textContent = 'Registrado ⚠️';
                            swStatus.style.color = 'orange';
                        }
                    } else {
                        swStatus.textContent = 'No registrado ❌';
                        swStatus.style.color = 'red';
                    }
                } catch (error) {
                    document.getElementById('swStatus').textContent = 'Error ❌';
                    document.getElementById('swStatus').style.color = 'red';
                }
            } else {
                document.getElementById('swStatus').textContent = 'No soportado ❌';
                document.getElementById('swStatus').style.color = 'red';
            }
            
            // Actualizar botones
            updateButtonStates();
        }
    </script>
</body>
</html>
