# Instrucciones para el Modo Mantenimiento

## Archivos creados:

1. **maintenance.php** - Página de mantenimiento con diseño profesional
2. **.htaccess.maintenance** - Configuración de Apache para redirigir el tráfico

## Cómo activar el modo mantenimiento:

### Opción 1: Usando .htaccess (Recomendado)
```bash
# Renombrar el archivo actual .htaccess (si existe)
mv .htaccess .htaccess.backup

# Activar el modo mantenimiento
mv .htaccess.maintenance .htaccess
```

### Opción 2: Reemplazar index.php temporalmente
```bash
# Hacer backup del index.php actual
mv public/index.php public/index.php.backup

# Copiar la página de mantenimiento
cp maintenance.php public/index.php
```

## Cómo desactivar el modo mantenimiento:

### Si usaste la Opción 1:
```bash
# Restaurar .htaccess original
mv .htaccess .htaccess.maintenance
mv .htaccess.backup .htaccess
```

### Si usaste la Opción 2:
```bash
# Restaurar index.php original
mv public/index.php.backup public/index.php
```

## Personalización:

### Cambiar el tiempo estimado:
Edita la línea en `maintenance.php`:
```html
<div class="estimated-time">
    ⏱️ Tiempo estimado: TU_TIEMPO_AQUÍ
</div>
```

### Cambiar información de contacto:
Edita la sección `contact-details` en `maintenance.php`:
```html
📧 Email: tu-email@dominio.com<br>
📞 Teléfono: tu-telefono<br>
💬 WhatsApp: tu-whatsapp
```

### Cambiar enlaces sociales:
Edita la sección `social-links` y agrega tus URLs reales.

## Características incluidas:

- ✅ Diseño responsivo y profesional
- ✅ Animaciones CSS atractivas
- ✅ Auto-refresh cada 5 minutos
- ✅ Código de estado HTTP 503 apropiado
- ✅ Headers de cache control
- ✅ Timestamp de última actualización
- ✅ Información de contacto
- ✅ Enlaces a redes sociales
- ✅ Compatible con dispositivos móviles

## Notas importantes:

1. El archivo `maintenance.php` es completamente independiente del sistema Laravel
2. No requiere ninguna dependencia del framework
3. El código HTTP 503 informa a los motores de búsqueda que es temporal
4. El header `Retry-After` sugiere cuándo volver a intentar (1 hora por defecto)
