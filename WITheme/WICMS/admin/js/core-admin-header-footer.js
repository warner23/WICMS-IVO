
(function(){
  'use strict';

  function ready(fn){
    if(document.readyState === 'loading'){
      document.addEventListener('DOMContentLoaded', fn);
      return;
    }
    fn();
  }

  function qs(selector, parent){ return (parent || document).querySelector(selector); }
  function qsa(selector, parent){ return Array.prototype.slice.call((parent || document).querySelectorAll(selector)); }

  function setResult(selectorOrEl, message, ok){
    var el = typeof selectorOrEl === 'string' ? qs(selectorOrEl) : selectorOrEl;
    if(!el){ return; }
    el.textContent = message || '';
    el.classList.remove('is-success','is-error');
    if(message){ el.classList.add(ok ? 'is-success' : 'is-error'); }
  }

  function normalise(payload){
    if(!payload){ return {ok:false, message:'Invalid server response.', data:{}}; }
    var status = String(payload.status || '').toLowerCase();
    var ok = payload.success === true || status === 'success' || status === 'completed';
    return {ok:ok, message:payload.message || payload.msg || (ok ? 'Saved.' : 'Unable to save.'), data:payload.data || {}, raw:payload};
  }

  function applyTokens(tokens){
    if(!tokens){ return; }
    if(tokens.save){
      qsa('[data-wi-hf-save-token]').forEach(function(input){ input.value = tokens.save; });
    }
    if(tokens.upload){
      qsa('[data-wi-hf-upload-zone]').forEach(function(zone){ zone.setAttribute('data-csrf-token', tokens.upload); });
    }
  }

  function activeTab(key){
    var root = qs('[data-wi-header-footer-page]');
    if(!root){ return; }
    qsa('[data-wi-hf-tab]', root).forEach(function(btn){
      var isActive = btn.getAttribute('data-wi-hf-tab') === key;
      btn.classList.toggle('is-active', isActive);
      btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
    });
    qsa('[data-wi-hf-panel]', root).forEach(function(panel){
      panel.hidden = panel.getAttribute('data-wi-hf-panel') !== key;
    });
    try{ window.sessionStorage.setItem('wi_header_footer_active_tab', key); }catch(e){}
  }

  function bootTabs(){
    var tabs = qsa('[data-wi-hf-tab]');
    if(!tabs.length){ return; }
    tabs.forEach(function(btn){
      btn.addEventListener('click', function(){ activeTab(btn.getAttribute('data-wi-hf-tab') || 'header'); });
    });
    var saved = '';
    try{ saved = window.sessionStorage.getItem('wi_header_footer_active_tab') || ''; }catch(e){}
    var exists = saved && qs('[data-wi-hf-tab="' + saved + '"]');
    activeTab(exists ? saved : (tabs[0].getAttribute('data-wi-hf-tab') || 'header'));
  }

  function postForm(form){
    var btn = qs('button[type="submit"]', form);
    var resultSelector = form.getAttribute('data-result') || '';
    var oldText = btn ? btn.textContent : '';
    if(btn){ btn.disabled = true; btn.textContent = 'Saving...'; }
    setResult(resultSelector, 'Saving...', true);

    fetch('WICore/WIClass/WIAjax.php', {
      method: 'POST',
      credentials: 'same-origin',
      headers: {'X-Requested-With':'XMLHttpRequest'},
      body: new FormData(form)
    }).then(function(response){
      return response.json().catch(function(){ return null; });
    }).then(function(payload){
      var res = normalise(payload);
      applyTokens((res.data && res.data.csrf_tokens) || (payload && payload.csrf_tokens));
      setResult(resultSelector, res.message, res.ok);
    }).catch(function(){
      setResult(resultSelector, 'Network or server error while saving.', false);
    }).finally(function(){
      if(btn){ btn.disabled = false; btn.textContent = oldText; }
    });
  }

  function bootForms(){
    qsa('[data-wi-hf-form]').forEach(function(form){
      form.addEventListener('submit', function(event){
        event.preventDefault();
        postForm(form);
      });
      qsa('input, textarea', form).forEach(function(input){
        input.addEventListener('input', updateFooterPreview);
      });
    });
  }

  function uploadFile(zone, file){
    if(!file){ return; }
    var assetType = zone.getAttribute('data-asset-type') || '';
    var token = zone.getAttribute('data-csrf-token') || '';
    var resultId = assetType === 'favicon' ? '#wi-hf-favicon-result' : '#wi-hf-header-result';
    var form = new FormData();
    form.append('action', 'wicms_header_footer_upload');
    form.append('asset_type', assetType);
    form.append('csrf_token', token);
    form.append('file', file);

    zone.classList.add('is-uploading');
    setResult(resultId, 'Uploading...', true);

    fetch('WICore/WIClass/WIAjax.php', {
      method: 'POST',
      credentials: 'same-origin',
      headers: {'X-Requested-With':'XMLHttpRequest'},
      body: form
    }).then(function(response){
      return response.json().catch(function(){ return null; });
    }).then(function(payload){
      var res = normalise(payload);
      applyTokens((res.data && res.data.csrf_tokens) || (payload && payload.csrf_tokens));
      if(res.ok && res.data && res.data.url){
        updatePreview(assetType, res.data.url);
      }
      setResult(resultId, res.message, res.ok);
    }).catch(function(){
      setResult(resultId, 'Network or server error while uploading.', false);
    }).finally(function(){
      zone.classList.remove('is-uploading');
    });
  }

  function updatePreview(assetType, url){
    qsa('[data-preview-for="' + assetType + '"]').forEach(function(preview){
      preview.innerHTML = '';
      var img = document.createElement('img');
      img.src = url;
      img.alt = assetType === 'favicon' ? 'Current favicon' : 'Current header image';
      preview.appendChild(img);
    });
  }

  function bootUploads(){
    qsa('[data-wi-hf-upload-zone]').forEach(function(zone){
      var input = qs('[data-wi-hf-file]', zone);
      var choose = qs('[data-wi-hf-choose]', zone);
      function openPicker(event){
        if(event){ event.preventDefault(); event.stopPropagation(); }
        if(input){ input.click(); }
      }
      zone.addEventListener('click', function(event){
        if(event.target && event.target.matches('button')){ return; }
        openPicker(event);
      });
      if(choose){ choose.addEventListener('click', openPicker); }
      if(input){
        input.addEventListener('change', function(){
          uploadFile(zone, input.files && input.files[0]);
          input.value = '';
        });
      }
      zone.addEventListener('dragover', function(event){
        event.preventDefault();
        zone.classList.add('is-dragover');
      });
      zone.addEventListener('dragleave', function(){ zone.classList.remove('is-dragover'); });
      zone.addEventListener('drop', function(event){
        event.preventDefault();
        zone.classList.remove('is-dragover');
        var file = event.dataTransfer && event.dataTransfer.files ? event.dataTransfer.files[0] : null;
        uploadFile(zone, file);
      });
    });
  }

  function updateFooterPreview(){
    var name = qs('input[name="website_name"]');
    var content = qs('textarea[name="footer_content"]');
    var links = qs('textarea[name="footer_linking"]');
    var namePreview = qs('[data-wi-hf-preview-name]');
    var contentPreview = qs('[data-wi-hf-preview-content]');
    var linksPreview = qs('[data-wi-hf-preview-links]');
    if(namePreview && name){ namePreview.textContent = name.value || 'WICMS'; }
    if(contentPreview && content){ contentPreview.textContent = content.value || 'Core WICMS website footer.'; }
    if(linksPreview && links){ linksPreview.textContent = links.value || 'All rights reserved.'; }
  }

  ready(function(){
    bootTabs();
    bootForms();
    bootUploads();
    updateFooterPreview();
  });
})();
