// Notification Management
const NotificationManager = {
    init() {
        this.updateNotificationCount();
        this.initializePolling();
    },

    updateNotificationCount() {
        fetch('/notifications/count')
            .then(response => response.json())
            .then(data => {
                const counter = document.getElementById('notification-counter');
                if (counter) {
                    if (data.count > 0) {
                        counter.textContent = data.count;
                        counter.classList.remove('d-none');
                    } else {
                        counter.classList.add('d-none');
                    }
                }
            });
    },

    markAsRead(id) {
        fetch(`/notifications/${id}/mark-as-read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateNotificationCount();
                const notification = document.querySelector(`[data-notification-id="${id}"]`);
                if (notification) {
                    notification.classList.remove('unread');
                }
            }
        });
    },

    markAllAsRead() {
        fetch('/notifications/mark-all-as-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateNotificationCount();
                document.querySelectorAll('.notification.unread').forEach(el => {
                    el.classList.remove('unread');
                });
            }
        });
    },

    initializePolling() {
        // Poll for new notifications every 30 seconds
        setInterval(() => this.updateNotificationCount(), 30000);
    }
};

// Initialize notification management when document is ready
document.addEventListener('DOMContentLoaded', () => {
    NotificationManager.init();
});