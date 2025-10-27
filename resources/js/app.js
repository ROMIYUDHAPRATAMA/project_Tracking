import Gantt from 'frappe-gantt';


document.addEventListener('DOMContentLoaded', () => {
  const btn  = document.getElementById('btnSettings');
  const menu = document.getElementById('menuSettings');
  if (!btn || !menu) return;

  const closeMenu = () => {
    if (!menu.classList.contains('hidden')) {
      menu.classList.add('hidden');
      btn.setAttribute('aria-expanded','false');
    }
  };

  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    const open = !menu.classList.contains('hidden');
    if (open) closeMenu();
    else { menu.classList.remove('hidden'); btn.setAttribute('aria-expanded','true'); }
  });

  document.addEventListener('click', (e) => {
    if (!menu.contains(e.target) && e.target !== btn) closeMenu();
  });

  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeMenu(); });
});
