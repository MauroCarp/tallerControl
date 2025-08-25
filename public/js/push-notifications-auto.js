// Auto-inicialización de Push Notifications para TallerControl
// Este script se carga automáticamente en el panel de administración

(function() {
    'use strict';

    // Verificar si ya está inicializado para evitar duplicaciones
    if (window.pushNotificationsInitialized) {
        return;
    }
    window.pushNotificationsInitialized = true;

    console.log('🔔 Inicializando sistema de notificaciones push...');

    // Cargar el PushNotificationManager principal
    function loadPushNotificationManager() {
        return new Promise((resolve, reject) => {
            if (window.pushManager) {
                resolve(window.pushManager);
                return;
            }

            const script = document.createElement('script');
            script.src = '/js/push-notifications.js';
            script.onload = () => {
                // Esperar a que se inicialice
                const checkInit = () => {
                    if (window.pushManager) {
                        resolve(window.pushManager);
                    } else {
                        setTimeout(checkInit, 100);
                    }
                };
                checkInit();
            };
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    // Función para mostrar notificaciones de sistema (toast)
    function showSystemNotification(title, message, type = 'info') {
        // Intentar usar el sistema de notificaciones de Filament si está disponible
        if (window.FilamentNotification) {
            window.FilamentNotification.make()
                .title(title)
                .body(message)
                .send();
            return;
        }

        // Fallback a console log
        console.log(`📢 ${type.toUpperCase()}: ${title} - ${message}`);
    }

    // Inicializar cuando el DOM esté listo
    function initializePushNotifications() {
        loadPushNotificationManager()
            .then(pushManager => {
                console.log('✅ Sistema de notificaciones push cargado correctamente');

                // Verificar si ya está suscrito
                pushManager.isSubscribed()
                    .then(isSubscribed => {
                        if (!isSubscribed) {
                            console.log('ℹ️ Usuario no está suscrito a notificaciones push');
                            
                            // Solo mostrar prompt si el usuario está autenticado y no ha rechazado previamente
                            if (Notification.permission === 'default') {
                                // Esperar un poco antes de preguntar al usuario
                                setTimeout(() => {
                                    showAutoSubscribePrompt(pushManager);
                                }, 3000); // 3 segundos después de cargar
                            }
                        } else {
                            console.log('✅ Usuario ya está suscrito a notificaciones push');
                        }
                    })
                    .catch(error => {
                        console.warn('⚠️ Error verificando estado de suscripción:', error);
                    });
            })
            .catch(error => {
                console.error('❌ Error cargando sistema de notificaciones push:', error);
            });
    }

    // Mostrar prompt discreto para suscribirse
    function showAutoSubscribePrompt(pushManager) {
        // Solo preguntar si el navegador soporta notificaciones
        if (!pushManager.isSupported) {
            console.log('ℹ️ Este navegador no soporta notificaciones push');
            return;
        }

        // Crear un toast/banner discreto
        const banner = document.createElement('div');
        banner.id = 'push-notification-banner';
        banner.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 9999;
            max-width: 350px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            display: none;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
        `;

        banner.innerHTML = `
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="font-size: 24px;">🔔</div>
                <div style="flex: 1;">
                    <div style="font-weight: 600; margin-bottom: 4px;">Notificaciones Push</div>
                    <div style="opacity: 0.9; font-size: 13px;">¿Deseas recibir notificaciones de TallerControl?</div>
                </div>
            </div>
            <div style="margin-top: 12px; display: flex; gap: 8px; justify-content: flex-end;">
                <button id="push-deny-btn" style="background: rgba(255,255,255,0.2); border: none; color: white; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">Ahora no</button>
                <button id="push-allow-btn" style="background: rgba(255,255,255,0.9); border: none; color: #333; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600;">Permitir</button>
            </div>
        `;

        document.body.appendChild(banner);

        // Mostrar el banner con animación
        setTimeout(() => {
            banner.style.display = 'block';
            setTimeout(() => {
                banner.style.opacity = '1';
                banner.style.transform = 'translateX(0)';
            }, 10);
        }, 500);

        // Manejar botones
        document.getElementById('push-allow-btn').addEventListener('click', async () => {
            try {
                await pushManager.subscribe();
                showSystemNotification('¡Perfecto!', 'Te has suscrito correctamente a las notificaciones', 'success');
                hideBanner();
            } catch (error) {
                console.error('Error suscribiéndose:', error);
                showSystemNotification('Error', 'No se pudo activar las notificaciones. Verifica los permisos del navegador.', 'error');
                hideBanner();
            }
        });

        document.getElementById('push-deny-btn').addEventListener('click', () => {
            hideBanner();
            // Guardar preferencia para no volver a preguntar en esta sesión
            sessionStorage.setItem('push-notifications-declined', 'true');
        });

        function hideBanner() {
            banner.style.opacity = '0';
            banner.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (banner.parentNode) {
                    banner.parentNode.removeChild(banner);
                }
            }, 300);
        }

        // Auto-ocultar después de 15 segundos
        setTimeout(() => {
            if (document.getElementById('push-notification-banner')) {
                hideBanner();
            }
        }, 15000);
    }

    // Esperar a que el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializePushNotifications);
    } else {
        // Si ya está cargado, ejecutar inmediatamente
        setTimeout(initializePushNotifications, 100);
    }

    // Exponer algunas funciones útiles globalmente
    window.TallerControlPush = {
        subscribe: () => window.subscribeToPush && window.subscribeToPush(),
        unsubscribe: () => window.unsubscribeFromPush && window.unsubscribeFromPush(),
        sendTest: (title, message, userId) => window.sendTestPush && window.sendTestPush(title, message, userId),
        isSupported: () => window.pushManager && window.pushManager.isSupported,
        isSubscribed: () => window.pushManager && window.pushManager.isSubscribed()
    };

})();
