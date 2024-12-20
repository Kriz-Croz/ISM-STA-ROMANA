function updateSidebarNotification() {
    fetch('notification_count.php')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('sidebar-notification-badge');
            if (data.notificationCount > 0) {
                badge.textContent = data.notificationCount;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        })
        .catch(error => console.error('Error fetching notification count:', error));
}

setInterval(updateSidebarNotification, 1000);

document.addEventListener('DOMContentLoaded', updateSidebarNotification);
