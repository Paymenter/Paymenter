import { Livewire, Alpine } from '../../../vendor/livewire/livewire/dist/livewire.esm';
import anchor from '@alpinejs/anchor';

document.addEventListener('livewire:init', () => {
    Livewire.hook('request', ({ fail }) => {
        fail(({ status, preventDefault }) => {
            if (status === 419) {
                window.location.reload();
                preventDefault();
            }
        });
    });
});

Alpine.store('notifications', {
    init() {
        Livewire.on('notify', e => {
            Alpine.store('notifications').addNotification(e);
        });
    },
    notifications: [],
    addNotification(notification) {
        notification = notification[0];
        notification.show = false;
        notification.id = Date.now() + Math.floor(Math.random() * 1000);
        this.notifications.push(notification);
        Alpine.nextTick(() => {
            this.notifications = this.notifications.map(n => {
                n.show = true;
                return n;
            });
        });
        setTimeout(() => {
            this.notifications = this.notifications.filter(n => n.id !== notification.id);
        }, 5000);
    },
    removeNotification(id) {
        this.notifications = this.notifications.filter(n => n.id !== id);
    },
});

Alpine.store('confirmation', {
    show: false,
    loading: false,
    title: '',
    message: '',
    confirmText: 'Confirm',
    cancelText: 'Cancel',
    callback: null,
    confirm(options) {
        this.show = true;
        this.loading = false;
        this.title = options.title;
        this.message = options.message;
        this.confirmText = options.confirmText || 'Confirm';
        this.cancelText = options.cancelText || 'Cancel';
        this.callback = options.callback;
    },
    async execute() {
        if (this.loading) return;
        this.loading = true;
        try {
            if (this.callback) {
                await this.callback();
                this.loading = false;
            }
            this.close();
        } catch (error) {
            console.error('Callback failed:', error);
            this.close();
        }
    },
    close() {
        if (this.loading) return;
        this.show = false;
        this.loading = false;
        this.callback = null;
    },
});

Alpine.plugin(anchor);

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.getRegistrations().then(registrations => {
            for (const registration of registrations) {
                registration.unregister();
            }
        });
    });
}

Livewire.start();
