(function(){
    'use strict';

    function ready(fn){ if(document.readyState === 'loading'){ document.addEventListener('DOMContentLoaded', fn); return; } fn(); }
    function qsa(sel, root){ return Array.prototype.slice.call((root || document).querySelectorAll(sel)); }
    function qs(sel, root){ return (root || document).querySelector(sel); }

    function resultBox(form){
        var selector = form.getAttribute('data-result') || '';
        return selector ? document.querySelector(selector) : null;
    }

    function showResult(box, message, ok){
        if(!box){ return; }
        box.textContent = message || '';
        box.classList.remove('is-success', 'is-error');
        if(message){ box.classList.add(ok ? 'is-success' : 'is-error'); }
    }

    function normalize(payload){
        if(!payload){ return {ok:false, message:'Invalid server response.'}; }
        var status = String(payload.status || '').toLowerCase();
        var ok = payload.success === true || status === 'success' || status === 'successful';
        return {ok:ok, message:payload.message || payload.msg || (ok ? 'Saved.' : 'Unable to save.')};
    }

    function submitForm(form){
        var confirmText = '';
        var confirmButton = qs('[data-confirm]', form);
        if(confirmButton){ confirmText = confirmButton.getAttribute('data-confirm') || ''; }
        if(confirmText && !window.confirm(confirmText)){ return; }

        var box = resultBox(form);
        var button = qs('button[type="submit"]', form);
        var oldText = button ? button.textContent : '';
        if(button){ button.disabled = true; button.textContent = 'Saving...'; }
        showResult(box, 'Saving...', true);

        fetch('WICore/WIClass/WIAjax.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'X-Requested-With':'XMLHttpRequest'},
            body: new FormData(form)
        }).then(function(response){
            return response.json().catch(function(){ return null; });
        }).then(function(payload){
            var res = normalize(payload);
            showResult(box, res.message, res.ok);
            if(res.ok && form.classList.contains('wi-language-inline-delete')){
                var li = form.closest('li');
                if(li){ li.remove(); }
            }
        }).catch(function(){
            showResult(box, 'Network or server error.', false);
        }).finally(function(){
            if(button){ button.disabled = false; button.textContent = oldText; }
        });
    }

    function bootForms(){
        qsa('[data-wi-language-form]').forEach(function(form){
            form.addEventListener('submit', function(ev){
                ev.preventDefault();
                submitForm(form);
            });
        });
    }

    function bootFilters(){
        var langFilter = qs('[data-wi-language-filter]');
        if(langFilter){
            langFilter.addEventListener('input', function(){
                var needle = langFilter.value.toLowerCase().trim();
                qsa('[data-language-item]').forEach(function(item){
                    item.hidden = needle !== '' && String(item.getAttribute('data-search') || '').indexOf(needle) === -1;
                });
            });
        }

        var transFilter = qs('[data-wi-translation-filter]');
        if(transFilter){
            transFilter.addEventListener('input', function(){
                var needle = transFilter.value.toLowerCase().trim();
                qsa('[data-translation-item]').forEach(function(item){
                    item.hidden = needle !== '' && String(item.getAttribute('data-search') || '').indexOf(needle) === -1;
                });
            });
        }
    }

    function fill(prefix, data){
        Object.keys(data || {}).forEach(function(key){
            var field = qs('[data-' + prefix + '-field="' + key + '"]');
            if(field){ field.value = data[key] == null ? '' : data[key]; }
        });
    }

    function clear(prefix){
        qsa('[data-' + prefix + '-field]').forEach(function(field){ field.value = ''; });
    }

    function bootEditors(){
        qsa('[data-edit-language]').forEach(function(button){
            button.addEventListener('click', function(){
                try{ fill('lang', JSON.parse(button.getAttribute('data-edit-language') || '{}')); }catch(e){}
                var form = qs('.wi-language-edit-form');
                if(form){ form.scrollIntoView({behavior:'smooth', block:'center'}); }
            });
        });

        var clearLang = qs('[data-wi-language-clear]');
        if(clearLang){ clearLang.addEventListener('click', function(){ clear('lang'); }); }

        qsa('[data-edit-translation]').forEach(function(button){
            button.addEventListener('click', function(){
                try{ fill('trans', JSON.parse(button.getAttribute('data-edit-translation') || '{}')); }catch(e){}
                var form = qs('.wi-translation-manager .wi-language-edit-form');
                if(form){ form.scrollIntoView({behavior:'smooth', block:'center'}); }
            });
        });

        var clearTrans = qs('[data-wi-translation-clear]');
        if(clearTrans){ clearTrans.addEventListener('click', function(){ clear('trans'); }); }
    }

    ready(function(){
        bootForms();
        bootFilters();
        bootEditors();
    });
})();
