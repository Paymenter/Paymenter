import { Livewire, Alpine } from '../../../vendor/livewire/livewire/dist/livewire.esm';

document.addEventListener('livewire:init', () => {
    Livewire.hook('request', ({ fail }) => {
        fail(({ status, preventDefault }) => {
            if (status === 419) {
                window.location.reload()

                preventDefault()
            }
        })
    })
})

Alpine.store('notifications', {
    init () {
        Livewire.on('notify', e => {
            Alpine.store('notifications').addNotification(e)
        })
    },
    notifications: [],
    addNotification (notification) {
        notification = notification[0]
        notification.show = false
        notification.id = Date.now() + Math.floor(Math.random() * 1000)
        this.notifications.push(notification)

        // Update the notification to show it
        Alpine.nextTick(() => {
            this.notifications = this.notifications.map(notification => {
                if (notification.id === notification.id) {
                    notification.show = true
                }
                return notification
            })
        })

        setTimeout(() => {
            this.removeNotification(notification.id)
        }, notification.timeout || 5000)
    },
    removeNotification (id) {
        this.notifications = this.notifications
            .map(notification => {
                if (notification.id === id) {
                    notification.show = false
                }
                return notification
            })
            .filter(notification => notification.show) // This line filters out notifications that are not shown
    }
})

Alpine.store('confirmation', {
    show: false,
    loading: false,
    title: '',
    message: '',
    confirmText: 'Confirm',
    cancelText: 'Cancel',
    callback: null,

    confirm (options) {
        this.show = true
        this.loading = false
        this.title = options.title
        this.message = options.message
        this.confirmText = options.confirmText || 'Confirm'
        this.cancelText = options.cancelText || 'Cancel'
        this.callback = options.callback
    },

    async execute () {
        if (this.loading) return

        this.loading = true

        try {
            if (this.callback) {
                await this.callback()
                this.loading = false
            }
            this.close()
        } catch (error) {
            console.error('Callback failed:', error)
            this.close()
        }
    },

    close () {
        if (this.loading) return

        this.show = false
        this.loading = false
        this.callback = null
    }
})

if ('serviceWorker' in navigator) {
    navigator.serviceWorker
        .register('/service-worker.js')
        .then(function (registration) {
            console.log(
                'Service Worker registered with scope:',
                registration.scope
            )
        })
        .catch(function (error) {
            console.log('Service Worker registration failed:', error)
        })

    navigator.serviceWorker.onmessage = function (event) {
        if (event.data && event.data.type === 'SHOW_NOTIFICATION') {
            Livewire.dispatch('notification-added', [event.data.notification])
            window.dispatchEvent(new CustomEvent('new-notification'))
        }
    }
}


Livewire.start()
