(function () {
  function qsa(sel, root) {
    return Array.from((root || document).querySelectorAll(sel));
  }

  const STORAGE_KEY = 'managerActivePage';

  function getParamPage() {
    try {
      const params = new URLSearchParams(window.location.search);
      const p = params.get('page');
      return p ? p.trim() : null;
    } catch (e) {
      return null;
    }
  }

  function fromParentHref() {
    try {
      const win = (window.parent !== window) ? window.parent : window;
      const href = win.location ? win.location.href : '';
      const m = href.match(/\/([^\/?#]+)\.html/i);
      if (m) return m[1];
    } catch (e) {}
    return null;
  }

  function fromStorage() {
    try {
      const v = localStorage.getItem(STORAGE_KEY);
      return v ? v.trim() : null;
    } catch (e) {
      return null;
    }
  }

  function getCurrentPage() {
    const fromParam = getParamPage();
    if (fromParam) return fromParam;

    const fromHref = fromParentHref();
    if (fromHref) return fromHref;

    const stored = fromStorage();
    if (stored) return stored;

    return 'dashboard';
  }

  function setTargets() {
    qsa('.nav-link').forEach(a => {
      a.setAttribute('target', '_parent');
    });
  }

  function setActiveByKey(key) {
    if (!key) return;
    qsa('.nav-link').forEach(a => {
      const k = a.getAttribute('data-page');
      if (k === key) a.classList.add('active');
      else a.classList.remove('active');
    });
  }

  function saveActive(key) {
    try { localStorage.setItem(STORAGE_KEY, key); } catch (e) {}
  }

  function bindClicks() {
    qsa('.nav-link').forEach(a => {
      a.addEventListener('click', function () {
        const key = a.getAttribute('data-page');
        if (!key) return;
        saveActive(key);
        setActiveByKey(key);
      });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    setTargets();
    bindClicks();
    const current = getCurrentPage();
    setActiveByKey(current);
  });
})();