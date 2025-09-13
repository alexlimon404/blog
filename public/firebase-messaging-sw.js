importScripts('https://www.gstatic.com/firebasejs/10.13.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.13.0/firebase-messaging-compat.js');

let messaging;

// Initialize Firebase with dynamic config
fetch('/firebase-config')
    .then(response => response.json())
    .then(config => {
        firebase.initializeApp(config);
        messaging = firebase.messaging();
        setupMessageHandling();
    })
    .catch(error => {
        console.error('Error loading Firebase config:', error);
    });

function setupMessageHandling() {
    messaging.onBackgroundMessage((payload) => {
        console.log('Received background message: ', payload);
        
        const notificationTitle = payload.notification?.title || 'New notification';
        const notificationOptions = {
            body: payload.notification?.body || 'You have a new message',
            icon: '/favicon.ico',
            badge: '/favicon.ico',
            tag: 'blog-notification',
            data: payload.data || {}
        };

        self.registration.showNotification(notificationTitle, notificationOptions);
    });
}

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    
    event.waitUntil(
        clients.openWindow('/')
    );
});