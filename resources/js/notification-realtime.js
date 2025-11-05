// resources/js/bootstrap.js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true
});

// Listen for notifications
window.Echo.private(`App.Models.User.${userId}`)
    .notification((notification) => {
        NotificationManager.addNotification(notification);
    });

// Extend NotificationManager with real-time handling
Object.assign(NotificationManager, {
    addNotification(notification) {
        // Update notification counter
        this.updateNotificationCount();
        
        // Add notification to dropdown if it exists
        const dropdownList = document.querySelector('.notification-list');
        if (dropdownList) {
            const template = this.createNotificationTemplate(notification);
            dropdownList.insertAdjacentHTML('afterbegin', template);
            
            // Remove oldest notification if more than 5
            const notifications = dropdownList.querySelectorAll('.notification');
            if (notifications.length > 5) {
                notifications[notifications.length - 1].remove();
            }
        }
        
        // Show toast notification
        this.showToast(notification);
    },
    
    createNotificationTemplate(notification) {
        const icons = {
            OrderPlacedNotification: 'shopping-cart',
            OrderShippedNotification: 'truck',
            OrderDeliveredNotification: 'check-circle',
            PaymentReceivedNotification: 'money-bill'
        };
        
        const icon = icons[notification.type.split('\\').pop()] || 'bell';
        
        return `
            <div class="dropdown-item notification unread" data-notification-id="${notification.id}">
                <div class="d-flex">
                    <div class="notification-icon">
                        <i class="fas fa-${icon} text-primary"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${notification.data.title}</div>
                        <div class="notification-message">${notification.data.message}</div>
                        <div class="notification-time">Just now</div>
                    </div>
                </div>
                ${notification.data.link ? `
                    <a href="${notification.data.link}" class="btn btn-sm btn-primary mt-2">
                        View Details
                    </a>
                ` : ''}
            </div>
        `;
    },
    
    showToast(notification) {
        const toast = document.createElement('div');
        toast.className = 'toast position-fixed bottom-0 end-0 m-3';
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="toast-header">
                <strong class="me-auto">${notification.data.title}</strong>
                <small>Just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${notification.data.message}
            </div>
        `;
        
        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        // Remove toast after it's hidden
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }
});