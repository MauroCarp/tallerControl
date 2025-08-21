// Service Worker optimizado para Chrome
console.log('Service Worker: Script cargado');

// Instalar Service Worker
self.addEventListener("install", event => {
    console.log('Service Worker: Instalando...');
    self.skipWaiting();
    console.log('Service Worker: Instalación completada');
});

// Activar Service Worker
self.addEventListener('activate', event => {
    console.log('Service Worker: Activando...');
    event.waitUntil(
        self.clients.claim().then(() => {
            console.log('Service Worker: Activación completada, controlando clientes');
        })
    );
});

// Manejar requests de fetch de forma simple
self.addEventListener("fetch", event => {
    // Solo interceptar requests si es necesario para Chrome
    event.respondWith(
        fetch(event.request).catch(() => {
            // Fallback simple en caso de error
            return new Response('Offline', {
                status: 200,
                statusText: 'OK'
            });
        })
    );
});

// Push Notifications - Optimizado para Chrome
self.addEventListener('push', event => {
    console.log('Service Worker: Notificación push recibida', event);
    
    if (!event.data) {
        console.log('Service Worker: No hay datos en la notificación push');
        return;
    }

    try {
        const data = event.data.json();
        console.log('Service Worker: Datos de notificación:', data);
        
        const options = {
            body: data.body || 'Sin mensaje',
            icon: data.icon || '/images/icons/icon-192x192.png',
            badge: data.badge || '/images/icons/icon-72x72.png',
            tag: data.tag || 'default-' + Date.now(),
            data: data.data || {},
            requireInteraction: false, // Chrome específico: no requiere interacción
            silent: false, // Chrome específico: no silenciar
            actions: [
                {
                    action: 'open',
                    title: 'Abrir',
                    icon: '/images/icons/icon-72x72.png'
                },
                {
                    action: 'close',
                    title: 'Cerrar',
                    icon: '/images/icons/icon-72x72.png'
                }
            ]
        };

        console.log('Service Worker: Mostrando notificación con opciones:', options);

        event.waitUntil(
            self.registration.showNotification(data.title || 'Notificación', options)
                .then(() => {
                    console.log('Service Worker: Notificación mostrada exitosamente');
                })
                .catch(error => {
                    console.error('Service Worker: Error mostrando notificación:', error);
                })
        );
    } catch (error) {
        console.error('Service Worker: Error procesando notificación push:', error);
        
        // Fallback: mostrar notificación simple
        event.waitUntil(
            self.registration.showNotification('Nueva notificación', {
                body: 'Se recibió una nueva notificación',
                icon: '/images/icons/icon-192x192.png',
                tag: 'fallback-' + Date.now()
            })
        );
    }
});

// Notification click handler
self.addEventListener('notificationclick', event => {
    event.notification.close();

    if (event.action === 'open' || !event.action) {
        const urlToOpen = event.notification.data.url || '/';
        
        event.waitUntil(
            clients.matchAll({
                type: 'window',
                includeUncontrolled: true
            }).then(clientList => {
                // Si ya hay una ventana abierta, enfocarla
                for (let i = 0; i < clientList.length; i++) {
                    const client = clientList[i];
                    if (client.url === urlToOpen && 'focus' in client) {
                        return client.focus();
                    }
                }
                
                // Si no hay ventana abierta, abrir una nueva
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
        );
    }
});