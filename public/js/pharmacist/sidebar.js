(function () {
  function qsa(sel, root) {
    return Array.from((root || document).querySelectorAll(sel));
  }

  function getParamPage() {
    try {
      const params = new URLSearchParams(window.location.search);
      const p = params.get('page');
      return p ? p.trim() : null;
    } catch (e) {
      return null;
    }
  }

  function getCurrentPage() {
    const fromParam = getParamPage();
    if (fromParam) return fromParam;

    try {
      const p = window.parent && window.parent.document && window.parent.document.body
        ? window.parent.document.body.getAttribute('data-page')
        : null;
      if (p) return p;
    } catch (e) {}

    try {
      const href = window.parent && window.parent.location ? window.parent.location.href : '';
      const m = href.match(/\/([^\/?#]+)\.html/i);
      if (m) return m[1];
    } catch (e) {}

    return null;
  }

  function setTargets() {
    qsa('.nav-link').forEach(a => {
      a.setAttribute('target', '_parent');
    });
  }

  function setActive() {
    const current = getCurrentPage();
    if (!current) return;

    qsa('.nav-link').forEach(a => {
      const key = a.getAttribute('data-page');
      if (key === current) a.classList.add('active');
      else a.classList.remove('active');
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    setTargets();
    setActive();
  });
})();
