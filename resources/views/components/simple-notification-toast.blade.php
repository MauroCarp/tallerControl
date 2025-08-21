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
            <button onclick="closeSimpleToast()" 
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

function showSimpleToast(message) {
    if (simpleToastShown) return;
    
    const toast = document.getElementById('simple-notification-toast');
    const messageEl = document.getElementById('simple-toast-message');
    
    if (message) {
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
        const registration = await navigator.serviceWorker.register('/serviceworker.js');
        await navigator.serviceWorker.ready;
        console.log('✅ Service worker registrado');
        
        // Obtener clave VAPID del servidor
        console.log('🔑 Obteniendo clave VAPID...');
        const vapidResponse = await fetch('/push/vapid-public-key');
        const vapidData = await vapidResponse.json();
        const vapidKey = vapidData.public_key;
        console.log('✅ Clave VAPID obtenida');
        
        // Crear suscripción
        console.log('📝 Creando suscripción push...');
        const subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: vapidKey
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
        setTimeout(checkSimpleNotificationStatus, 2000);
    } else {
        console.log('📝 Usuario no autenticado - toast no se mostrará');
    }
});

async function checkSimpleNotificationStatus() {
    if (simpleToastShown) return;
    
    console.log('🔍 Verificando estado de notificaciones...');
    
    // Verificar soporte del navegador
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        console.log('❌ Navegador no compatible con notificaciones push');
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
        showSimpleToast('Mantente al día con las últimas actualizaciones del taller.');
        return;
    }
    
    if (permission === 'granted') {
        try {
            const registration = await navigator.serviceWorker.getRegistration();
            if (registration) {
                const subscription = await registration.pushManager.getSubscription();
                if (!subscription) {
                    console.log('📢 Mostrando toast - tiene permisos pero no suscripción');
                    showSimpleToast('Completa la configuración de notificaciones.');
                }
            } else {
                console.log('📢 Mostrando toast - tiene permisos pero no service worker');
                showSimpleToast('Completa la configuración de notificaciones.');
            }
        } catch (error) {
            console.error('❌ Error verificando suscripción:', error);
        }
    }
}

// Función global para mostrar el toast manualmente
window.showNotificationToast = showSimpleToast;
window.checkNotificationStatus = checkSimpleNotificationStatus;
</script>
