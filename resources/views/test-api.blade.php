<!DOCTYPE html>
<html>
<head>
    <title>Test Push API</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        button { padding: 10px 20px; margin: 10px; background: #007bff; color: white; border: none; cursor: pointer; }
        #results { margin-top: 20px; padding: 20px; background: #f8f9fa; border: 1px solid #ddd; }
        .error { color: red; }
        .success { color: green; }
        .info { color: blue; }
    </style>
</head>
<body>
    <h1>🧪 Test Push Notifications API</h1>
    
    <div>
        <button onclick="testVapidKey()">1. Test VAPID Key</button>
        <button onclick="testSubscribe()">2. Test Subscribe</button>
        <button onclick="testSendNotification()">3. Test Send Notification</button>
        <button onclick="testAll()">🚀 Test All</button>
    </div>
    
    <div id="results">
        <p>Haz clic en los botones para probar los endpoints...</p>
    </div>

    <script>
        function log(message, type = 'info') {
            const results = document.getElementById('results');
            const timestamp = new Date().toLocaleTimeString();
            results.innerHTML += `<div class="${type}">[${timestamp}] ${message}</div>`;
        }

        function clearResults() {
            document.getElementById('results').innerHTML = '';
        }

        async function testVapidKey() {
            log('🔑 Testing VAPID Key endpoint...');
            try {
                const response = await fetch('/push/vapid-public-key');
                log(`Response status: ${response.status}`);
                
                if (response.ok) {
                    const data = await response.json();
                    log(`✅ VAPID Key: ${data.publicKey}`, 'success');
                } else {
                    log(`❌ Error: HTTP ${response.status}`, 'error');
                }
            } catch (error) {
                log(`❌ Network Error: ${error.message}`, 'error');
            }
        }

        async function testSubscribe() {
            log('📝 Testing Subscribe endpoint...');
            try {
                const mockSubscription = {
                    endpoint: 'https://test.endpoint.com/mock-' + Date.now(),
                    keys: {
                        p256dh: 'mock_p256dh_key_' + Math.random().toString(36),
                        auth: 'mock_auth_token_' + Math.random().toString(36)
                    }
                };

                const response = await fetch('/push/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(mockSubscription)
                });

                log(`Response status: ${response.status}`);
                
                if (response.ok) {
                    const data = await response.json();
                    log(`✅ Subscription created: ${JSON.stringify(data)}`, 'success');
                } else {
                    const errorText = await response.text();
                    log(`❌ Error: HTTP ${response.status} - ${errorText}`, 'error');
                }
            } catch (error) {
                log(`❌ Network Error: ${error.message}`, 'error');
            }
        }

        async function testSendNotification() {
            log('📢 Testing Send Notification endpoint...');
            try {
                const payload = {
                    title: 'Test Notification',
                    message: 'This is a test message from the test page'
                };

                const response = await fetch('/push/send-test', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });

                log(`Response status: ${response.status}`);
                
                if (response.ok) {
                    const data = await response.json();
                    log(`✅ Notification sent: ${JSON.stringify(data)}`, 'success');
                } else {
                    const errorText = await response.text();
                    log(`❌ Error: HTTP ${response.status} - ${errorText}`, 'error');
                }
            } catch (error) {
                log(`❌ Network Error: ${error.message}`, 'error');
            }
        }

        async function testAll() {
            clearResults();
            log('🚀 Running all tests...', 'info');
            
            await testVapidKey();
            await new Promise(resolve => setTimeout(resolve, 500));
            
            await testSubscribe();
            await new Promise(resolve => setTimeout(resolve, 500));
            
            await testSendNotification();
            
            log('✅ All tests completed!', 'success');
        }
    </script>
</body>
</html>
