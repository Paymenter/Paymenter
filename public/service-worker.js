self.addEventListener('push', event => {
    const notification = event.data.json()

    event.waitUntil(
        (async () => {
            // Check if user has active windows
            const clients = await self.clients.matchAll({ type: 'window' })
            const hasVisibleClient = clients.some(
                client => client.visibilityState === 'visible'
            )

            // Show in-app if user is active and notification supports in-app
            if (notification.show_in_app && hasVisibleClient) {
                // Send in-app notification to all active clients
                const allClients = await self.clients.matchAll({
                    type: 'window',
                    includeUncontrolled: true
                })

                allClients.forEach(client => {
                    client.postMessage({
                        type: 'SHOW_NOTIFICATION',
                        notification: notification
                    })
                })

                // Only show push notification if also configured for push
                if (!notification.show_as_push) {
                    return
                }
            }

            // Show push notification if user is not active OR notification is configured for push
            if (
                notification.show_as_push &&
                (!hasVisibleClient || !notification.show_in_app)
            ) {
                await self.registration.showNotification(notification.title, {
                    body: notification.body,
                    icon: notification.icon,
                    badge: notification.icon,
                    data: {
                        url: notification.url
                    }
                })
            }
        })()
    )
})

self.addEventListener('notificationclick', event => {
    event.waitUntil(clients.openWindow(event.notification.data.url))
})
