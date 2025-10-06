// Admin/admin_dashboard.js
document.addEventListener('DOMContentLoaded', function() {
        // Navigation functionality
        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.addEventListener('click', function() {
                const pageId = this.getAttribute('data-page');
                showPage(pageId);
            });
        });
        
        function showPage(pageId) {
            const pages = document.querySelectorAll('.page');
            pages.forEach(page => {
                page.classList.remove('active');
            });
            document.getElementById(pageId).classList.add('active');

            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach(item => {
                item.classList.remove('active');
            });
            event.currentTarget.classList.add('active');
        }

        // Simple search functionality for profiles
        document.getElementById('profileSearch').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const profiles = document.querySelectorAll('.profile-card');
            profiles.forEach(profile => {
                const name = profile.querySelector('h3').innerText.toLowerCase();
                if (name.includes(query)) {
                    profile.style.display = '';
                } else {
                    profile.style.display = 'none';
                }
            });
        });
    });