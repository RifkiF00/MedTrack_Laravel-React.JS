// Notification Panel Function
function toggleNotificationPanel() {
    const panel = document.getElementById('notificationPanel');
    const overlay = document.getElementById('notificationOverlay');
    if (panel) {
        const isHidden = panel.style.display === 'none' || panel.style.display === '';
        panel.style.display = isHidden ? 'block' : 'none';
        if (overlay) {
            overlay.style.display = isHidden ? 'block' : 'none';
        }
    }
}

// Close notification when clicking outside
document.addEventListener('click', function (e) {
    const panel = document.getElementById('notificationPanel');
    const bell = document.getElementById('notificationBell');
    const overlay = document.getElementById('notificationOverlay');

    if (panel && bell && panel.style.display === 'block') {
        if (!panel.contains(e.target) && !bell.contains(e.target)) {
            panel.style.display = 'none';
            if (overlay) {
                overlay.style.display = 'none';
            }
        }
    }
});

// Close notification when clicking overlay
document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('notificationOverlay');
    if (overlay) {
        overlay.addEventListener('click', function () {
            const panel = document.getElementById('notificationPanel');
            if (panel) {
                panel.style.display = 'none';
            }
            overlay.style.display = 'none';
        });
    }

    // Fitur Toggle Sidebar Kanan Saja
    const wrapper = document.querySelector('.dashboard-wrapper');
    const btnRight = document.getElementById('toggleRight');

    if (btnRight && wrapper) {
        btnRight.addEventListener('click', function () {
            wrapper.classList.toggle('hide-right');
        });
    }

    // Fitur Search Sidebar
    const searchInput = document.getElementById('sidebarSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const searchTerm = this.value.toLowerCase().trim();
            const sidebarItems = document.querySelectorAll('.sidebar-right > div[style*="margin-bottom"]');

            sidebarItems.forEach(section => {
                const items = section.querySelectorAll('div[style*="padding"], strong');
                let visibleItems = 0;

                section.querySelectorAll('div > div[style*="padding"]').forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if (searchTerm === '' || text.includes(searchTerm)) {
                        item.style.display = 'block';
                        visibleItems++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Hide section if no items match
                if (visibleItems === 0) {
                    section.style.display = 'none';
                } else {
                    section.style.display = 'block';
                }
            });
        });
    }
});