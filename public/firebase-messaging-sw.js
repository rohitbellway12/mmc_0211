importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');

firebase.initializeApp({
    apiKey: "AIzaSyCGoZ-coUFTMUoUcAat5aQUzAu7mj8g26g",
    authDomain: "motor-market-connect-a2eff.firebaseapp.com",
    projectId: "motor-market-connect-a2eff",
    storageBucket: "motor-market-connect-a2eff.firebasestorage.app",
    messagingSenderId: "361839663402",
    appId: "1:361839663402:web:7ad55d5dac8adcb1e96653",
    measurementId: ""
});

const messaging = firebase.messaging();

self.addEventListener('install', function (event) {
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(self.clients.claim());
});

messaging.setBackgroundMessageHandler(function (payload) {
    const title = payload.notification?.title || payload.data?.title || 'MMC Notification';
    const body = payload.notification?.body || payload.data?.body || payload.data?.description || '';
    const icon = payload.notification?.image || payload.data?.image || payload.data?.icon || '/public/assets/admin-module/img/placeholder.png';

    return self.registration.showNotification(title, {
        body: body,
        icon: icon,
        data: payload.data || {}
    });
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    // Dynamically resolve target URL based on registration scope or payload data (No hardcoded domain needed)
    const clickAction = event.notification?.data?.click_action || event.notification?.data?.url;
    const defaultUrl = new URL('provider/dashboard', self.registration.scope).href;
    const targetUrl = clickAction ? new URL(clickAction, self.registration.scope).href : defaultUrl;

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
            for (var i = 0; i < clientList.length; i++) {
                var client = clientList[i];
                if (client.url && client.url.includes('provider') && 'focus' in client) {
                    if (targetUrl && client.navigate) {
                        client.navigate(targetUrl);
                    }
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
