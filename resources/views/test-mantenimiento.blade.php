<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test de Mantenimiento General - Push Notifications</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2196F3 0%, #21CBF3 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .content {
            padding: 40px;
        }

        .section {
            margin-bottom: 40px;
            padding: 30px;
            border-radius: 15px;
            border: 2px solid #f0f0f0;
            transition: all 0.3s ease;
        }

        .section:hover {
            border-color: #2196F3;
            box-shadow: 0 5px 15px rgba(33, 150, 243, 0.1);
        }

        .section h2 {
            color: #333;
            font-size: 1.5rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .status-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #2196F3;
        }

        .status-card h3 {
            color: #333;
            font-size: 1.1rem;
            margin-bottom: 10px;
        }

        .status-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2196F3;
        }

        .button-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 20px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-width: 150px;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #4CAF50, #388E3C);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #FF9800, #F57C00);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #f44336, #d32f2f);
            color: white;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #9E9E9E, #757575);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .result-box {
            margin-top: 20px;
            padding: 20px;
            border-radius: 10px;
            display: none;
        }

        .result-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .result-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .result-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #2196F3;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .test-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .test-option {
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .test-option:hover {
            border-color: #2196F3;
            background: #f5f5f5;
        }

        .test-option.selected {
            border-color: #2196F3;
            background: #e3f2fd;
        }

        .icon {
            font-size: 1.2rem;
            margin-right: 5px;
        }

        .logs {
            background: #1e1e1e;
            color: #00ff00;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            max-height: 300px;
            overflow-y: auto;
            margin-top: 20px;
            display: none;
        }

        .timestamp {
            color: #888;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 Test de Mantenimiento General</h1>
            <p>Prueba manual del sistema de Push Notifications para MantenimientoGeneral</p>
        </div>

        <div class="content">
            <!-- Estado del Sistema -->
            <div class="section">
                <h2><span class="icon">📊</span>Estado del Sistema</h2>
                <div class="status-grid" id="statusGrid">
                    <div class="status-card">
                        <h3>Usuario Carlos Morelli (ID 6)</h3>
                        <div class="status-value" id="usuario6Status">Cargando...</div>
                    </div>
                    <div class="status-card">
                        <h3>Suscripciones Push</h3>
                        <div class="status-value" id="suscripcionesStatus">Cargando...</div>
                    </div>
                    <div class="status-card">
                        <h3>Registros de Mantenimiento</h3>
                        <div class="status-value" id="mantenimientosStatus">Cargando...</div>
                    </div>
                </div>
                <div class="button-group">
                    <button class="btn btn-secondary" onclick="actualizarEstado()">
                        <span class="icon">🔄</span>Actualizar Estado
                    </button>
                </div>
            </div>

            <!-- Preparación del Entorno -->
            <div class="section">
                <h2><span class="icon">⚙️</span>Preparación del Entorno</h2>
                <p style="margin-bottom: 20px;">Antes de ejecutar el test, asegúrate de que el usuario Carlos Morelli (ID 6) existe y tiene suscripciones push activas.</p>
                
                <div class="button-group">
                    <button class="btn btn-success" onclick="crearUsuario6()">
                        <span class="icon">👤</span>Crear Usuario ID 6
                    </button>
                    <button class="btn btn-primary" onclick="crearSuscripcion()">
                        <span class="icon">🔔</span>Crear Suscripción Push
                    </button>
                    <button class="btn btn-danger" onclick="limpiarDatos()">
                        <span class="icon">🗑️</span>Limpiar Datos de Prueba
                    </button>
                </div>
            </div>

            <!-- Ejecutar Test -->
            <div class="section">
                <h2><span class="icon">🚀</span>Ejecutar Test de Mantenimiento</h2>
                <p style="margin-bottom: 20px;">Selecciona el tipo de mantenimiento para crear y ejecutar el test:</p>
                
                <div class="test-options">
                    <div class="test-option selected" data-tipo="completo">
                        <strong>🔧 Mantenimiento Completo</strong>
                        <br><small>Revisión integral (8 horas)</small>
                    </div>
                    <div class="test-option" data-tipo="frenos">
                        <strong>🛑 Sistema de Frenos</strong>
                        <br><small>Pastillas y discos (2 horas)</small>
                    </div>
                    <div class="test-option" data-tipo="motor">
                        <strong>⚙️ Motor</strong>
                        <br><small>Aceite y filtros (3 horas)</small>
                    </div>
                    <div class="test-option" data-tipo="electrico">
                        <strong>⚡ Sistema Eléctrico</strong>
                        <br><small>Diagnóstico completo (4 horas)</small>
                    </div>
                </div>

                <div class="button-group">
                    <button class="btn btn-primary" onclick="ejecutarTest()" id="btnEjecutarTest">
                        <span class="icon">🎯</span>Ejecutar Test de Push Notification
                    </button>
                </div>

                <div class="loading" id="loading">
                    <div class="spinner"></div>
                    <p>Ejecutando test... Por favor espera</p>
                </div>

                <div class="result-box" id="resultBox"></div>
            </div>

            <!-- Logs en Tiempo Real -->
            <div class="section">
                <h2><span class="icon">📝</span>Logs del Sistema</h2>
                <button class="btn btn-secondary" onclick="toggleLogs()">
                    <span class="icon">👁️</span>Mostrar/Ocultar Logs
                </button>
                <div class="logs" id="logsContainer">
                    <div class="timestamp">[Sistema iniciado]</div>
                    Logs en tiempo real aparecerán aquí...
                </div>
            </div>
        </div>
    </div>

    <script>
        // Configuración CSRF para Laravel
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Variables globales
        let tipoSeleccionado = 'completo';
        
        // Configurar headers para todas las peticiones
        const defaultHeaders = {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        };

        // Inicializar página
        document.addEventListener('DOMContentLoaded', function() {
            actualizarEstado();
            configurarEventos();
            addLog('Página de test cargada exitosamente', 'info');
        });

        function configurarEventos() {
            // Configurar selección de tipo de test
            document.querySelectorAll('.test-option').forEach(option => {
                option.addEventListener('click', function() {
                    document.querySelectorAll('.test-option').forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                    tipoSeleccionado = this.dataset.tipo;
                    addLog(`Tipo de test seleccionado: ${tipoSeleccionado}`, 'info');
                });
            });
        }

        async function actualizarEstado() {
            try {
                addLog('Actualizando estado del sistema...', 'info');
                
                const response = await fetch('/test-mantenimiento/estado', {
                    method: 'GET',
                    headers: defaultHeaders
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();
                
                // Actualizar UI con el estado
                document.getElementById('usuario6Status').textContent = 
                    data.usuario_6.existe ? `✅ ${data.usuario_6.name}` : '❌ No existe';
                
                document.getElementById('suscripcionesStatus').textContent = 
                    `${data.suscripciones_push.count} activas`;
                
                document.getElementById('mantenimientosStatus').textContent = 
                    `${data.mantenimientos_recientes.length} recientes`;

                addLog('Estado actualizado correctamente', 'success');

            } catch (error) {
                addLog(`Error actualizando estado: ${error.message}`, 'error');
                showResult('Error al actualizar el estado: ' + error.message, 'error');
            }
        }

        async function crearUsuario6() {
            try {
                addLog('Creando usuario Carlos Morelli (ID 6)...', 'info');
                showLoading(true);

                const response = await fetch('/test-mantenimiento/crear-usuario-6', {
                    method: 'POST',
                    headers: defaultHeaders
                });

                const data = await response.json();
                
                if (data.success) {
                    showResult(`✅ ${data.message}`, 'success');
                    addLog(`Usuario creado: ${data.data.name} (${data.data.email})`, 'success');
                } else {
                    showResult(`⚠️ ${data.error}`, 'warning');
                    addLog(`Advertencia: ${data.error}`, 'warning');
                }

                await actualizarEstado();

            } catch (error) {
                addLog(`Error creando usuario: ${error.message}`, 'error');
                showResult('Error creando usuario: ' + error.message, 'error');
            } finally {
                showLoading(false);
            }
        }

        async function crearSuscripcion() {
            try {
                addLog('Creando suscripción push de prueba...', 'info');
                showLoading(true);

                const response = await fetch('/test-mantenimiento/crear-suscripcion', {
                    method: 'POST',
                    headers: defaultHeaders
                });

                const data = await response.json();
                
                if (data.success) {
                    showResult(`✅ ${data.message}`, 'success');
                    addLog(`Suscripción creada con ID: ${data.data.subscription_id}`, 'success');
                } else {
                    showResult(`❌ ${data.error}`, 'error');
                    addLog(`Error: ${data.error}`, 'error');
                }

                await actualizarEstado();

            } catch (error) {
                addLog(`Error creando suscripción: ${error.message}`, 'error');
                showResult('Error creando suscripción: ' + error.message, 'error');
            } finally {
                showLoading(false);
            }
        }

        async function ejecutarTest() {
            try {
                addLog(`Iniciando test de mantenimiento tipo: ${tipoSeleccionado}...`, 'info');
                showLoading(true);
                
                document.getElementById('btnEjecutarTest').disabled = true;

                const response = await fetch('/test-mantenimiento/crear', {
                    method: 'POST',
                    headers: defaultHeaders,
                    body: JSON.stringify({
                        tipo: tipoSeleccionado
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    let mensaje = `✅ Test ejecutado exitosamente!\n\n`;
                    mensaje += `🔧 Mantenimiento creado: ${data.data.tarea}\n`;
                    mensaje += `📝 Descripción: ${data.data.solicitado}\n`;
                    mensaje += `🆔 ID: ${data.data.mantenimiento_id}\n`;
                    mensaje += `👤 Destinatario: ${data.data.usuario_destino.nombre} (ID ${data.data.usuario_destino.id})\n`;
                    mensaje += `🔔 Suscripciones disponibles: ${data.data.suscripciones_push}\n`;
                    mensaje += `⚡ Observer ejecutado: ${data.data.observer_ejecutado ? 'SÍ' : 'NO'}\n`;
                    
                    if (data.warning) {
                        mensaje += `\n⚠️ Advertencia: ${data.warning}`;
                    }

                    showResult(mensaje, data.warning ? 'warning' : 'success');
                    addLog(`Test completado: Mantenimiento ID ${data.data.mantenimiento_id}`, 'success');
                    
                    if (data.data.suscripciones_push > 0) {
                        addLog('Push notification enviada al usuario ID 6', 'success');
                    } else {
                        addLog('No se enviaron push notifications (sin suscripciones)', 'warning');
                    }

                } else {
                    showResult(`❌ Error: ${data.error}\n\nDetalles: ${data.details}`, 'error');
                    addLog(`Error en test: ${data.error}`, 'error');
                }

                await actualizarEstado();

            } catch (error) {
                addLog(`Error ejecutando test: ${error.message}`, 'error');
                showResult('Error ejecutando test: ' + error.message, 'error');
            } finally {
                showLoading(false);
                document.getElementById('btnEjecutarTest').disabled = false;
            }
        }

        async function limpiarDatos() {
            if (!confirm('¿Estás seguro de que quieres limpiar los datos de prueba?')) {
                return;
            }

            try {
                addLog('Limpiando datos de prueba...', 'info');
                showLoading(true);

                const response = await fetch('/test-mantenimiento/limpiar', {
                    method: 'POST',
                    headers: defaultHeaders
                });

                const data = await response.json();
                
                if (data.success) {
                    showResult(`✅ ${data.message}\n\nSuscripciones eliminadas: ${data.data.suscripciones_eliminadas}\nMantenimientos eliminados: ${data.data.mantenimientos_eliminados}`, 'success');
                    addLog('Datos de prueba limpiados correctamente', 'success');
                } else {
                    showResult(`❌ ${data.error}`, 'error');
                    addLog(`Error limpiando datos: ${data.error}`, 'error');
                }

                await actualizarEstado();

            } catch (error) {
                addLog(`Error limpiando datos: ${error.message}`, 'error');
                showResult('Error limpiando datos: ' + error.message, 'error');
            } finally {
                showLoading(false);
            }
        }

        function showLoading(show) {
            document.getElementById('loading').style.display = show ? 'block' : 'none';
        }

        function showResult(message, type) {
            const resultBox = document.getElementById('resultBox');
            resultBox.className = `result-box result-${type}`;
            resultBox.innerHTML = message.replace(/\n/g, '<br>');
            resultBox.style.display = 'block';
        }

        function toggleLogs() {
            const logsContainer = document.getElementById('logsContainer');
            logsContainer.style.display = logsContainer.style.display === 'none' ? 'block' : 'none';
        }

        function addLog(message, type = 'info') {
            const logsContainer = document.getElementById('logsContainer');
            const timestamp = new Date().toLocaleTimeString();
            const color = {
                'info': '#00aaff',
                'success': '#00ff00',
                'warning': '#ffaa00',
                'error': '#ff4444'
            }[type] || '#ffffff';
            
            const logEntry = document.createElement('div');
            logEntry.innerHTML = `<span class="timestamp">[${timestamp}]</span> <span style="color: ${color}">${message}</span>`;
            logsContainer.appendChild(logEntry);
            logsContainer.scrollTop = logsContainer.scrollHeight;
        }
    </script>
</body>
</html>
