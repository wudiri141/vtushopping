(function () {
  var shell = document.querySelector('[data-admin-shell]');
  if (!shell) return;

  var STORAGE_KEY = 'admin-sidebar-collapsed';
  var collapseBtn = document.querySelector('[data-admin-collapse]');
  var mobileBtn = document.querySelector('[data-admin-mobile-toggle]');
  var backdrop = document.querySelector('[data-admin-backdrop]');

  try {
    if (localStorage.getItem(STORAGE_KEY) === '1') {
      shell.classList.add('is-collapsed');
    }
  } catch (e) {}

  function setCollapsed(collapsed) {
    shell.classList.toggle('is-collapsed', collapsed);
    try {
      localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0');
    } catch (e) {}
  }

  if (collapseBtn) {
    collapseBtn.addEventListener('click', function () {
      setCollapsed(!shell.classList.contains('is-collapsed'));
    });
  }

  function setMobileOpen(open) {
    shell.classList.toggle('is-mobile-open', open);
  }

  if (mobileBtn) {
    mobileBtn.addEventListener('click', function () {
      setMobileOpen(!shell.classList.contains('is-mobile-open'));
    });
  }

  if (backdrop) {
    backdrop.addEventListener('click', function () {
      setMobileOpen(false);
    });
  }

  // Auto-dismiss flash messages after a few seconds.
  document.querySelectorAll('[data-admin-flash]').forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity .3s ease';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 300);
    }, 4500);
  });
})();
