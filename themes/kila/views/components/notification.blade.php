<div x-data>
    <template x-for="(notification, index) in $store.notifications.notifications" :key="notification.id">
        <div x-show="notification.show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90" @click="$store.notifications.removeNotification(notification.id)"
            :class="notification.type === 'success' ? 'bg-secondary' : 'bg-red-500'"
            class="fixed text-white px-4 py-2 rounded shadow-md mb-4 z-50"
            :style="'top: ' + (20 + index * 60) + 'px;left: 50%; transform: translateX(-50%);'">
            <p x-text="notification.message"></p>
        </div>
    </template>
</div>
