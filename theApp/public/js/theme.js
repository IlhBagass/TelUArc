
// =======================
// Theme Toggle Logic
// =======================
function toggleTheme() {
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

    html.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);

    updateThemeIcon(newTheme);
}

function updateThemeIcon(theme) {
    const toggleBtn = document.getElementById('themeToggle');
    if (!toggleBtn) return;

    const icon = toggleBtn.querySelector('i');
    if (theme === 'dark') {
        icon.classList.remove('fa-moon', 'text-gray-600');
        icon.classList.add('fa-sun', 'text-yellow-400');
        toggleBtn.classList.remove('bg-gray-100', 'hover:bg-gray-200');
        toggleBtn.classList.add('bg-gray-700', 'hover:bg-gray-600');
    } else {
        icon.classList.remove('fa-sun', 'text-yellow-400');
        icon.classList.add('fa-moon', 'text-gray-600');
        toggleBtn.classList.add('bg-gray-100', 'hover:bg-gray-200');
        toggleBtn.classList.remove('bg-gray-700', 'hover:bg-gray-600');
    }
}

// Initialize Theme
document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);
});
