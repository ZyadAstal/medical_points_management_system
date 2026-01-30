(function(){
  'use strict';
  const $=(s,r=document)=>r.querySelector(s);
  const $$=(s,r=document)=>Array.from(r.querySelectorAll(s));

  const addModal=$('#mcAddModal');
  const editModal=$('#mcEditModal');
  const addOpen=$('#mcAddCenterBtn');
  const rowsWrap=$('#mcTableRows') || $('#mcTableBody');

  const addCancel=$('#mcAddCancelBtn');
  const editCancel=$('#mcEditCancelBtn');

  let activeRow=null;

  function openModal(m){
    if(!m) return;
    m.style.display='flex';
    document.body.style.overflow='hidden';
  }
  function closeModal(m){
    if(!m) return;
    m.style.display='none';
    document.body.style.overflow='';
  }

  if(addOpen && addModal){
    addOpen.addEventListener('click',function(e){
      e.preventDefault();
      openModal(addModal);
    });
  }

  $$('[data-close]').forEach(btn=>{
    btn.addEventListener('click',function(e){
      e.preventDefault();
      const id=btn.getAttribute('data-close');
      const m=document.getElementById(id);
      closeModal(m);
    });
  });

  [addModal, editModal].forEach(m=>{
    if(!m) return;
    m.addEventListener('click',function(e){
      if(e.target === m) closeModal(m);
    });
  });

  if(rowsWrap && editModal){
    rowsWrap.addEventListener('click',function(e){
      const btn=e.target.closest('.mc-edit-action');
      if(!btn) return;
      e.preventDefault();
      const row=btn.closest('.mc-row');
      if(!row) return;
      activeRow=row;

      const tds=$$('.mc-td',row);
      const name=tds[0]?.textContent?.trim() || '';
      const users=tds[1]?.textContent?.trim() || '';
      const location=tds[2]?.textContent?.trim() || '';
      const phone=tds[3]?.textContent?.trim() || '';

      const iName=$('#mcEditName');
      const iLoc=$('#mcEditLocation');
      const iPhone=$('#mcEditPhone');

      if(iName) iName.value=name;
      if(iLoc) iLoc.value=location;
      if(iPhone) iPhone.value=phone;

      editModal.dataset.users = users;

      openModal(editModal);
    });
  }

  const editUpdateBtn=$('#mcEditUpdateBtn') || $('#mcEditUpdate');
  if(editUpdateBtn){
    editUpdateBtn.addEventListener('click',function(e){
      e.preventDefault();
      if(!activeRow) return;
      const iName=$('#mcEditName');
      const iLoc=$('#mcEditLocation');
      const iPhone=$('#mcEditPhone');
      const tds=$$('.mc-td',activeRow);
      if(tds[0] && iName) tds[0].textContent=iName.value;
      if(tds[2] && iLoc) tds[2].textContent=iLoc.value;
      if(tds[3] && iPhone) tds[3].textContent=iPhone.value;
      closeModal(editModal);
    });
  }

  const addSaveBtn=$('#mcAddSave');
  if(addSaveBtn && rowsWrap){
    addSaveBtn.addEventListener('click',function(e){
      e.preventDefault();
      const name=$('#mcAddName')?.value?.trim() || '';
      const location=$('#mcAddLocation')?.value?.trim() || '';
      const phone=$('#mcAddPhone')?.value?.trim() || '';
      if(!name && !location && !phone) return;

      const newRow=document.createElement('div');
      newRow.className='mc-row';
      newRow.innerHTML =
        '<div class="mc-td mc-name">'+escapeHtml(name)+'</div>'+
        '<div class="mc-td mc-users">0</div>'+
        '<div class="mc-td mc-location">'+escapeHtml(location)+'</div>'+
        '<div class="mc-td mc-phone">'+escapeHtml(phone)+'</div>'+
        '<div class="mc-td mc-spend">0</div>'+
        '<div class="mc-td mc-actions">'+
          '<button class="mc-edit-action" type="button" aria-label="Edit">'+
            '<img src="../assets/icons/edit.svg" alt="Edit" />'+
          '</button>'+
        '</div>';

      rowsWrap.appendChild(newRow);

      const n=$('#mcAddName');
      const l=$('#mcAddLocation');
      const p=$('#mcAddPhone');
      if(n) n.value='';
      if(l) l.value='';
      if(p) p.value='';

      closeModal(addModal);
    });
  }


  if(addCancel){
    addCancel.addEventListener('click', function(e){
      e.preventDefault();
      closeModal(addModal);
    });
  }
  if(editCancel){
    editCancel.addEventListener('click', function(e){
      e.preventDefault();
      closeModal(editModal);
    });
  }

  function escapeHtml(str){
    return String(str)
      .replace(/&/g,'&amp;')
      .replace(/</g,'&lt;')
      .replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;')
      .replace(/'/g,'&#39;');
  }
})();
