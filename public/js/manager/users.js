(function(){
  const table = document.querySelector('.users-table');
  if(!table) return;

  const tbody = table.querySelector('tbody');
  const getRows = () => Array.from(tbody ? tbody.querySelectorAll('tr') : []);

  const STORAGE_KEY = 'medicare_users_v1';

  function loadUsers(){
    try{
      const raw = localStorage.getItem(STORAGE_KEY);
      return raw ? JSON.parse(raw) : [];
    }catch(e){
      return [];
    }
  }

  function saveUsers(users){
    try{
      localStorage.setItem(STORAGE_KEY, JSON.stringify(users || []));
    }catch(e){}
  }

  function normalizeIdFromEmail(email){
    return (email || '').trim().toLowerCase();
  }

  function escapeHtml(str){
    return String(str ?? '')
      .replace(/&/g,'&amp;')
      .replace(/</g,'&lt;')
      .replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;')
      .replace(/'/g,'&#39;');
  }

  function seedUsersFromDomIfEmpty(){
    const existing = loadUsers();
    if(existing && existing.length) return existing;

    const domRows = Array.from(tbody ? tbody.querySelectorAll('tr') : []);
    const seeded = domRows.map(function(tr){
      const localCell = function(t, nth){ const td=t.querySelector(`td:nth-child(${nth})`); return (td?td.textContent:'').replace(/\s+/g,' ').trim(); };
      const name = localCell(tr, 1);
      const email = localCell(tr, 2);
      const role = localCell(tr, 3);
      const center = localCell(tr, 4);

      const statusBadge = tr.querySelector('.status-badge');
      const statusText = statusBadge ? statusBadge.textContent.trim() : localCell(tr, 5);
      const status = statusText || 'نشط';

      return {
        id: normalizeIdFromEmail(email) || (Date.now() + Math.random()).toString(36),
        name, email, role, center, status
      };
    });

    saveUsers(seeded);
    return seeded;
  }

  function renderUsers(users){
    if(!tbody) return;

    const html = (users || []).map(function(u){
      const idParam = encodeURIComponent(u.id);
      const statusText = (u.status || '').trim() || 'نشط';
      const isActive = statusText.includes('نشط');
      const badgeClass = isActive ? 'status-badge active' : 'status-badge inactive';

      return `
      <tr class="user-row" data-user-id="${escapeHtml(u.id)}">
        <td class="cell-name">${escapeHtml(u.name)}</td>
        <td class="cell-email">${escapeHtml(u.email)}</td>
        <td class="cell-role">${escapeHtml(u.role)}</td>
        <td class="cell-center">${escapeHtml(u.center)}</td>
        <td class="cell-status"><span class="${badgeClass}">${escapeHtml(statusText)}</span></td>
        <td class="cell-actions">
          <div class="actions-grid" aria-label="إجراءات المستخدم">
            <button class="action-square js-user-edit" type="button" data-user-id="${escapeHtml(u.id)}" title="تعديل">
              <img src="../assets/icons/edit.svg" alt="تعديل" width="20" height="20" />
            </button>
            <button class="action-square js-user-delete" type="button" data-user-id="${escapeHtml(u.id)}" title="حذف">
              <img src="../assets/icons/delete.svg" alt="حذف" width="20" height="20" />
            </button>
          </div>
        </td>
      </tr>`;
    }).join('');

    tbody.innerHTML = html;
  }


  const state = {
    role: 'all',
    status: 'all',
    center: 'all',
    search: ''
  };

  function cellText(tr, nth){
    const td = tr.querySelector(`td:nth-child(${nth})`);
    return (td ? td.textContent : '').replace(/\s+/g,' ').trim();
  }

  function normalizeForSearch(s){
    return (s || '')
      .toString()
      .toLowerCase()
      .replace(/[\u064B-\u065F\u0670]/g,'') // tashkeel
      .replace(/\u0640/g,'') // tatweel
      .replace(/\s+/g,' ')
      .trim();
  }

  function applyFilters(){
    getRows().forEach(tr => {
      if(tr.closest('thead')) return;

      const rName = cellText(tr, 1);
      const rRole = cellText(tr, 3);
      const rCenter = cellText(tr, 4);
      const rStatus = cellText(tr, 5);

      const okRole = (state.role === 'all') || (rRole === state.role);
      const okStatus = (state.status === 'all') || (rStatus === state.status);
      const okCenter = (state.center === 'all') || (rCenter === state.center);
      const q = normalizeForSearch(state.search);
      const okSearch = !q || normalizeForSearch(rName).includes(q);

      tr.style.display = (okRole && okStatus && okCenter && okSearch) ? '' : 'none';
    });
  }

  function closeAllMenus(){
    document.querySelectorAll('.filter-menu.open').forEach(m => {
      m.classList.remove('open');
      m.setAttribute('aria-hidden','true');
    });
  }

  function setupFilter(filterSelector, menuSelector, onSelect){
    const filter = document.querySelector(filterSelector);
    if(!filter) return;

    const box = filter.querySelector('.filter-box');
    const textEl = filter.querySelector('.filter-text');
    const menu = filter.querySelector(menuSelector);

    if(box){
      box.addEventListener('click', function(e){
        e.preventDefault();
        e.stopPropagation();

        const isOpen = menu && menu.classList.contains('open');
        closeAllMenus();
        if(menu && !isOpen){
          menu.classList.add('open');
          menu.setAttribute('aria-hidden','false');
        }
      });
    }

    if(menu){
      menu.addEventListener('click', function(e){
        const item = e.target.closest('.menu-item');
        if(!item) return;

        const value = item.getAttribute('data-value');
        const label = item.textContent.trim();

        if(textEl) textEl.textContent = label;
        onSelect(value);

        closeAllMenus();
        applyFilters();
      });
    }
  }

  setupFilter('.users-filter.filter-role', '.role-menu', function(value){
    state.role = value || 'all';
  });

  setupFilter('.users-filter.filter-status', '.status-menu', function(value){
    state.status = value || 'all';
  });

  setupFilter('.users-filter.filter-center', '.center-menu', function(value){
    state.center = value || 'all';
  });

  const pagination = document.querySelector('.users-pagination');
  if(pagination){
    const pageButtons = Array.from(pagination.querySelectorAll('.page-number'));
    pageButtons.forEach(function(btn){
      btn.addEventListener('click', function(){
        pageButtons.forEach(function(b){ b.classList.remove('active'); });
        btn.classList.add('active');
      });
    });
  }


  const addOverlay = document.getElementById('userAddOverlay');
  const editOverlay = document.getElementById('userEditOverlay');
  const deleteOverlay = document.getElementById('userDeleteOverlay');
  const changePassOverlay = document.getElementById('userChangePassOverlay');

  const addForm = document.getElementById('userAddForm');
  const editForm = document.getElementById('userEditForm') || document.querySelector('.user-edit-fields');
  const changePassForm = document.getElementById('changePassForm');

  const addBtn = document.querySelector('.users-add-btn');
  const addSaveBtn = document.getElementById('userAddSaveBtn');
  const addCancelBtn = document.getElementById('userAddCancelBtn');

  const editUpdateBtn = document.getElementById('userEditUpdateBtn');
  const editCancelBtn = document.getElementById('userEditCancelBtn');
  const openChangePassBtn = document.getElementById('openChangePassBtn');

  const deleteConfirmBtn = document.getElementById('userDeleteConfirmBtn');
  const deleteCancelBtn = document.getElementById('userDeleteCancelBtn');

  const changePassCancelBtn = document.getElementById('changePassCancelBtn');

  let editingUserId = null;
  let deletingUserId = null;
  let changingPassUserId = null;

  function openOverlay(overlay){
    if(!overlay) return;
    overlay.classList.add('is-open');
    overlay.setAttribute('aria-hidden','false');
    document.body.classList.add('modal-open');
  }

  function closeOverlay(overlay){
    if(!overlay) return;
    overlay.classList.remove('is-open');
    overlay.setAttribute('aria-hidden','true');
    const anyOpen = document.querySelector('.modal-overlay.is-open');
    if(!anyOpen) document.body.classList.remove('modal-open');
  }

  function closeAllOverlays(){
    [addOverlay, editOverlay, deleteOverlay, changePassOverlay].forEach(closeOverlay);
  }

  function getUserById(id){
    return (loadUsers() || []).find(u => u.id === id);
  }

  function upsertUser(updated, oldId){
    const users = loadUsers() || [];
    const idx = users.findIndex(u => u.id === (oldId || updated.id));
    if(idx >= 0){
      users[idx] = { ...users[idx], ...updated };
    }else{
      users.push(updated);
    }
    saveUsers(users);
    renderUsers(users);
    applyFilters();
  }

  function deleteUser(id){
    const users = (loadUsers() || []).filter(u => u.id !== id);
    saveUsers(users);
    renderUsers(users);
    applyFilters();
  }

  if(addBtn){
    addBtn.addEventListener('click', function(){
      if(addForm) addForm.reset();
      openOverlay(addOverlay);
    });
  }

  if(addSaveBtn){
    addSaveBtn.addEventListener('click', function(e){
      e.preventDefault();
      if(!addForm) return;
      const name = (addForm.querySelector('#ua_name')?.value || '').trim();
      const email = (addForm.querySelector('#ua_email')?.value || '').trim();
      const role = (addForm.querySelector('#ua_role')?.value || '').trim();
      const center = (addForm.querySelector('#ua_center')?.value || '').trim();
      const password = (addForm.querySelector('#ua_password')?.value || '');
      const passwordConfirm = (addForm.querySelector('#ua_password_confirm')?.value || '');

      if(!name || !email || !role || !center) return;
      if(password && passwordConfirm && password !== passwordConfirm) return;

      const id = normalizeIdFromEmail(email) || (Date.now() + Math.random()).toString(36);
      const users = loadUsers() || [];
      const exists = users.some(u => u.id === id);
      const newUser = { id, name, email, role, center, status: 'نشط', password: password || undefined };
      if(exists){
        upsertUser(newUser, id);
      }else{
        users.push(newUser);
        saveUsers(users);
        renderUsers(users);
        applyFilters();
      }

      closeOverlay(addOverlay);
    });
  }

  if(addCancelBtn){
    addCancelBtn.addEventListener('click', function(){
      closeOverlay(addOverlay);
    });
  }

  tbody.addEventListener('click', function(e){
    const editBtn = e.target.closest('.js-user-edit');
    const delBtn = e.target.closest('.js-user-delete');

    if(editBtn){
      const id = editBtn.getAttribute('data-user-id');
      const u = getUserById(id);
      if(!u || !editForm) return;
      editingUserId = id;
      editForm.querySelector('#edit-name').value = u.name || '';
      editForm.querySelector('#edit-email').value = u.email || '';
      editForm.querySelector('#edit-role').value = u.role || editForm.querySelector('#edit-role').value;
      editForm.querySelector('#edit-center').value = u.center || editForm.querySelector('#edit-center').value;
      openOverlay(editOverlay);
    }

    if(delBtn){
      const id = delBtn.getAttribute('data-user-id');
      deletingUserId = id;
      openOverlay(deleteOverlay);
    }
  });

  if(editUpdateBtn){
    editUpdateBtn.addEventListener('click', function(){
      if(!editForm || !editingUserId) return;
      const name = (editForm.querySelector('#edit-name')?.value || '').trim();
      const email = (editForm.querySelector('#edit-email')?.value || '').trim();
      const role = (editForm.querySelector('#edit-role')?.value || '').trim();
      const center = (editForm.querySelector('#edit-center')?.value || '').trim();

      if(!name || !email || !role || !center) return;

      const oldUser = getUserById(editingUserId);
      const newId = normalizeIdFromEmail(email) || editingUserId;
      const updated = {
        id: newId,
        name,
        email,
        role,
        center,
        status: oldUser?.status || 'نشط',
        password: oldUser?.password
      };

      if(newId !== editingUserId){
        deleteUser(editingUserId);
        upsertUser(updated, newId);
      }else{
        upsertUser(updated, editingUserId);
      }

      closeOverlay(editOverlay);
      editingUserId = null;
    });
  }

  if(editCancelBtn){
    editCancelBtn.addEventListener('click', function(){
      closeOverlay(editOverlay);
      editingUserId = null;
    });
  }

  if(deleteConfirmBtn){
    deleteConfirmBtn.addEventListener('click', function(){
      if(!deletingUserId) return;
      deleteUser(deletingUserId);
      closeOverlay(deleteOverlay);
      deletingUserId = null;
    });
  }

  if(deleteCancelBtn){
    deleteCancelBtn.addEventListener('click', function(){
      closeOverlay(deleteOverlay);
      deletingUserId = null;
    });
  }

  if(openChangePassBtn){
    openChangePassBtn.addEventListener('click', function(e){
      e.preventDefault();
      if(!editingUserId) return;
      changingPassUserId = editingUserId;
      if(changePassForm) changePassForm.reset();
      closeOverlay(editOverlay);
      openOverlay(changePassOverlay);
    });
  }

  if(changePassCancelBtn){
    changePassCancelBtn.addEventListener('click', function(){
      closeOverlay(changePassOverlay);
      if(editingUserId){
        openOverlay(editOverlay);
      }
    });
  }

  if(changePassForm){
    changePassForm.addEventListener('submit', function(e){
      e.preventDefault();
      if(!changingPassUserId) return;
      const p1 = (changePassForm.querySelector('#newPassword')?.value || '');
      const p2 = (changePassForm.querySelector('#confirmPassword')?.value || '');
      if(!p1 || p1.length < 8) return;
      if(p1 !== p2) return;
      const u = getUserById(changingPassUserId);
      if(!u) return;
      upsertUser({ ...u, password: p1 }, changingPassUserId);
      closeOverlay(changePassOverlay);
      openOverlay(editOverlay);
    });
  }

  [addOverlay, editOverlay, deleteOverlay, changePassOverlay].forEach(function(ov){
    if(!ov) return;
    ov.addEventListener('click', function(e){
      if(e.target === ov) closeOverlay(ov);
    });
  });

  const searchInput = document.querySelector('.users-search');
  if(searchInput){
    const runSearch = () => {
      state.search = searchInput.value || '';
      applyFilters();
    };

    searchInput.addEventListener('input', runSearch);

    searchInput.addEventListener('keydown', function(e){
      if((e.key || e.code) === 'Enter'){
        e.preventDefault();
        runSearch();
      }
    });

    document.addEventListener('keydown', function(e){
      if(document.activeElement === searchInput && (e.key || e.code) === 'Enter'){
        e.preventDefault();
        runSearch();
      }
    });
  }


  document.addEventListener('click', function(){ closeAllMenus(); });
  document.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeAllMenus(); });
  const users = seedUsersFromDomIfEmpty();
  renderUsers(users);
  applyFilters();
})();
