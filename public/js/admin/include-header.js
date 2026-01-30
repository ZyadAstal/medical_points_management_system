(function () {
  const mount = document.getElementById('headerMount');
  if (!mount) return;

  const DEFAULT_ADMIN_NAME = 'د.أحمد محمود';
  const adminName = (localStorage.getItem('adminName') || DEFAULT_ADMIN_NAME).trim();
  const adminEmail = (localStorage.getItem('adminEmail') || localStorage.getItem('userEmail') || '').trim();

  function getInitials(fullName) {
    const cleaned = (fullName || '')
      .replace(/[.،,:;()\[\]{}]/g, ' ')
      .replace(/\s+/g, ' ')
      .trim();

    const rawTokens = cleaned.split(' ').filter(Boolean);

    const TITLE_TOKENS = new Set(['د', 'دكتور', 'أ', 'أستاذ', 'م', 'م.', 'د.']);
    const tokens = rawTokens.filter((t) => !TITLE_TOKENS.has(t) && t.length > 1);

    const first = tokens[0] || rawTokens[0] || '';
    const second = tokens[1] || rawTokens[1] || '';

    const a = first ? first[0] : '';
    const b = second ? second[0] : '';

    if (a && b) return `${a}${b}`;
    if (a) return a;
    return '؟';
  }

  const initials = getInitials(adminName);

  mount.innerHTML = `
    <header class="header header-v2" aria-label="Header">
      <div class="header-v2__inner">

        <div class="header-v2__user">
          <span class="header-v2__divider" aria-hidden="true"></span>

          <div class="header-v2__avatar" id="adminAvatar">${initials}</div>

          <div class="header-v2__meta">
            <div class="header-v2__name" id="adminNameText">${adminName}</div>
            <div class="header-v2__role">مدير النظام</div>
          </div>

          <div class="header-v2__menuWrap">
          <button class="header-v2__arrow" id="userMenuBtn" type="button" aria-label="قائمة المستخدم">
            <img src="../assets/icons/arrow.svg" alt="" />
          </button>
          <div class="header-v2__userDropdown" id="userDropdown" aria-hidden="true">
            <div class="user-dd__roleLabel">الدور</div>
            <div class="user-dd__roleValue">مدير النظام</div>
                        <span class="user-dd__line user-dd__line--1" aria-hidden="true"></span>
            <div class="user-dd__emailLabel">البريد الالكتروني</div>
            <div class="user-dd__emailValue" id="dropdownAdminEmail"></div>

            <span class="user-dd__line user-dd__line--2" aria-hidden="true"></span>
            <div class="user-dd__logoutText" id="logoutBtn">تسجيل خروج</div>
            <img class="user-dd__logoutIcon" id="logoutIconBtn" src="../assets/icons/exit.svg" alt="" />

          </div>
        </div>
        </div>

        <button class="header-v2__notifBtn" id="notifBtn" type="button" aria-label="الإشعارات">
          <img src="../assets/icons/notfacation.svg" alt="" />
          <span class="header-v2__notifDot" id="notifDot" aria-hidden="true"></span>
        </button>

        <div class="header-v2__notifMenu" id="notifMenu" aria-hidden="true">
          <div class="header-v2__notifTitle">الإشعارات</div>
          <div class="header-v2__notifEmpty" id="notifEmpty">لا توجد إشعارات جديدة</div>
        </div>

      </div>
    </header>
  `;


  const emailEl = document.getElementById('dropdownAdminEmail');
  if (emailEl) {
    emailEl.textContent = adminEmail || 'ahmedmahmoud@gmail.com';
  }


  const notifBtn = document.getElementById('notifBtn');
  const userMenuBtn = document.getElementById('userMenuBtn');
  const userDropdown = document.getElementById('userDropdown');
  const notifMenu = document.getElementById('notifMenu');
  const notifDot = document.getElementById('notifDot');

  function closeAll() {
    notifMenu?.classList.remove('open');
    notifMenu?.setAttribute('aria-hidden', 'true');

    userDropdown?.classList.remove('open');
    userDropdown?.setAttribute('aria-hidden', 'true');
  }

  const hasNewNotif = localStorage.getItem('hasNewNotif') === '1';
  if (!hasNewNotif && notifDot) notifDot.style.display = 'none';

userMenuBtn?.addEventListener('click', (e) => {
  e.stopPropagation();
  closeAll();
  const open = userDropdown.classList.toggle('open');
  userDropdown.setAttribute('aria-hidden', open ? 'false' : 'true');
});

  notifBtn?.addEventListener('click', (e) => {
    e.stopPropagation();
    const open = notifMenu.classList.toggle('open');
    notifMenu.setAttribute('aria-hidden', open ? 'false' : 'true');
    if (open) {
      localStorage.setItem('hasNewNotif', '0');
      if (notifDot) notifDot.style.display = 'none';
    }
  });


  function getLoginHref() {
    const p = window.location.pathname || '';
    if (p.includes('/auth/')) return 'login.html';
    if (p.includes('/pages/') || p.includes('/layouts/')) return '../auth/login.html';
    return 'auth/login.html';
  }

  function doLogout() {

    ['adminEmail','userEmail','adminName','isLoggedIn','token','accessToken'].forEach(k => {
      try { localStorage.removeItem(k); } catch(_) {}
    });
    window.location.href = getLoginHref();
  }

  const logoutBtn = document.getElementById('logoutBtn');
  const logoutIconBtn = document.getElementById('logoutIconBtn');
  logoutBtn?.addEventListener('click', (e) => { e.stopPropagation(); doLogout(); });
  logoutIconBtn?.addEventListener('click', (e) => { e.stopPropagation(); doLogout(); });


document.addEventListener('click', () => closeAll());
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeAll();
  });
})();
