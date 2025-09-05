<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Suscripciones Push</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: #1a202c;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            border-left: 4px solid #667eea;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #64748b;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .main-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .card-header {
            background: #f7fafc;
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2d3748;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #374151;
        }
        
        .form-select, .form-input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        
        .form-select:focus, .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a67d8;
            transform: translateY(-1px);
        }
        
        .btn-danger {
            background: #e53e3e;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c53030;
            transform: translateY(-1px);
        }
        
        .btn-warning {
            background: #d69e2e;
            color: white;
        }
        
        .btn-warning:hover {
            background: #b7791f;
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .user-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .user-item {
            display: flex;
            justify-content: between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
            transition: background-color 0.2s;
        }
        
        .user-item:hover {
            background-color: #f9fafb;
        }
        
        .user-info {
            flex: 1;
        }
        
        .user-name {
            font-weight: 600;
            color: #1f2937;
        }
        
        .user-email {
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        .user-stats {
            text-align: right;
            margin-right: 15px;
        }
        
        .subscription-count {
            background: #667eea;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .subscription-details {
            background: #f8fafc;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            display: none;
        }
        
        .subscription-item {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 10px;
        }
        
        .subscription-endpoint {
            font-family: monospace;
            font-size: 0.8rem;
            color: #4b5563;
            word-break: break-all;
        }
        
        .subscription-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
        }
        
        .subscription-date {
            font-size: 0.8rem;
            color: #6b7280;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid transparent;
        }
        
        .alert-success {
            background-color: #d1fae5;
            border-color: #a7f3d0;
            color: #065f46;
        }
        
        .alert-error {
            background-color: #fee2e2;
            border-color: #fecaca;
            color: #991b1b;
        }
        
        .alert-warning {
            background-color: #fef3c7;
            border-color: #fde68a;
            color: #92400e;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
            color: #6b7280;
        }
        
        .full-width {
            grid-column: 1 / -1;
        }
        
        .danger-zone {
            border: 2px solid #e53e3e;
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .danger-zone h3 {
            color: #e53e3e;
            margin-bottom: 15px;
        }
        
        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .user-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .user-stats {
                margin-top: 10px;
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔔 Gestión de Suscripciones Push</h1>
            <p>Administra las suscripciones de notificaciones push de todos los usuarios</p>
        </div>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number" id="total-subscriptions">{{ $totalSubscriptions }}</div>
                <div class="stat-label">Total Suscripciones</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="users-with-subs">{{ $usersWithSubscriptions }}</div>
                <div class="stat-label">Usuarios con Suscripciones</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="total-users">{{ $allUsers->count() }}</div>
                <div class="stat-label">Total Usuarios</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="subs-last-30">-</div>
                <div class="stat-label">Últimos 30 días</div>
            </div>
        </div>

        <!-- Alertas -->
        <div id="alert-container"></div>

        <!-- Contenido principal -->
        <div class="main-content">
            <!-- Selector de usuario -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Seleccionar Usuario</h2>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Usuario:</label>
                        <select id="user-select" class="form-select">
                            <option value="">Seleccionar usuario...</option>
                            @foreach($allUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <button id="load-user-btn" class="btn btn-primary" onclick="loadUserSubscriptions()">
                            📋 Cargar Suscripciones
                        </button>
                        <button id="delete-all-user-btn" class="btn btn-danger" onclick="deleteAllUserSubscriptions()" disabled>
                            🗑️ Eliminar Todas
                        </button>
                    </div>
                </div>
            </div>

            <!-- Lista de usuarios con suscripciones -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Usuarios con Suscripciones Activas</h2>
                </div>
                <div class="card-body">
                    <div class="user-list">
                        @foreach($users as $user)
                            <div class="user-item">
                                <div class="user-info">
                                    <div class="user-name">{{ $user['name'] }}</div>
                                    <div class="user-email">{{ $user['email'] }}</div>
                                </div>
                                <div class="user-stats">
                                    <div class="subscription-count">{{ $user['subscriptions_count'] }} subs</div>
                                    @if($user['latest_subscription'])
                                        <div style="font-size: 0.8rem; color: #6b7280; margin-top: 4px;">
                                            {{ $user['latest_subscription'] }}
                                        </div>
                                    @endif
                                </div>
                                <button class="btn btn-primary" onclick="selectUser({{ $user['id'] }})">
                                    👁️ Ver
                                </button>
                            </div>
                        @endforeach
                        
                        @if($users->isEmpty())
                            <div class="loading">
                                No hay usuarios con suscripciones activas
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalles de suscripciones -->
        <div class="card full-width">
            <div class="card-header">
                <h2 class="card-title">Detalles de Suscripciones</h2>
            </div>
            <div class="card-body">
                <div id="subscription-details" class="subscription-details">
                    <p class="loading">Selecciona un usuario para ver sus suscripciones</p>
                </div>
            </div>
        </div>

        <!-- Zona de peligro -->
        <div class="danger-zone">
            <h3>⚠️ Zona de Peligro</h3>
            <p style="margin-bottom: 15px; color: #4b5563;">
                Las siguientes acciones son irreversibles y afectarán a todos los usuarios.
            </p>
            <button class="btn btn-warning" onclick="refreshStats()">
                🔄 Actualizar Estadísticas
            </button>
            <button class="btn btn-danger" onclick="confirmDeleteAll()" style="margin-left: 10px;">
                💥 Eliminar TODAS las Suscripciones
            </button>
        </div>
    </div>

    <script>
        let currentUserId = null;
        let currentUserData = null;

        // Cargar estadísticas al inicio
        document.addEventListener('DOMContentLoaded', function() {
            refreshStats();
        });

        function showAlert(message, type = 'success') {
            const container = document.getElementById('alert-container');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.textContent = message;
            
            container.appendChild(alert);
            
            setTimeout(() => {
                alert.remove();
            }, 5000);
        }

        function selectUser(userId) {
            document.getElementById('user-select').value = userId;
            loadUserSubscriptions();
        }

        async function loadUserSubscriptions() {
            const userId = document.getElementById('user-select').value;
            if (!userId) {
                showAlert('Por favor selecciona un usuario', 'error');
                return;
            }

            currentUserId = userId;
            const detailsContainer = document.getElementById('subscription-details');
            detailsContainer.style.display = 'block';
            detailsContainer.innerHTML = '<div class="loading">Cargando suscripciones...</div>';

            document.getElementById('delete-all-user-btn').disabled = false;

            try {
                const response = await fetch(`/push-manager/user-subscriptions?user_id=${userId}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) {
                    throw new Error('Error al cargar suscripciones');
                }

                const data = await response.json();
                currentUserData = data;
                displayUserSubscriptions(data);

            } catch (error) {
                console.error('Error:', error);
                showAlert('Error al cargar las suscripciones: ' + error.message, 'error');
                detailsContainer.innerHTML = '<div class="loading">Error al cargar suscripciones</div>';
            }
        }

        function displayUserSubscriptions(data) {
            const container = document.getElementById('subscription-details');
            
            if (data.subscriptions.length === 0) {
                container.innerHTML = `
                    <div class="loading">
                        El usuario <strong>${data.user.name}</strong> no tiene suscripciones activas
                    </div>
                `;
                return;
            }

            let html = `
                <div style="margin-bottom: 20px;">
                    <h3>👤 ${data.user.name} (${data.user.email})</h3>
                    <p>Total de suscripciones: <strong>${data.total}</strong></p>
                </div>
            `;

            data.subscriptions.forEach(sub => {
                html += `
                    <div class="subscription-item">
                        <div class="subscription-endpoint">
                            <strong>Endpoint:</strong> ${sub.endpoint_preview}
                        </div>
                        <div class="subscription-meta">
                            <div class="subscription-date">
                                Creada: ${sub.created_at} | Actualizada: ${sub.updated_at}
                            </div>
                            <button class="btn btn-danger" onclick="deleteSubscription(${sub.id})" style="padding: 6px 12px; font-size: 0.8rem;">
                                🗑️ Eliminar
                            </button>
                        </div>
                        <div style="margin-top: 8px; font-size: 0.8rem; color: #6b7280;">
                            <strong>Hash:</strong> ${sub.endpoint_hash}
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        async function deleteAllUserSubscriptions() {
            if (!currentUserId) {
                showAlert('No hay usuario seleccionado', 'error');
                return;
            }

            if (!confirm(`¿Estás seguro de que quieres eliminar TODAS las suscripciones del usuario ${currentUserData?.user?.name}?`)) {
                return;
            }

            try {
                const response = await fetch('/push-manager/delete-user-subscriptions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        user_id: currentUserId
                    })
                });

                if (!response.ok) {
                    throw new Error('Error al eliminar suscripciones');
                }

                const data = await response.json();
                showAlert(data.message, 'success');
                
                // Recargar datos
                await loadUserSubscriptions();
                await refreshStats();
                location.reload(); // Recargar para actualizar la lista de usuarios

            } catch (error) {
                console.error('Error:', error);
                showAlert('Error al eliminar suscripciones: ' + error.message, 'error');
            }
        }

        async function deleteSubscription(subscriptionId) {
            if (!confirm('¿Estás seguro de que quieres eliminar esta suscripción?')) {
                return;
            }

            try {
                const response = await fetch('/push-manager/delete-subscription', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        subscription_id: subscriptionId
                    })
                });

                if (!response.ok) {
                    throw new Error('Error al eliminar suscripción');
                }

                const data = await response.json();
                showAlert(data.message, 'success');
                
                // Recargar datos
                await loadUserSubscriptions();
                await refreshStats();

            } catch (error) {
                console.error('Error:', error);
                showAlert('Error al eliminar suscripción: ' + error.message, 'error');
            }
        }

        async function refreshStats() {
            try {
                const response = await fetch('/push-manager/stats', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) {
                    throw new Error('Error al cargar estadísticas');
                }

                const data = await response.json();
                
                document.getElementById('total-subscriptions').textContent = data.total_subscriptions;
                document.getElementById('users-with-subs').textContent = data.users_with_subscriptions;
                document.getElementById('total-users').textContent = data.total_users;
                document.getElementById('subs-last-30').textContent = data.subscriptions_last_30_days;

            } catch (error) {
                console.error('Error al cargar estadísticas:', error);
            }
        }

        function confirmDeleteAll() {
            const confirmation = prompt(
                'Esta acción eliminará TODAS las suscripciones de TODOS los usuarios.\n' +
                'Para confirmar, escribe: ELIMINAR TODO'
            );

            if (confirmation === 'ELIMINAR TODO') {
                deleteAllSubscriptions();
            } else {
                showAlert('Acción cancelada', 'warning');
            }
        }

        async function deleteAllSubscriptions() {
            try {
                const response = await fetch('/push-manager/clean-all', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) {
                    throw new Error('Error al eliminar todas las suscripciones');
                }

                const data = await response.json();
                showAlert(data.message, 'warning');
                
                // Recargar página
                setTimeout(() => {
                    location.reload();
                }, 2000);

            } catch (error) {
                console.error('Error:', error);
                showAlert('Error: ' + error.message, 'error');
            }
        }
    </script>
</body>
</html>
