self.addEventListener('push', event => {
    // // Check if user is active
    // if (self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(clients => clients.some(client => client.focused))) {
    //     // We need to open up the app and show an in-app notification instead

    //     return
    // }
    const notification = event.data.json()

    event.waitUntil(
        self.registration.showNotification(notification.title, {
            body: notification.body,
            icon: notification.icon,
            badge: notification.icon,  
            data: {
                url: notification.url
            }
        })
    )
})

self.addEventListener('notificationclick', event => {
    event.waitUntil(clients.openWindow(event.notification.data.url))
})
