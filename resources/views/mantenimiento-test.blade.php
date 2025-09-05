<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Mantenimiento General - Observer</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8fafc;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .test-section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
        }
        
        .test-section h3 {
            color: #2d3748;
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .btn.btn-success {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        }
        
        .btn.btn-danger {
            background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        }
        
        .btn.btn-info {
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        }
        
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            white-space: pre-wrap;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 14px;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .result.success {
            background-color: #f0fff4;
            border: 1px solid #68d391;
            color: #22543d;
        }
        
        .result.error {
            background-color: #fed7d7;
            border: 1px solid #fc8181;
            color: #742a2a;
        }
        
        .result.info {
            background-color: #ebf8ff;
            border: 1px solid #90cdf4;
            color: #2a4365;
        }
        
        .loading {
            display: none;
            color: #4299e1;
            font-weight: 500;
        }
        
        .mantenimientos-list {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        
        .mantenimiento-item {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .mantenimiento-item:last-child {
            border-bottom: none;
        }
        
        .mantenimiento-info {
            flex: 1;
        }
        
        .mantenimiento-actions {
            display: flex;
            gap: 8px;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-pending {
            background-color: #fed7aa;
            color: #9c4221;
        }
        
        .status-completed {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .icon {
            font-size: 20px;
        }
        
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }
        
        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }
        
        .warning-box {
            background-color: #fffbeb;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .warning-box h4 {
            color: #92400e;
            margin: 0 0 10px 0;
        }
        
        .warning-box p {
            color: #92400e;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔧 Test de Mantenimiento General</h1>
        <p>Página de prueba para verificar el funcionamiento del Observer y las Push Notifications</p>
    </div>

    <div class="warning-box">
        <h4>⚠️ Requisitos para el test:</h4>
        <p>• El usuario con ID 6 debe existir en la base de datos</p>
        <p>• El usuario debe tener suscripciones push activas para recibir notificaciones</p>
        <p>• El Observer MantenimientoGeneralObserver debe estar registrado</p>
        <p>• Revisa los logs en storage/logs/laravel.log para ver el detalle del observer</p>
    </div>

    <div class="grid">
        <div class="test-section">
            <h3><span class="icon">🆕</span> Test: Crear Nuevo Mantenimiento</h3>
            <p>Este test crea un nuevo registro de MantenimientoGeneral con solo los datos del primer step. 
               El observer debería detectarlo y enviar una push notification.</p>
            
            <button class="btn" onclick="createTest()">
                🚀 Crear Mantenimiento de Test
            </button>
            
            <div class="loading" id="createLoading">
                ⏳ Creando registro y ejecutando observer...
            </div>
            
            <div id="createResult"></div>
        </div>

        <div class="test-section">
            <h3><span class="icon">✅</span> Test: Completar Mantenimiento</h3>
            <p>Marca un mantenimiento existente como completado (reparado = 1). 
               El observer debería enviar una notificación de completado.</p>
            
            <input type="number" id="completeId" placeholder="ID del mantenimiento" style="padding: 8px; margin-right: 10px; border: 1px solid #cbd5e0; border-radius: 4px;">
            <button class="btn btn-success" onclick="completeTest()">
                ✅ Marcar como Completado
            </button>
            
            <div class="loading" id="completeLoading">
                ⏳ Completando registro...
            </div>
            
            <div id="completeResult"></div>
        </div>
    </div>

    <div class="test-section">
        <h3><span class="icon">📋</span> Registros de Test Recientes</h3>
        <div style="margin-bottom: 15px;">
            <button class="btn btn-info" onclick="loadRecentMantenimientos()">
                🔄 Actualizar Lista
            </button>
            <button class="btn btn-danger" onclick="cleanTestData()">
                🗑️ Limpiar Datos de Test
            </button>
        </div>
        
        <div class="loading" id="listLoading">
            ⏳ Cargando registros...
        </div>
        
        <div id="mantenimientosList" class="mantenimientos-list">
            <!-- Lista se carga dinámicamente -->
        </div>
    </div>

    <div class="test-section">
        <h3><span class="icon">📊</span> Instrucciones de Verificación</h3>
        <div class="result info">
1. Ejecuta los tests usando los botones de arriba
2. Revisa los logs del sistema: tail -f storage/logs/laravel.log
3. Verifica que las notificaciones push se envíen al usuario ID 6
4. Comprueba que los logs muestren información del observer

Logs a buscar:
- "Notificación automática enviada" (al crear)
- "Notificación de completado enviada" (al completar)
- "Error enviando notificación" (si hay errores)

Para testing completo, también puedes ejecutar:
php artisan test tests/Feature/MantenimientoGeneralObserverTest.php
        </div>
    </div>

    <script>
        // Configurar token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        // Cargar lista inicial
        document.addEventListener('DOMContentLoaded', function() {
            loadRecentMantenimientos();
        });

        async function createTest() {
            const btn = document.querySelector('button[onclick="createTest()"]');
            const loading = document.getElementById('createLoading');
            const result = document.getElementById('createResult');
            
            btn.disabled = true;
            loading.style.display = 'block';
            result.innerHTML = '';
            
            try {
                const response = await fetch('/mantenimiento-test/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    result.className = 'result success';
                    result.innerHTML = `✅ Test ejecutado exitosamente!

📋 Mantenimiento creado:
   ID: ${data.mantenimiento.id}
   Tarea: ${data.mantenimiento.tarea}
   Solicitado: ${data.mantenimiento.solicitado}
   Estado: ${data.mantenimiento.reparado ? 'Completado' : 'Pendiente'}

👤 Usuario objetivo:
   ID: ${data.user_info.id}
   Nombre: ${data.user_info.name}
   Email: ${data.user_info.email}
   Suscripciones Push: ${data.user_info.push_subscriptions}

🔔 Observer: ${data.observer_info}`;
                    
                    // Actualizar lista automáticamente
                    setTimeout(loadRecentMantenimientos, 1000);
                } else {
                    result.className = 'result error';
                    result.innerHTML = `❌ Error: ${data.message}`;
                }
                
            } catch (error) {
                result.className = 'result error';
                result.innerHTML = `❌ Error de conexión: ${error.message}`;
            } finally {
                btn.disabled = false;
                loading.style.display = 'none';
            }
        }

        async function completeTest() {
            const btn = document.querySelector('button[onclick="completeTest()"]');
            const loading = document.getElementById('completeLoading');
            const result = document.getElementById('completeResult');
            const idInput = document.getElementById('completeId');
            
            const id = idInput.value.trim();
            if (!id) {
                result.className = 'result error';
                result.innerHTML = '❌ Por favor ingresa el ID del mantenimiento';
                return;
            }
            
            btn.disabled = true;
            loading.style.display = 'block';
            result.innerHTML = '';
            
            try {
                const response = await fetch('/mantenimiento-test/complete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ id: parseInt(id) })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    result.className = 'result success';
                    result.innerHTML = `✅ Mantenimiento completado exitosamente!

📋 Registro actualizado:
   ID: ${data.mantenimiento.id}
   Tarea: ${data.mantenimiento.tarea}
   Estado: ${data.mantenimiento.reparado ? 'Completado ✅' : 'Pendiente ⏳'}
   Realizado por: ${data.mantenimiento.realizado}
   Fecha realizado: ${data.mantenimiento.fecha_realizado}
   Horas: ${data.mantenimiento.horas}
   Costo: $${data.mantenimiento.costo}

🔔 Observer: ${data.observer_info}`;
                    
                    idInput.value = '';
                    setTimeout(loadRecentMantenimientos, 1000);
                } else {
                    result.className = 'result error';
                    result.innerHTML = `❌ Error: ${data.message}`;
                }
                
            } catch (error) {
                result.className = 'result error';
                result.innerHTML = `❌ Error de conexión: ${error.message}`;
            } finally {
                btn.disabled = false;
                loading.style.display = 'none';
            }
        }

        async function loadRecentMantenimientos() {
            const loading = document.getElementById('listLoading');
            const list = document.getElementById('mantenimientosList');
            
            loading.style.display = 'block';
            
            try {
                const response = await fetch('/mantenimiento-test/recent');
                const data = await response.json();
                
                if (data.success) {
                    if (data.mantenimientos.length === 0) {
                        list.innerHTML = '<div style="padding: 20px; text-align: center; color: #718096;">No hay registros de test</div>';
                    } else {
                        list.innerHTML = data.mantenimientos.map(m => `
                            <div class="mantenimiento-item">
                                <div class="mantenimiento-info">
                                    <strong>ID ${m.id}:</strong> ${m.tarea}<br>
                                    <small>Solicitado: ${m.solicitado} | Fecha: ${m.fechaSolicitud}</small>
                                </div>
                                <div class="mantenimiento-actions">
                                    <span class="status-badge ${m.reparado ? 'status-completed' : 'status-pending'}">
                                        ${m.reparado ? 'Completado' : 'Pendiente'}
                                    </span>
                                    ${!m.reparado ? `<button class="btn btn-success" style="padding: 4px 8px; font-size: 12px;" onclick="document.getElementById('completeId').value=${m.id}">Completar</button>` : ''}
                                </div>
                            </div>
                        `).join('');
                    }
                } else {
                    list.innerHTML = `<div style="padding: 20px; color: #e53e3e;">Error: ${data.message}</div>`;
                }
                
            } catch (error) {
                list.innerHTML = `<div style="padding: 20px; color: #e53e3e;">Error de conexión: ${error.message}</div>`;
            } finally {
                loading.style.display = 'none';
            }
        }

        async function cleanTestData() {
            if (!confirm('¿Estás seguro de que quieres eliminar todos los registros de test?')) {
                return;
            }
            
            try {
                const response = await fetch('/mantenimiento-test/clean', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    loadRecentMantenimientos();
                } else {
                    alert('Error: ' + data.message);
                }
                
            } catch (error) {
                alert('Error de conexión: ' + error.message);
            }
        }
    </script>
</body>
</html>
