
(function(){
  'use strict';

  function qs(sel, root){return (root || document).querySelector(sel);}
  function qsa(sel, root){return Array.prototype.slice.call((root || document).querySelectorAll(sel));}
  function esc(value){
    return String(value == null ? '' : value).replace(/[&<>"']/g,function(ch){
      return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[ch];
    });
  }

  function boot(root){
    var state = {type:'theme', page:1, totalPages:1, perPage:10, pageFilter:'all', search:''};
    var csrf = root.getAttribute('data-csrf-token') || '';

    var nodes = {
      tabs:qsa('[data-style-tab]', root),
      list:qs('[data-style-list]', root),
      empty:qs('[data-style-empty]', root),
      result:qs('[data-style-result]', root),
      pageFilter:qs('[data-style-page-filter]', root),
      search:qs('[data-style-search]', root),
      perPage:qs('[data-style-per-page]', root),
      count:qs('[data-style-count]', root),
      title:qs('[data-style-list-title]', root),
      kicker:qs('[data-style-list-kicker]', root),
      prev:qs('[data-style-prev]', root),
      next:qs('[data-style-next]', root),
      summary:qs('[data-style-page-summary]', root),
      newBtn:qs('[data-style-new]', root),
      editor:qs('[data-style-editor]', root),
      form:qs('[data-style-form]', root),
      editorTitle:qs('[data-style-editor-title]', root)
    };

    function setResult(message, ok){
      if(!nodes.result){return;}
      nodes.result.textContent = message || '';
      nodes.result.classList.toggle('is-ok', !!ok);
      nodes.result.classList.toggle('is-error', ok === false);
    }

    function updateCsrf(payload){
      var token = payload && payload.data && payload.data.csrf_token ? payload.data.csrf_token : '';
      if(token){
        csrf = token;
        root.setAttribute('data-csrf-token', token);
      }
    }

    function post(action, extra){
      var fd = new FormData();
      fd.append('action', action);
      fd.append('csrf_token', csrf);
      Object.keys(extra || {}).forEach(function(key){
        var value = extra[key];
        if(value !== undefined && value !== null){fd.append(key, value);}
      });
      return fetch('WICore/WIClass/WIAjax.php', {
        method:'POST',
        credentials:'same-origin',
        headers:{'X-Requested-With':'XMLHttpRequest'},
        body:fd
      }).then(function(res){
        return res.json().catch(function(){return {status:'error',message:'Invalid JSON response.'};});
      }).then(function(payload){
        updateCsrf(payload);
        return payload;
      });
    }

    function labelForType(type){
      return type === 'theme' ? 'Theme' : type === 'css' ? 'CSS' : type === 'js' ? 'JS' : 'Meta';
    }

    function setTab(type){
      state.type = type;
      state.page = 1;
      nodes.tabs.forEach(function(btn){
        var active = btn.getAttribute('data-style-tab') === type;
        btn.classList.toggle('is-active', active);
        btn.setAttribute('aria-selected', active ? 'true' : 'false');
      });
      if(nodes.pageFilter){nodes.pageFilter.disabled = type === 'theme';}
      closeEditor();
      load();
    }

    function load(){
      setResult('Loading ' + labelForType(state.type) + ' records...', true);
      post('wicms_style_assets_list', {
        type:state.type,
        page_no:state.page,
        per_page:state.perPage,
        page_filter:state.pageFilter,
        search:state.search
      }).then(function(payload){
        if(!payload || payload.status !== 'success'){
          setResult(payload && payload.message ? payload.message : 'Unable to load records.', false);
          return;
        }
        render(payload.data || {});
        setResult(payload.message || 'Records loaded.', true);
      }).catch(function(){setResult('Network or server error while loading records.', false);});
    }

    function render(data){
      var records = data.records || [];
      var pag = data.pagination || {page:1,total_pages:1,total:0};
      state.page = Number(pag.page || 1);
      state.totalPages = Number(pag.total_pages || 1);
      var label = labelForType(state.type);
      if(nodes.kicker){nodes.kicker.textContent = label + ' Records';}
      if(nodes.title){nodes.title.textContent = 'Showing ' + label + ' records';}
      if(nodes.count){nodes.count.textContent = Number(pag.total || 0) + ' records';}
      if(nodes.summary){nodes.summary.textContent = 'Page ' + state.page + ' of ' + state.totalPages;}
      if(nodes.prev){nodes.prev.disabled = state.page <= 1;}
      if(nodes.next){nodes.next.disabled = state.page >= state.totalPages;}
      if(nodes.list){nodes.list.innerHTML = records.map(renderItem).join('');}
      if(nodes.empty){nodes.empty.hidden = records.length > 0;}
    }

    function renderItem(item){
      if(state.type === 'theme'){
        return '<li class="wi-style-item" data-style-id="'+esc(item.id)+'">'+
          '<div class="wi-style-item-main"><div class="wi-style-item-title"><span>'+esc(item.theme)+'</span>'+
          (Number(item.in_use) === 1 ? '<span class="wi-style-badge is-active">Active</span>' : '<span class="wi-style-badge">Theme</span>')+
          '</div><div class="wi-style-item-detail">'+esc(item.destination)+'</div></div>'+
          '<div class="wi-style-item-actions">'+
          (Number(item.in_use) === 1 ? '' : '<button type="button" class="wi-style-mini success" data-style-activate>Activate</button>')+
          '<button type="button" class="wi-style-mini" data-style-edit>Edit</button>'+
          '<button type="button" class="wi-style-mini danger" data-style-delete>Delete</button>'+
          '</div></li>';
      }
      if(state.type === 'meta'){
        return '<li class="wi-style-item" data-style-id="'+esc(item.id)+'">'+
          '<div class="wi-style-item-main"><div class="wi-style-item-title"><span>'+esc(item.name)+'</span><span class="wi-style-badge">'+esc(item.page)+'</span></div>'+
          '<div class="wi-style-item-detail">'+esc(item.content)+'</div>'+
          (item.author ? '<div class="wi-style-item-detail">Author: '+esc(item.author)+'</div>' : '')+'</div>'+
          '<div class="wi-style-item-actions"><button type="button" class="wi-style-mini" data-style-edit>Edit</button><button type="button" class="wi-style-mini danger" data-style-delete>Delete</button></div></li>';
      }
      var path = state.type === 'css' ? item.href : item.src;
      var extra = state.type === 'css' ? '<span class="wi-style-badge">'+esc(item.rel || 'stylesheet')+'</span>' : '<span class="wi-style-badge">Script</span>';
      return '<li class="wi-style-item" data-style-id="'+esc(item.id)+'">'+
        '<div class="wi-style-item-main"><div class="wi-style-item-title"><span>'+esc(path)+'</span><span class="wi-style-badge">'+esc(item.page)+'</span>'+extra+'</div>'+
        '<div class="wi-style-item-detail">Loads from the active theme destination.</div></div>'+
        '<div class="wi-style-item-actions"><button type="button" class="wi-style-mini" data-style-edit>Edit</button><button type="button" class="wi-style-mini danger" data-style-delete>Delete</button></div></li>';
    }

    function openNew(){
      resetForm();
      showEditor(labelForType(state.type) === 'Theme' ? 'Add Theme' : 'Add ' + labelForType(state.type));
    }

    function openEdit(id){
      post('wicms_style_asset_get', {type:state.type, id:id}).then(function(payload){
        if(!payload || payload.status !== 'success'){
          setResult(payload && payload.message ? payload.message : 'Unable to load record.', false);
          return;
        }
        fillForm(payload.data.record || {});
        showEditor('Edit ' + labelForType(state.type));
      }).catch(function(){setResult('Network or server error while loading record.', false);});
    }

    function resetForm(){
      if(!nodes.form){return;}
      nodes.form.reset();
      qs('[data-style-id]', nodes.form).value = '0';
      qs('[data-style-type]', nodes.form).value = state.type;
      var pageInput = qs('[data-style-page-input]', nodes.form);
      if(pageInput && state.pageFilter !== 'all'){pageInput.value = state.pageFilter;}
      setFieldVisibility();
    }

    function fillForm(record){
      resetForm();
      qs('[data-style-id]', nodes.form).value = record.id || 0;
      qs('[data-style-type]', nodes.form).value = state.type;
      function set(name, value){var field = nodes.form.elements[name]; if(field){field.value = value == null ? '' : value;}}
      if(state.type === 'theme'){
        set('theme', record.theme || '');
        set('destination', record.destination || '');
        if(nodes.form.elements.in_use){nodes.form.elements.in_use.checked = Number(record.in_use) === 1;}
      }else if(state.type === 'css'){
        set('asset_page', record.page || 'global');
        set('href', record.href || '');
        set('rel', record.rel || 'stylesheet');
      }else if(state.type === 'js'){
        set('asset_page', record.page || 'global');
        set('src', record.src || '');
      }else{
        set('asset_page', record.page || 'global');
        set('name', record.name || '');
        set('content', record.content || '');
        set('author', record.author || 'WICMS');
      }
      setFieldVisibility();
    }

    function showEditor(title){
      if(nodes.editorTitle){nodes.editorTitle.textContent = title;}
      if(nodes.editor){nodes.editor.hidden = false; nodes.editor.scrollIntoView({behavior:'smooth',block:'nearest'});}
    }

    function closeEditor(){if(nodes.editor){nodes.editor.hidden = true;}}

    function setFieldVisibility(){
      var type = state.type;
      var show = {
        theme:type==='theme', destination:type==='theme', inUse:type==='theme', page:type!=='theme', href:type==='css', rel:type==='css', src:type==='js', metaName:type==='meta', author:type==='meta', content:type==='meta'
      };
      toggle('[data-field-theme]', show.theme);
      toggle('[data-field-destination]', show.destination);
      toggle('[data-field-in-use]', show.inUse);
      toggle('[data-field-page]', show.page);
      toggle('[data-field-href]', show.href);
      toggle('[data-field-rel]', show.rel);
      toggle('[data-field-src]', show.src);
      toggle('[data-field-meta-name]', show.metaName);
      toggle('[data-field-author]', show.author);
      toggle('[data-field-content]', show.content);
    }

    function toggle(sel, visible){var el = qs(sel, nodes.form); if(el){el.hidden = !visible;}}

    function save(ev){
      ev.preventDefault();
      var fd = new FormData(nodes.form);
      var data = {};
      fd.forEach(function(v,k){data[k]=v;});
      data.type = state.type;
      if(state.type === 'theme'){
        data.in_use = nodes.form.elements.in_use && nodes.form.elements.in_use.checked ? '1' : '0';
      }
      post('wicms_style_asset_save', data).then(function(payload){
        if(!payload || payload.status !== 'success'){
          setResult(payload && payload.message ? payload.message : 'Unable to save record.', false);
          return;
        }
        setResult(payload.message || 'Saved.', true);
        closeEditor();
        load();
      }).catch(function(){setResult('Network or server error while saving.', false);});
    }

    function del(id){
      if(!window.confirm('Delete this ' + labelForType(state.type) + ' record?')){return;}
      post('wicms_style_asset_delete', {type:state.type, id:id}).then(function(payload){
        if(!payload || payload.status !== 'success'){
          setResult(payload && payload.message ? payload.message : 'Unable to delete record.', false);
          return;
        }
        setResult(payload.message || 'Deleted.', true);
        load();
      }).catch(function(){setResult('Network or server error while deleting.', false);});
    }

    function activate(id){
      post('wicms_style_theme_activate', {id:id}).then(function(payload){
        if(!payload || payload.status !== 'success'){
          setResult(payload && payload.message ? payload.message : 'Unable to activate theme.', false);
          return;
        }
        setResult(payload.message || 'Theme activated.', true);
        load();
      }).catch(function(){setResult('Network or server error while activating theme.', false);});
    }

    nodes.tabs.forEach(function(btn){btn.addEventListener('click', function(){setTab(btn.getAttribute('data-style-tab') || 'theme');});});
    if(nodes.pageFilter){nodes.pageFilter.addEventListener('change', function(){state.pageFilter = this.value || 'all'; state.page = 1; load();});}
    if(nodes.perPage){nodes.perPage.addEventListener('change', function(){state.perPage = Number(this.value || 10); state.page = 1; load();});}
    if(nodes.search){
      var timer = null;
      nodes.search.addEventListener('input', function(){
        clearTimeout(timer);
        var value = this.value || '';
        timer = setTimeout(function(){state.search = value; state.page = 1; load();}, 250);
      });
    }
    if(nodes.prev){nodes.prev.addEventListener('click', function(){if(state.page > 1){state.page -= 1; load();}});}
    if(nodes.next){nodes.next.addEventListener('click', function(){if(state.page < state.totalPages){state.page += 1; load();}});}
    if(nodes.newBtn){nodes.newBtn.addEventListener('click', openNew);}
    qsa('[data-style-cancel]', root).forEach(function(btn){btn.addEventListener('click', closeEditor);});
    if(nodes.form){nodes.form.addEventListener('submit', save);}
    if(nodes.list){nodes.list.addEventListener('click', function(ev){
      var target = ev.target;
      if(!(target instanceof Element)){return;}
      var item = target.closest('[data-style-id]');
      if(!item){return;}
      var id = item.getAttribute('data-style-id');
      if(target.closest('[data-style-edit]')){openEdit(id);}
      if(target.closest('[data-style-delete]')){del(id);}
      if(target.closest('[data-style-activate]')){activate(id);}
    });}

    setTab('theme');
  }

  document.addEventListener('DOMContentLoaded', function(){
    qsa('[data-wi-style-manager]').forEach(boot);
  });
})();
