
document.addEventListener('livewire:init', () => {
    Livewire.hook('request', ({ fail }) => {
        fail(({ status, preventDefault }) => {
            if (status === 419) {
                window.location.reload()

                preventDefault()
            }
        })
    });
})

Alpine.store('notifications', {
    init() {
        Livewire.on('notify', (e) => {
            Alpine.store('notifications').addNotification(e);
        });
    },
    notifications: [],
    addNotification(notification) {
        notification = notification[0]
        notification.show = false;
        notification.id = Date.now() + Math.floor(Math.random() * 1000);
        this.notifications.push(notification);

        // Update the notification to show it
        Alpine.nextTick(() => {
            this.notifications = this.notifications.map(notification => {
                if (notification.id === notification.id) {
                    notification.show = true;
                }
                return notification;
            });
        });

        setTimeout(() => {
            this.removeNotification(notification.id);
        }, notification.timeout || 5000);
    },
    removeNotification(id) {
        this.notifications = this.notifications.map(notification => {
            if (notification.id === id) {
                notification.show = false;
            }
            return notification;
        }).filter(notification => notification.show); // This line filters out notifications that are not shown
    }
});

