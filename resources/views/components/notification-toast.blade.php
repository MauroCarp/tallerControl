<!-- Toast de Notificaciones Push -->
<div id="notification-toast" class="toast-container" style="display: none;">
    <div class="toast notification-toast">
        <div class="toast-header">
            <div class="toast-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <strong class="toast-title">Notificaciones Push</strong>
            <button type="button" class="toast-close" onclick="closeNotificationToast()">
                <span>&times;</span>
            </button>
        </div>
        <div class="toast-body">
            <p id="toast-message">Mantente al día con las últimas actualizaciones del taller.</p>
            <div class="toast-actions">
                <button id="enable-notifications-btn" class="btn btn-primary btn-sm" onclick="enableNotifications()">
                    <span class="btn-text">Activar Notificaciones</span>
                    <span class="btn-loading" style="display: none;">
                        <svg class="spinner" width="16" height="16" viewBox="0 0 24 24">
                            <circle class="path" cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-dasharray="32" stroke-dashoffset="32">
                                <animate attributeName="stroke-dasharray" dur="2s" values="0 32;16 16;0 32;0 32" repeatCount="indefinite"/>
                                <animate attributeName="stroke-dashoffset" dur="2s" values="0;-16;-32;-32" repeatCount="indefinite"/>
                            </circle>
                        </svg>
                    </span>
                </button>
                <button class="btn btn-outline btn-sm" onclick="closeNotificationToast()">
                    Más tarde
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    max-width: 350px;
}

.notification-toast {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    overflow: hidden;
    animation: slideInRight 0.4s ease-out;
    border: none;
}

.toast-header {
    display: flex;
    align-items: center;
    padding: 16px 20px 12px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.toast-icon {
    margin-right: 12px;
    opacity: 0.9;
}

.toast-title {
    flex: 1;
    font-size: 16px;
    font-weight: 600;
    margin: 0;
}

.toast-close {
    background: none;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background-color 0.2s;
}

.toast-close:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.toast-body {
    padding: 16px 20px 20px;
}

.toast-body p {
    margin: 0 0 16px;
    font-size: 14px;
    line-height: 1.5;
    opacity: 0.9;
}

.toast-actions {
    display: flex;
    gap: 10px;
}

.btn {
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-primary {
    background-color: rgba(255, 255, 255, 0.2);
    color: white;
    backdrop-filter: blur(10px);
}

.btn-primary:hover {
    background-color: rgba(255, 255, 255, 0.3);
    transform: translateY(-1px);
}

.btn-success {
    background-color: rgba(72, 187, 120, 0.9);
    color: white;
}

.btn-success:hover {
    background-color: rgba(72, 187, 120, 1);
    transform: translateY(-1px);
}

.btn-outline {
    background-color: transparent;
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.btn-outline:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
}

.spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.toast-container.hiding .notification-toast {
    animation: slideOutRight 0.4s ease-out;
}

/* Responsive */
@media (max-width: 768px) {
    .toast-container {
        top: 10px;
        right: 10px;
        left: 10px;
        max-width: none;
    }
    
    .toast-actions {
        flex-direction: column;
    }
    
    .btn {
        justify-content: center;
    }
}
</style>

<script>
let notificationToastShown = false;

// Función para mostrar el toast
function showNotificationToast(message = null) {
    if (notificationToastShown) return;
    
    const toast = document.getElementById('notification-toast');
    const messageEl = document.getElementById('toast-message');
    
    if (message) {
        messageEl.textContent = message;
    }
    
    toast.style.display = 'block';
    notificationToastShown = true;
    
    // Auto-hide después de 10 segundos si no hay interacción
    setTimeout(() => {
        if (document.getElementById('notification-toast').style.display !== 'none') {
            closeNotificationToast();
        }
    }, 10000);
}

// Función para cerrar el toast
function closeNotificationToast() {
    const container = document.getElementById('notification-toast');
    container.classList.add('hiding');
    
    setTimeout(() => {
        container.style.display = 'none';
        container.classList.remove('hiding');
    }, 400);
}

// Función para activar notificaciones
async function enableNotifications() {
    const btn = document.getElementById('enable-notifications-btn');
    const btnText = btn.querySelector('.btn-text');
    const btnLoading = btn.querySelector('.btn-loading');
    
    // Mostrar loading
    btn.disabled = true;
    btnText.style.display = 'none';
    btnLoading.style.display = 'inline-flex';
    
    try {
        // Verificar soporte del navegador
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            throw new Error('Tu navegador no soporta notificaciones push');
        }
        
        // Pedir permiso para notificaciones
        const permission = await Notification.requestPermission();
        
        if (permission !== 'granted') {
            throw new Error('Necesitas permitir las notificaciones para continuar');
        }
        
        // Registrar service worker
        const registration = await navigator.serviceWorker.register('/serviceworker.js');
        await navigator.serviceWorker.ready;
        
        // Obtener suscripción
        const subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: '{{ config("app.vapid_public_key") }}'
        });
        
        // Enviar suscripción al servidor
        const response = await fetch('/push-subscriptions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(subscription.toJSON())
        });
        
        if (!response.ok) {
            throw new Error('Error al guardar la suscripción');
        }
        
        // Mostrar éxito
        btnText.textContent = '¡Activado!';
        btnText.style.display = 'inline';
        btnLoading.style.display = 'none';
        btn.disabled = false;
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-success');
        
        // Cerrar toast después de un momento
        setTimeout(() => {
            closeNotificationToast();
        }, 2000);
        
        console.log('✅ Notificaciones push activadas correctamente');
        
    } catch (error) {
        console.error('❌ Error al activar notificaciones:', error);
        
        // Mostrar error
        btnText.textContent = 'Error - Reintentar';
        btnText.style.display = 'inline';
        btnLoading.style.display = 'none';
        btn.disabled = false;
        
        // Mostrar mensaje de error específico
        const messageEl = document.getElementById('toast-message');
        messageEl.textContent = error.message || 'Error al activar las notificaciones. Inténtalo de nuevo.';
        messageEl.style.color = '#ffeb3b';
    }
}

// Verificar estado de notificaciones cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    checkNotificationStatus();
});

async function checkNotificationStatus() {
    // No mostrar el toast si ya se mostró en esta sesión
    if (notificationToastShown) return;
    
    // Verificar si el navegador soporta notificaciones
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        return; // No mostrar toast en navegadores no compatibles
    }
    
    // Verificar permisos
    const permission = Notification.permission;
    
    if (permission === 'denied') {
        return; // No molestar si el usuario rechazó explícitamente
    }
    
    if (permission === 'default') {
        // Usuario no ha decidido aún - mostrar toast
        setTimeout(() => {
            showNotificationToast('Mantente al día con las últimas actualizaciones del taller.');
        }, 3000); // Mostrar después de 3 segundos
        return;
    }
    
    if (permission === 'granted') {
        // Verificar si ya tiene una suscripción activa
        try {
            const registration = await navigator.serviceWorker.getRegistration();
            if (registration) {
                const subscription = await registration.pushManager.getSubscription();
                if (!subscription) {
                    // Tiene permisos pero no suscripción - mostrar toast
                    setTimeout(() => {
                        showNotificationToast('Completa la configuración de notificaciones para recibir actualizaciones.');
                    }, 3000);
                }
            } else {
                // Tiene permisos pero no service worker - mostrar toast
                setTimeout(() => {
                    showNotificationToast('Completa la configuración de notificaciones para recibir actualizaciones.');
                }, 3000);
            }
        } catch (error) {
            console.error('Error verificando suscripción:', error);
        }
    }
}
</script>
