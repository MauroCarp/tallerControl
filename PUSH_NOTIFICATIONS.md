# Push Notifications con Web-Push PHP

## Descripción

Sistema completo de push notifications implementado en Laravel usando la librería `minishlink/web-push` de PHP. Permite enviar notificaciones push a usuarios suscritos a través del navegador.

## Características

- ✅ Suscripción/desuscripción automática de usuarios
- ✅ Envío de notificaciones a usuarios específicos o a todos
- ✅ Gestión automática de suscripciones expiradas
- ✅ Interfaz web de prueba
- ✅ Comando de consola para envío masivo
- ✅ Service Worker con manejo de notificaciones
- ✅ Almacenamiento seguro de suscripciones en base de datos

## Instalación y Configuración

### 1. Dependencias Instaladas

```bash
composer require minishlink/web-push
```

### 2. Variables de Entorno

Configurar en `.env`:

```env
VAPID_PUBLIC_KEY="BL84_CHtLTrbLGAVMQIgPXrRdepd-b34Mesjm6jAdSX7lg3bN6Lh4f6TUcX5VX8r3443IgAzOBbpvm3W35XVCZc"
VAPID_PRIVATE_KEY="oawYatke3AIwxhvgMno104dXaKmAp-DYY3CGRYChxWg"
VAPID_SUBJECT="mailto:admin@tallercontrol.com"
```

**IMPORTANTE**: Estas claves son específicas para este proyecto. Para producción, genera nuevas claves usando:
```bash
npx web-push generate-vapid-keys
```

### 3. Base de Datos

Ejecutar migración:

```bash
php artisan migrate
```

## Archivos Creados/Modificados

### Modelos
- `app/Models/PushSubscription.php` - Modelo para suscripciones

### Servicios
- `app/Services/PushNotificationService.php` - Servicio principal para push notifications

### Controladores
- `app/Http/Controllers/PushNotificationController.php` - API endpoints

### Comandos
- `app/Console/Commands/SendPushNotification.php` - Comando de consola

### Frontend
- `public/js/push-notifications.js` - Cliente JavaScript
- `public/serviceworker.js` - Service Worker actualizado
- `resources/views/push-test.blade.php` - Página de prueba

### Configuración
- `config/app.php` - Configuración VAPID
- `database/migrations/2025_08_21_114545_create_push_subscriptions_table.php` - Migración

## Uso

### Página de Prueba

Visitar: `http://localhost:8000/tallerControl/public/push-test`

### API Endpoints

#### Obtener clave pública VAPID
```
GET /push/vapid-public-key
```

#### Suscribirse a notificaciones
```
POST /push/subscribe
Content-Type: application/json

{
    "endpoint": "https://...",
    "keys": {
        "p256dh": "...",
        "auth": "..."
    }
}
```

#### Enviar notificación de prueba
```
POST /push/send-test
Content-Type: application/json

{
    "title": "Título",
    "message": "Mensaje",
    "user_id": 1 // opcional
}
```

### Comando de Consola

```bash
# Enviar a todos los usuarios
php artisan push:send "Título" "Mensaje"

# Enviar a usuario específico
php artisan push:send "Título" "Mensaje" --user=1

# Con icono personalizado
php artisan push:send "Título" "Mensaje" --icon="/custom-icon.png"
```

### Uso Programático

```php
use App\Services\PushNotificationService;

$pushService = new PushNotificationService();

// Enviar a usuario específico
$payload = [
    'title' => 'Título',
    'body' => 'Mensaje',
    'icon' => '/icon.png',
    'data' => ['url' => '/some-page']
];

$pushService->sendToUser(1, $payload);

// Enviar a todos
$pushService->sendToAll($payload);
```

### Uso en JavaScript

```javascript
// Suscribirse
await window.subscribeToPush();

// Desuscribirse
await window.unsubscribeFromPush();

// Enviar notificación de prueba
await window.sendTestPush('Título', 'Mensaje', userId);

// Verificar si está suscrito
const isSubscribed = await window.pushManager.isSubscribed();
```

## Estructura de Notificación

```javascript
{
    title: "Título de la notificación",
    body: "Contenido del mensaje",
    icon: "/images/icons/icon-192x192.png",
    badge: "/images/icons/icon-72x72.png",
    tag: "unique-tag",
    data: {
        url: "/target-page",
        timestamp: "2025-08-21T11:45:00.000Z",
        custom_data: "valor"
    }
}
```

## Funcionalidades del Service Worker

- **Manejo de eventos push**: Recibe y muestra notificaciones
- **Click handler**: Abre la URL especificada al hacer click
- **Acciones**: Botones "Abrir" y "Cerrar" en notificaciones
- **Gestión de ventanas**: Enfoca ventana existente o abre nueva

## Consideraciones de Seguridad

1. **Claves VAPID**: Generar claves únicas para producción
2. **CSRF Protection**: Incluido en todas las peticiones
3. **Validación**: Validación de datos en servidor
4. **Cleanup**: Eliminación automática de suscripciones expiradas

## Troubleshooting

### La notificación no se muestra
- Verificar permisos del navegador
- Comprobar que el service worker está registrado
- Revisar la consola del navegador

### Error de claves VAPID
- Verificar que las claves están configuradas en `.env`
- Asegurarse de que las claves son válidas

### Suscripción falla
- Verificar conexión HTTPS (requerida para push notifications)
- Comprobar que el service worker está activo

## Navegadores Soportados

- ✅ Chrome 50+
- ✅ Firefox 44+
- ✅ Safari 16+
- ✅ Edge 17+
- ❌ Internet Explorer (no soportado)

## Próximas Mejoras

- [ ] Notificaciones programadas
- [ ] Segmentación de usuarios
- [ ] Analytics de notificaciones
- [ ] Templates de notificaciones
- [ ] Integración con Filament
