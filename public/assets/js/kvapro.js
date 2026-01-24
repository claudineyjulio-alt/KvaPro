const sidebar = document.getElementById('sidebar');
const wrapper = document.getElementById('mainWrapper');
const overlay = document.getElementById('overlay');

function handleSidebarToggle() {
    if (window.innerWidth <= 768) {
        sidebar.classList.toggle('mobile-active');
        overlay.classList.toggle('active');
    } else {
        sidebar.classList.toggle('collapsed');
        wrapper.classList.toggle('expanded');
    }
}

function closeMobileSidebar() {
    sidebar.classList.remove('mobile-active');
    overlay.classList.remove('active');
}
window.addEventListener('resize', () => {
    if (window.innerWidth > 768) {
        sidebar.classList.remove('mobile-active');
        overlay.classList.remove('active');
    }
});
