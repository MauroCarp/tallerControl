<!-- Toast Simple de Notificaciones Push -->
<div id="simple-notification-toast" style="display: none; position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 350px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); padding: 20px; animation: slideIn 0.4s ease-out;">
        <div style="display: flex; align-items: center; margin-bottom: 15px;">
            <div style="margin-right: 12px;">🔔</div>
            <strong style="flex: 1;">Notificaciones Push</strong>
            <button onclick="closeSimpleToast()" style="background: none; border: none; color: white; font-size: 20px; cursor: pointer; padding: 5px;">&times;</button>
        </div>
        <p id="simple-toast-message" style="margin: 0 0 15px; font-size: 14px; opacity: 0.9;">
            Mantente al día con las últimas actualizaciones del taller.
        </p>
        <div style="display: flex; gap: 10px;">
            <button id="simple-enable-btn" onclick="enableSimpleNotifications()" 
                    style="padding: 8px 16px; border-radius: 6px; font-size: 14px; background: rgba(255, 255, 255, 0.2); color: white; border: none; cursor: pointer; transition: all 0.2s;">
                <span id="simple-btn-text">Activar Notificaciones</span>
                <span id="simple-btn-loading" style="display: none;">⏳</span>
            </button>
            <button id="simple-later-btn" onclick="closeSimpleToast()" 
                    style="padding: 8px 16px; border-radius: 6px; font-size: 14px; background: transparent; color: white; border: 1px solid rgba(255, 255, 255, 0.3); cursor: pointer;">
                Más tarde
            </button>
        </div>
    </div>
</div>

<style>
@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
</style>

<script>
let simpleToastShown = false;

function showSimpleToast(message, type = 'default', reason = '') {
    if (simpleToastShown) return;
    
    const toast = document.getElementById('simple-notification-toast');
    const messageEl = document.getElementById('simple-toast-message');
    
    // Actualizar interfaz según el tipo
    if (type === 'reactivate') {
        updateToastForReactivation(reason);
    } else if (type === 'new') {
        updateToastForNewUser();
    } else if (message) {
        messageEl.textContent = message;
    }
    
    toast.style.display = 'block';
    simpleToastShown = true;
    
    // Auto cerrar después de 10 segundos
    setTimeout(() => {
        if (toast.style.display !== 'none') {
            closeSimpleToast();
        }
    }, 10000);
}

function closeSimpleToast() {
    document.getElementById('simple-notification-toast').style.display = 'none';
}

function updateToastForReactivation(reason = '') {
    const messageEl = document.getElementById('simple-toast-message');
    const btnText = document.getElementById('simple-btn-text');
    const laterBtn = document.getElementById('simple-later-btn');
    
    let message = 'Parece que tus notificaciones se desconectaron. Reconectemos.';
    
    // Mensajes más específicos según la razón
    switch(reason) {
        case 'subscription_not_found':
            message = 'Tus notificaciones necesitan ser configuradas nuevamente.';
            break;
        case 'subscription_belongs_to_different_user':
            message = 'Estas notificaciones pertenecen a otro usuario. Configura las tuyas.';
            break;
        case 'server_error':
            message = 'Problema de conexión. Intentemos reconectar las notificaciones.';
            break;
        default:
            message = 'Parece que tus notificaciones se desconectaron. Reconectemos.';
    }
    
    messageEl.textContent = message;
    btnText.textContent = 'Reconectar';
    laterBtn.textContent = 'Omitir';
}

function updateToastForNewUser() {
    const messageEl = document.getElementById('simple-toast-message');
    const btnText = document.getElementById('simple-btn-text');
    const laterBtn = document.getElementById('simple-later-btn');
    
    messageEl.textContent = 'Mantente al día con las últimas actualizaciones del taller.';
    btnText.textContent = 'Activar Notificaciones';
    laterBtn.textContent = 'Más tarde';
}

