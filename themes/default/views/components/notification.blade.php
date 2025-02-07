<div x-data="notificationsHandler()" @notify.window="addNotification($event.detail)">
    <template x-for="(notification, index) in notifications" :key="notification.id">
        <div x-show="notification.show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90" @click="removeNotification(notification.id)"
            :class="notification.type === 'success' ? 'bg-secondary' : 'bg-red-500'"
            class="fixed bg-secondary text-white px-4 py-2 rounded shadow-md mb-4 z-50"
            :style="'top: ' + (20 + index * 60) + 'px;left: 50%; transform: translateX(-50%);'">
            <p x-text="notification.message"></p>
        </div>
    </template>
</div>

<script>
    function notificationsHandler() {
        return {
            notifications: [],
            addNotification(notification) {
                notification = notification[0]
                notification.show = false;
                notification.id = Date.now();
                this.notifications.push(notification);

                // Update the notification to show it
                this.$nextTick(() => {
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
        };
    }
</script>
