class PushNotificationManager {
    constructor() {
        this.vapidPublicKey = null;
        this.isSupported = this.checkSupport();
        this.init();
    }

    checkSupport() {
        return 'serviceWorker' in navigator && 'PushManager' in window;
    }

    async init() {
        if (!this.isSupported) {
            console.warn('Push notifications no están soportadas en este navegador');
            return;
        }

        try {
            // Obtener la clave pública VAPID
            await this.getVapidPublicKey();
            
            // Registrar el service worker si no está registrado
            await this.registerServiceWorker();
            
            console.log('PushNotificationManager inicializado correctamente');
            
        } catch (error) {
            console.error('Error inicializando push notifications:', error);
        }
    }

    async getVapidPublicKey() {
        try {
            const response = await fetch('/push/vapid-public-key');
            const data = await response.json();
            this.vapidPublicKey = data.publicKey;
        } catch (error) {
            console.error('Error obteniendo clave VAPID:', error);
            throw error;
        }
    }

    async registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('/serviceworker.js');
                console.log('Service Worker registrado:', registration);
                return registration;
            } catch (error) {
                console.error('Error registrando Service Worker:', error);
                throw error;
            }
        }
    }

    async requestPermission() {
        if (!this.isSupported) {
            throw new Error('Push notifications no están soportadas');
        }

        const permission = await Notification.requestPermission();
        
        if (permission !== 'granted') {
            throw new Error('Permiso para notificaciones denegado');
        }

        return permission;
    }

    async subscribe() {
        try {
            // Solicitar permiso
            await this.requestPermission();

            // Asegurar que el service worker esté registrado
            let registration = await navigator.serviceWorker.getRegistration();
            
            if (!registration) {
                console.log('Service Worker no encontrado, registrando...');
                registration = await this.registerServiceWorker();
            }

            // Esperar a que el service worker esté activo
            if (registration.installing) {
                await new Promise((resolve) => {
                    registration.installing.addEventListener('statechange', (e) => {
                        if (e.target.state === 'activated') {
                            resolve();
                        }
                    });
                });
            }

            if (!registration.active) {
                throw new Error('Service Worker no está activo');
            }

            // Crear la suscripción
            const subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(this.vapidPublicKey)
            });

            // Enviar la suscripción al servidor
            await this.sendSubscriptionToServer(subscription);

            console.log('Suscripción creada exitosamente');
            return subscription;

        } catch (error) {
            console.error('Error al suscribirse:', error);
            throw error;
        }
    }

    async sendSubscriptionToServer(subscription) {
        try {
            const response = await fetch('/push/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(subscription.toJSON())
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error('Subscription response status:', response.status);
                console.error('Subscription response text:', errorText);
                throw new Error(`Error ${response.status}: ${errorText}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Error enviando suscripción:', error);
            throw error;
        }
    }

    async unsubscribe() {
        try {
            const registration = await navigator.serviceWorker.getRegistration();
            
            if (registration) {
                const subscription = await registration.pushManager.getSubscription();
                
                if (subscription) {
                    await subscription.unsubscribe();
                    console.log('Desuscripción exitosa');
                }
            }
        } catch (error) {
            console.error('Error al desuscribirse:', error);
            throw error;
        }
    }

    async isSubscribed() {
        try {
            const registration = await navigator.serviceWorker.getRegistration();
            
            if (registration) {
                const subscription = await registration.pushManager.getSubscription();
                return !!subscription;
            }
            
            return false;
        } catch (error) {
            console.error('Error verificando suscripción:', error);
            return false;
        }
    }

    // Utility function para convertir VAPID key
    urlBase64ToUint8Array(base64String) {
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

    // Método para enviar notificación de prueba
    async sendTestNotification(title, message, userId = null) {
        try {
            const response = await fetch('/push/send-test', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    title: title,
                    message: message,
                    user_id: userId
                })
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error('Response status:', response.status);
                console.error('Response text:', errorText);
                throw new Error(`Error ${response.status}: ${errorText}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Error enviando notificación de prueba:', error);
            throw error;
        }
    }
}

// Crear instancia global
window.pushManager = new PushNotificationManager();

// Funciones de utilidad globales
window.subscribeToPush = () => window.pushManager.subscribe();
window.unsubscribeFromPush = () => window.pushManager.unsubscribe();
window.sendTestPush = (title, message, userId) => window.pushManager.sendTestNotification(title, message, userId);