async function enableSimpleNotifications() {
    const btn = document.getElementById('simple-enable-btn');
    const btnText = document.getElementById('simple-btn-text');
    const btnLoading = document.getElementById('simple-btn-loading');
    
    // Mostrar loading
    btn.disabled = true;
    btnText.style.display = 'none';
    btnLoading.style.display = 'inline';
    
    try {
        console.log('🔔 Iniciando activación de notificaciones...');
        
        // Verificar soporte
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            throw new Error('Tu navegador no soporta notificaciones push');
        }
        
        // Pedir permiso
        const permission = await Notification.requestPermission();
        console.log('📋 Permiso obtenido:', permission);
        
        if (permission !== 'granted') {
            throw new Error('Necesitas permitir las notificaciones para continuar');
        }
        
        // Registrar service worker
        console.log('🔧 Registrando service worker...');
        const registration = await navigator.serviceWorker.register('/serviceworker.js', {
            scope: '/' // Forzar scope en la raíz para Firefox
        });
        console.log('🔧 Service worker registrado, esperando que esté ready...');
        await navigator.serviceWorker.ready;
        console.log('✅ Service worker registrado y ready');
        console.log('🔍 Scope del SW:', registration.scope);
        
        // En Firefox, esperar un poco más para asegurar que el SW esté completamente activo
        if (navigator.userAgent.includes('Firefox')) {
            console.log('🔧 Firefox detectado, esperando activación completa...');
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            // Verificar que el SW esté realmente activo
            const activeRegistration = await navigator.serviceWorker.getRegistration('/');
            console.log('🔍 SW activo después de espera:', !!activeRegistration?.active);
        }
        
        // Obtener clave VAPID del servidor
        console.log('🔑 Obteniendo clave VAPID...');
        const vapidResponse = await fetch('/push/vapid-public-key');
        if (!vapidResponse.ok) {
            throw new Error(`Error HTTP ${vapidResponse.status} obteniendo clave VAPID`);
        }
        
        const vapidData = await vapidResponse.json();
        const vapidKey = vapidData.publicKey; // ✅ Corregido: usar camelCase
        
        if (!vapidKey) {
            throw new Error('No se recibió la clave VAPID del servidor');
        }
        
        console.log('✅ Clave VAPID obtenida:', vapidKey.substring(0, 20) + '...');
        
        // Función para convertir VAPID key a Uint8Array (igual que push-notifications.js)
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
        
        // Crear suscripción
        console.log('📝 Creando suscripción push...');
        const subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(vapidKey) // ✅ Convertir a Uint8Array
        });
        console.log('✅ Suscripción creada');
        
        // Enviar al servidor
        console.log('📤 Enviando suscripción al servidor...');
        const response = await fetch('/push-subscriptions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(subscription.toJSON())
        });
        
        if (!response.ok) {
            if (response.status === 401) {
                throw new Error('Debes iniciar sesión para activar las notificaciones');
            }
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.error || `Error del servidor: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('✅ Suscripción guardada en el servidor para usuario:', result.user_id);
        
        // Mostrar éxito
        btnText.textContent = '¡Activado!';
        btnText.style.display = 'inline';
        btnLoading.style.display = 'none';
        btn.disabled = false;
        btn.style.background = 'rgba(72, 187, 120, 0.9)';
        
        // Cerrar toast
        setTimeout(closeSimpleToast, 2000);
        
        console.log('🎉 ¡Notificaciones activadas correctamente!');
        
    } catch (error) {
        console.error('❌ Error al activar notificaciones:', error);
        
        // Mostrar error
        btnText.textContent = 'Error - Reintentar';
        btnText.style.display = 'inline';
        btnLoading.style.display = 'none';
        btn.disabled = false;
        
        const messageEl = document.getElementById('simple-toast-message');
        messageEl.textContent = error.message || 'Error al activar las notificaciones. Inténtalo de nuevo.';
        messageEl.style.color = '#ffeb3b';
    }
}

// Verificar automáticamente al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Solo verificar si estamos en una página autenticada
    if (document.querySelector('meta[name="csrf-token"]')) {
        // En Firefox, esperar un poco más para que el SW esté completamente cargado
        const isFirefox = navigator.userAgent.includes('Firefox');
        const delay = isFirefox ? 3000 : 2000;
        console.log('🔍 Esperando', delay, 'ms antes de verificar (Firefox:', isFirefox, ')');
        setTimeout(checkSimpleNotificationStatus, delay);
    } else {
        console.log('📝 Usuario no autenticado - toast no se mostrará');
    }
});

async function checkSimpleNotificationStatus() {
    if (simpleToastShown) return;
    
    console.log('🔍 Verificando estado de notificaciones...');
    console.log('🌐 URL actual:', window.location.href);
    console.log('🌐 Pathname:', window.location.pathname);
    console.log('🔐 Token CSRF disponible:', !!document.querySelector('meta[name="csrf-token"]'));
    console.log('🌐 User Agent:', navigator.userAgent);
    console.log('🔍 Es Firefox:', navigator.userAgent.includes('Firefox'));
    
    // Verificar soporte del navegador
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        console.log('❌ Navegador no compatible con notificaciones push');
        console.log('🔍 ServiceWorker support:', 'serviceWorker' in navigator);
        console.log('🔍 PushManager support:', 'PushManager' in window);
        return;
    }
    
    const permission = Notification.permission;
    console.log('📋 Permiso actual:', permission);
    
    if (permission === 'denied') {
        console.log('❌ Permisos denegados por el usuario');
        return;
    }
    
    if (permission === 'default') {
        console.log('📢 Mostrando toast - usuario no ha decidido');
        showSimpleToast('', 'new');
        return;
    }
    
    if (permission === 'granted') {
        try {
            // Verificar service worker - Firefox específico
            console.log('🔍 Verificando Service Worker...');
            console.log('🔍 Service Worker support:', 'serviceWorker' in navigator);
            
            // En Firefox, verificar todas las registraciones disponibles
            const registrations = await navigator.serviceWorker.getRegistrations();
            console.log('🔍 Todas las registraciones disponibles:', registrations.length);
            registrations.forEach((reg, index) => {
                console.log(`🔍 Registración ${index}:`, {
                    scope: reg.scope,
                    active: !!reg.active,
                    installing: !!reg.installing,
                    waiting: !!reg.waiting
                });
            });
            
            // Intentar obtener la registración específica - Firefox fix
            let registration = await navigator.serviceWorker.getRegistration('/');
            
            // Si no se encuentra en Firefox, intentar buscar en todas las registraciones
            if (!registration && navigator.userAgent.includes('Firefox')) {
                console.log('🦊 Firefox: No se encontró registración en /, buscando en todas...');
                const allRegistrations = await navigator.serviceWorker.getRegistrations();
                if (allRegistrations.length > 0) {
                    registration = allRegistrations[0]; // Usar la primera disponible
                    console.log('🦊 Firefox: Usando registración alternativa:', registration.scope);
                }
            }
            
            if (!registration) {
                console.log('📢 Mostrando toast - tiene permisos pero no service worker en scope raíz');
                console.log('🔍 Detalles: Permisos = granted, pero Service Worker no registrado en /');
                console.log('🔍 URL actual:', window.location.href);
                console.log('🔍 Total registraciones:', registrations.length);
                showSimpleToast('', 'new');
                return;
            }
            
            console.log('✅ Service Worker encontrado:', registration);
            console.log('🔍 SW Estado:', registration.active ? 'activo' : 'no activo');
            console.log('🔍 SW Installing:', registration.installing ? 'instalando' : 'no instalando');
            console.log('🔍 SW Waiting:', registration.waiting ? 'esperando' : 'no esperando');
            console.log('🔍 SW Scope:', registration.scope);
            
            // Verificar suscripción local
            const subscription = await registration.pushManager.getSubscription();
            if (!subscription) {
                console.log('📢 Mostrando toast - tiene permisos pero no suscripción local');
                console.log('🔍 Detalles: Permisos = granted, SW = registrado, pero NO hay suscripción push');
                console.log('🔧 Posible causa: Service Worker registrado pero nunca se suscribió a push notifications');
                console.log('🔍 URL actual:', window.location.href);
                console.log('🔍 Firefox específico:', navigator.userAgent.includes('Firefox'));
                showSimpleToast('', 'new');
                return;
            }
            
            console.log('✅ Suscripción local encontrada:', subscription);
            console.log('📍 Endpoint:', subscription.endpoint.substring(0, 50) + '...');
            
            // Verificar si la suscripción existe en el servidor
            console.log('🔍 Verificando suscripción en el servidor...');
            console.log('📍 Endpoint a verificar:', subscription.endpoint);
            try {
                const response = await fetch('/push/verify-subscription', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        endpoint: subscription.endpoint
                    })
                });
                
                console.log('📡 Respuesta del servidor:', response.status);
                
                if (response.ok) {
                    const result = await response.json();
                    console.log('📋 Resultado de verificación:', result);
                    
                    if (result.exists) {
                        console.log('✅ Usuario ya está suscrito correctamente - no mostrar toast');
                        return;
                    } else {
                        console.log('⚠️ Suscripción local existe pero no en servidor');
                        console.log('🔍 Razón:', result.reason);
                        console.log('📢 Mostrando toast de reconexión');
                        showSimpleToast('', 'reactivate', result.reason);
                    }
                } else {
                    console.log('❌ Error HTTP verificando servidor:', response.status);
                    const errorText = await response.text();
                    console.log('📄 Respuesta de error:', errorText);
                    showSimpleToast('', 'reactivate', 'server_error');
                }
            } catch (serverError) {
                console.error('❌ Error conectando con servidor:', serverError);
                showSimpleToast('', 'reactivate', 'server_error');
            }
            
        } catch (error) {
            console.error('❌ Error verificando suscripción:', error);
            showSimpleToast('', 'reactivate', 'verification_error');
        }
    }
}

// Función global para mostrar el toast manualmente
window.showNotificationToast = showSimpleToast;
window.checkNotificationStatus = checkSimpleNotificationStatus;

// Función específica para diagnosticar Firefox
window.diagnoseFirefoxPushNotifications = async function() {
    console.log('🦊 === DIAGNÓSTICO ESPECÍFICO PARA FIREFOX ===');
    
    console.log('1️⃣ Información del navegador:');
    console.log('   - User Agent:', navigator.userAgent);
    console.log('   - Es Firefox:', navigator.userAgent.includes('Firefox'));
    console.log('   - URL actual:', window.location.href);
    console.log('   - Pathname:', window.location.pathname);
    
    console.log('2️⃣ Verificando todas las registraciones de Service Worker:');
    try {
        const registrations = await navigator.serviceWorker.getRegistrations();
        console.log('   - Total registraciones:', registrations.length);
        
        registrations.forEach((reg, index) => {
            console.log(`   📋 Registración ${index + 1}:`);
            console.log(`      - Scope: ${reg.scope}`);
            console.log(`      - Active: ${!!reg.active}`);
            console.log(`      - Installing: ${!!reg.installing}`);
            console.log(`      - Waiting: ${!!reg.waiting}`);
            console.log(`      - Update found: ${!!reg.update}`);
        });
        
        // Verificar registración específica para la raíz
        console.log('3️⃣ Verificando registración específica para "/"');
        const rootRegistration = await navigator.serviceWorker.getRegistration('/');
        if (rootRegistration) {
            console.log('   ✅ Registración en "/" encontrada');
            console.log('   - Scope:', rootRegistration.scope);
            console.log('   - Estado:', rootRegistration.active ? 'activo' : 'inactivo');
            
            // Verificar suscripción push
            console.log('4️⃣ Verificando suscripción push:');
            try {
                const subscription = await rootRegistration.pushManager.getSubscription();
                if (subscription) {
                    console.log('   ✅ Suscripción encontrada');
                    console.log('   - Endpoint:', subscription.endpoint.substring(0, 60) + '...');
                    console.log('   - Keys disponibles:', !!subscription.getKey);
                } else {
                    console.log('   ❌ NO hay suscripción push');
                    console.log('   - Esto explica por qué aparece el toast en esta URL');
                }
            } catch (subError) {
                console.log('   ❌ Error obteniendo suscripción:', subError);
            }
        } else {
            console.log('   ❌ NO hay registración en "/"');
            console.log('   - Esto puede explicar el problema en Firefox');
        }
        
        // Verificar registración para la URL actual
        console.log('5️⃣ Verificando registración para URL actual');
        const currentRegistration = await navigator.serviceWorker.getRegistration(window.location.pathname);
        if (currentRegistration) {
            console.log('   ✅ Registración para path actual encontrada');
            console.log('   - Scope:', currentRegistration.scope);
        } else {
            console.log('   ⚠️ NO hay registración específica para path actual');
            console.log('   - Path actual:', window.location.pathname);
        }
        
    } catch (error) {
        console.log('   ❌ Error obteniendo registraciones:', error);
    }
    
    console.log('🦊 === FIN DIAGNÓSTICO FIREFOX ===');
};

// Función para forzar re-registro del Service Worker en Firefox
window.forceReregisterServiceWorkerFirefox = async function() {
    console.log('🦊 === FORZANDO RE-REGISTRO EN FIREFOX ===');
    
    try {
        // Desregistrar todos los Service Workers
        const registrations = await navigator.serviceWorker.getRegistrations();
        console.log('🗑️ Desregistrando', registrations.length, 'Service Workers...');
        
        for (let registration of registrations) {
            await registration.unregister();
            console.log('   ✅ Desregistrado:', registration.scope);
        }
        
        // Esperar más tiempo en Firefox
        console.log('⏳ Esperando 3 segundos (Firefox)...');
        await new Promise(resolve => setTimeout(resolve, 3000));
        
        // Re-registrar
        console.log('📝 Re-registrando Service Worker...');
        const registration = await navigator.serviceWorker.register('/serviceworker.js');
        await navigator.serviceWorker.ready;
        
        // Esperar activación completa en Firefox
        console.log('⏳ Esperando activación completa en Firefox...');
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        console.log('✅ Re-registro completado para Firefox');
        console.log('🔄 Recarga la página para verificar');
        
    } catch (error) {
        console.error('❌ Error en re-registro para Firefox:', error);
    }
};

// Función de diagnóstico completo
window.diagnosePushNotifications = async function() {
    console.log('🔬 === DIAGNÓSTICO COMPLETO DE PUSH NOTIFICATIONS ===');
    
    console.log('1️⃣ Soporte del navegador:');
    console.log('   - ServiceWorker:', 'serviceWorker' in navigator);
    console.log('   - PushManager:', 'PushManager' in window);
    console.log('   - Notification:', 'Notification' in window);
    
    console.log('2️⃣ Permisos:');
    console.log('   - Estado actual:', Notification.permission);
    
    console.log('3️⃣ Service Worker:');
    try {
        const registration = await navigator.serviceWorker.getRegistration();
        if (registration) {
            console.log('   ✅ Registrado en:', registration.scope);
            console.log('   - Activo:', !!registration.active);
            console.log('   - Instalando:', !!registration.installing);
            console.log('   - Esperando:', !!registration.waiting);
            
            console.log('4️⃣ Suscripción Push:');
            try {
                const subscription = await registration.pushManager.getSubscription();
                if (subscription) {
                    console.log('   ✅ Suscripción encontrada');
                    console.log('   - Endpoint:', subscription.endpoint.substring(0, 60) + '...');
                    console.log('   - Keys disponibles:', !!subscription.getKey);
                } else {
                    console.log('   ❌ NO hay suscripción push');
                    console.log('   - Esto explica por qué aparece el toast');
                }
            } catch (subError) {
                console.log('   ❌ Error obteniendo suscripción:', subError);
            }
        } else {
            console.log('   ❌ NO registrado');
        }
    } catch (swError) {
        console.log('   ❌ Error accediendo a Service Worker:', swError);
    }
    
    console.log('5️⃣ Estado de la página:');
    console.log('   - URL:', window.location.href);
    console.log('   - CSRF Token:', !!document.querySelector('meta[name="csrf-token"]'));
    console.log('   - Usuario autenticado:', !!document.querySelector('meta[name="csrf-token"]'));
    
    console.log('🔬 === FIN DIAGNÓSTICO ===');
    
    // También intentar verificar en el servidor si tenemos token
    if (document.querySelector('meta[name="csrf-token"]')) {
        console.log('6️⃣ Verificando suscripciones en el servidor...');
        try {
            const response = await fetch('/push/verify-subscription', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({endpoint: 'test-endpoint-for-debug'})
            });
            
            console.log('   - Respuesta del servidor:', response.status);
            if (response.ok) {
                const result = await response.json();
                console.log('   - Resultado:', result);
            }
        } catch (serverError) {
            console.log('   - Error conectando al servidor:', serverError);
        }
    }
};

// Función para intentar reparar el Service Worker
window.repairServiceWorker = async function() {
    console.log('🔧 === INTENTANDO REPARAR SERVICE WORKER ===');
    
    try {
        // 1. Desregistrar Service Workers existentes
        const registrations = await navigator.serviceWorker.getRegistrations();
        console.log('🗑️ Desregistrando', registrations.length, 'Service Workers...');
        
        for (let registration of registrations) {
            await registration.unregister();
            console.log('   ✅ Desregistrado:', registration.scope);
        }
        
        // 2. Limpiar cache del navegador relacionado con SW
        if ('caches' in window) {
            const cacheNames = await caches.keys();
            console.log('🗑️ Limpiando', cacheNames.length, 'caches...');
            for (let cacheName of cacheNames) {
                await caches.delete(cacheName);
                console.log('   ✅ Cache eliminado:', cacheName);
            }
        }
        
        // 3. Esperar un momento
        console.log('⏳ Esperando 2 segundos...');
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        // 4. Registrar nuevo Service Worker
        console.log('📝 Registrando nuevo Service Worker...');
        const registration = await navigator.serviceWorker.register('/serviceworker.js');
        await navigator.serviceWorker.ready;
        
        console.log('✅ Service Worker reparado y registrado');
        console.log('🔄 Recarga la página para verificar que funcione');
        
    } catch (error) {
        console.error('❌ Error reparando Service Worker:', error);
    }
};
</script>
