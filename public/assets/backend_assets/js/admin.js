(function () {
  'use strict';

  var STORAGE_KEY = 'tahsin_admin_sidebar';

  function getStoredState() {
    try { return localStorage.getItem(STORAGE_KEY) === 'collapsed'; } catch (e) { return false; }
  }
  function setStoredState(collapsed) {
    try { localStorage.setItem(STORAGE_KEY, collapsed ? 'collapsed' : 'expanded'); } catch (e) {}
  }

  function initSidebar() {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebar-overlay');
    var collapseBtn = document.getElementById('sidebar-collapse-btn');
    var toggleBtn = document.getElementById('sidebar-toggle');
    var closeBtn = document.getElementById('sidebar-close-btn');

    if (!sidebar) return;

    if (getStoredState() && window.innerWidth >= 768) {
      sidebar.classList.add('collapsed');
      updateCollapseBtnIcon(collapseBtn, true);
    }

    function updateCollapseBtnIcon(btn, collapsed) {
      if (!btn) return;
      var icon = btn.querySelector('svg');
      if (!icon) return;
      icon.innerHTML = collapsed
        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>'
        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>';
    }

    function toggleCollapse() {
      var isCollapsed = sidebar.classList.contains('collapsed');
      sidebar.classList.toggle('collapsed');
      setStoredState(!isCollapsed);
      updateCollapseBtnIcon(collapseBtn, !isCollapsed);
    }

    if (collapseBtn) {
      collapseBtn.addEventListener('click', function (e) { e.stopPropagation(); toggleCollapse(); });
    }
    if (toggleBtn) {
      toggleBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        sidebar.classList.toggle('open');
        if (overlay) overlay.classList.toggle('active');
      });
    }
    if (closeBtn) {
      closeBtn.addEventListener('click', function () {
        sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('active');
      });
    }
    if (overlay) {
      overlay.addEventListener('click', function () {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
      });
    }
    document.querySelectorAll('#sidebar a[href]').forEach(function (el) {
      el.addEventListener('click', function () {
        if (window.innerWidth < 768) {
          sidebar.classList.remove('open');
          if (overlay) overlay.classList.remove('active');
        }
      });
    });

    var resizeTimer;
    window.addEventListener('resize', function () {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function () {
        if (window.innerWidth < 768) {
          sidebar.classList.remove('collapsed');
        } else {
          var stored = getStoredState();
          if (stored) sidebar.classList.add('collapsed');
          else sidebar.classList.remove('collapsed');
          if (overlay) overlay.classList.remove('active');
        }
      }, 150);
    });
  }

  function initSidebarDropdowns() {
    document.querySelectorAll('[data-dropdown-toggle]').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        var targetId = btn.getAttribute('data-dropdown-toggle');
        var menu = document.getElementById(targetId);
        if (!menu) return;
        var isOpen = menu.classList.contains('open');

        document.querySelectorAll('.sidebar-submenu').forEach(function (m) {
          if (m.id !== targetId) {
            m.classList.remove('open');
            var pb = document.querySelector('[data-dropdown-toggle="' + m.id + '"]');
            if (pb) { var pi = pb.querySelector('[data-dropdown-icon]'); if (pi) pi.classList.remove('rotate-180'); }
          }
        });

        menu.classList.toggle('open');
        var icon = btn.querySelector('[data-dropdown-icon]');
        if (icon) icon.classList.toggle('rotate-180');
      });
    });
  }

  function initProfileDropdown() {
    var btn = document.getElementById('profile-dropdown-btn');
    var menu = document.getElementById('profile-menu');
    if (!btn || !menu) return;
    btn.addEventListener('click', function (e) { e.stopPropagation(); menu.classList.toggle('hidden'); });
    document.addEventListener('click', function (e) {
      if (!menu.classList.contains('hidden') && !menu.contains(e.target) && e.target !== btn && !btn.contains(e.target)) {
        menu.classList.add('hidden');
      }
    });
  }

  function initAlerts() {
    document.querySelectorAll('[data-alert-close]').forEach(function (btn) {
      btn.addEventListener('click', function () { dismissAlert(btn.closest('[role="alert"]')); });
    });
    document.querySelectorAll('.alert[data-auto-dismiss]').forEach(function (alert) {
      var delay = parseInt(alert.getAttribute('data-auto-dismiss'), 10) || 5000;
      setTimeout(function () { dismissAlert(alert); }, delay);
    });
  }
  function dismissAlert(alert) {
    if (!alert) return;
    alert.classList.add('alert-dismissing');
    setTimeout(function () { if (alert.parentNode) alert.parentNode.removeChild(alert); }, 300);
  }

  function initActiveNav() {
    var path = window.location.pathname;
    document.querySelectorAll('.sidebar-nav a').forEach(function (link) {
      var href = link.getAttribute('href');
      if (href && href !== '#' && path.startsWith(href)) {
        link.classList.add('active');
        var sub = link.closest('.sidebar-submenu');
        if (sub) {
          sub.classList.add('open');
          var pb = document.querySelector('[data-dropdown-toggle="' + sub.id + '"]');
          if (pb) { var pi = pb.querySelector('[data-dropdown-icon]'); if (pi) pi.classList.add('rotate-180'); }
        }
      }
    });
  }

  function initFullscreen() {
    var btn = document.getElementById('fullscreen-btn');
    if (!btn) return;
    btn.addEventListener('click', function () {
      if (!document.fullscreenElement) { document.documentElement.requestFullscreen().catch(function(){}); }
      else { if (document.exitFullscreen) document.exitFullscreen(); }
    });
  }

  function initPageAnimations() {
    var main = document.querySelector('main');
    if (main) main.classList.add('page-enter');
  }

  /* ---- SEARCH MODAL ---- */
  function initSearchModal() {
    var modal = document.getElementById('search-modal');
    var backdrop = document.getElementById('search-backdrop');
    var input = document.getElementById('search-input');
    var results = document.getElementById('search-results');
    var items = [];
    var highlightedIndex = -1;

    if (!modal || !input || !results) return;

    function buildItems() {
      items = [];
      document.querySelectorAll('.sidebar-nav a[href]').forEach(function (link) {
        var text = link.querySelector('.sidebar-nav-text');
        var href = link.getAttribute('href');
        if (text && href && href !== '#') {
          var section = link.closest('.sidebar-section-title');
          items.push({ label: text.textContent.trim(), href: href, section: '' });
        }
      });
    }

    function render(query) {
      var q = query.toLowerCase().trim();
      var filtered = q ? items.filter(function (i) { return i.label.toLowerCase().includes(q); }) : items.slice(0, 8);
      var html = '';
      if (filtered.length === 0) {
        html = '<div class="search-empty">No results found for "<strong>' + escapeHtml(q) + '</strong>"</div>';
      } else {
        filtered.forEach(function (item, idx) {
          html += '<a href="' + item.href + '" class="search-result-item" data-index="' + idx + '">'
            + '<span class="sri-icon"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg></span>'
            + '<span class="sri-text">' + escapeHtml(item.label) + '</span>'
            + '<span class="sri-path">' + escapeHtml(item.href) + '</span>'
            + '</a>';
        });
      }
      results.innerHTML = html;
      highlightedIndex = -1;
    }

    function openModal() {
      buildItems();
      render('');
      modal.classList.add('active');
      input.value = '';
      input.focus();
      highlightedIndex = -1;
    }

    function closeModal() {
      modal.classList.remove('active');
    }

    document.addEventListener('keydown', function (e) {
      if ((e.ctrlKey || e.metaKey) && e.key === 'k') { e.preventDefault(); openModal(); }
      if (e.key === 'Escape') closeModal();
    });

    if (backdrop) backdrop.addEventListener('click', closeModal);

    input.addEventListener('input', function () { render(input.value); });

    input.addEventListener('keydown', function (e) {
      var resultItems = results.querySelectorAll('.search-result-item');
      if (e.key === 'ArrowDown') {
        e.preventDefault();
        highlightedIndex = Math.min(highlightedIndex + 1, resultItems.length - 1);
        updateHighlight(resultItems);
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        highlightedIndex = Math.max(highlightedIndex - 1, 0);
        updateHighlight(resultItems);
      } else if (e.key === 'Enter') {
        e.preventDefault();
        if (highlightedIndex >= 0 && resultItems[highlightedIndex]) {
          window.location.href = resultItems[highlightedIndex].getAttribute('href');
        }
      }
    });

    function updateHighlight(items) {
      items.forEach(function (el, idx) {
        el.classList.toggle('highlighted', idx === highlightedIndex);
        if (idx === highlightedIndex) el.scrollIntoView({ block: 'nearest' });
      });
    }

    function escapeHtml(str) {
      var div = document.createElement('div');
      div.appendChild(document.createTextNode(str));
      return div.innerHTML;
    }
  }

  /* ---- BACK TO TOP ---- */
  function initBackToTop() {
    var btn = document.getElementById('back-to-top');
    if (!btn) return;
    window.addEventListener('scroll', function () {
      if (window.scrollY > 300) btn.classList.add('visible');
      else btn.classList.remove('visible');
    });
    btn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ---- TOOLTIPS FOR COLLAPSED SIDEBAR ---- */
  function initTooltips() {
    var sidebar = document.getElementById('sidebar');
    if (!sidebar) return;
    sidebar.addEventListener('mouseenter', function () {
      if (sidebar.classList.contains('collapsed') && window.innerWidth >= 768) {
        sidebar.classList.add('collapsed-hover');
      }
    });
    sidebar.addEventListener('mouseleave', function () {
      sidebar.classList.remove('collapsed-hover');
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initSidebar();
    initSidebarDropdowns();
    initProfileDropdown();
    initAlerts();
    initActiveNav();
    initFullscreen();
    initPageAnimations();
    initSearchModal();
    initBackToTop();
    initTooltips();
  });
})();
