// resources/js/topbar.js
window.addEventListener('DOMContentLoaded', () => {
  console.log('[topbar] loaded');

  const root = document.documentElement;
  const LS_KEY = 'pt_theme';

  const btnSettings = document.getElementById('btnSettings');
  const menu        = document.getElementById('menuSettings');
  const btnDark     = document.getElementById('btnDarkMode');

  if (!btnSettings || !menu) {
    console.warn('[topbar] elements not found');
    return;
  }

  const getTheme = () => root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
  const setTheme = (mode) => {
    if (mode === 'dark') {
      root.setAttribute('data-theme', 'dark');
      localStorage.setItem(LS_KEY, 'dark');
    } else {
      root.removeAttribute('data-theme');
      localStorage.setItem(LS_KEY, 'light');
    }
  };

  const openMenu  = () => {
    menu.classList.remove('hidden');
    btnSettings.setAttribute('aria-expanded', 'true');
    if (btnDark) btnDark.classList.toggle('is-on', getTheme() === 'dark');
  };
  const closeMenu = () => {
    if (menu.classList.contains('hidden')) return;
    menu.classList.add('hidden');
    btnSettings.setAttribute('aria-expanded', 'false');
  };

  // Toggle dropdown
  btnSettings.addEventListener('click', (e) => {
    e.stopPropagation();
    menu.classList.contains('hidden') ? openMenu() : closeMenu();
  });

  // Close when clicking outside / ESC
  document.addEventListener('click', (e) => {
    if (!menu.contains(e.target) && e.target !== btnSettings && !btnSettings.contains(e.target)) {
      closeMenu();
    }
  });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeMenu(); });

  // Dark mode toggle
  if (btnDark) {
    btnDark.addEventListener('click', (e) => {
      e.preventDefault();
      const next = getTheme() === 'dark' ? 'light' : 'dark';
      setTheme(next);
      btnDark.classList.toggle('is-on', next === 'dark');
    });
    // Sinkron awal
    btnDark.classList.toggle('is-on', getTheme() === 'dark');
  }
});
