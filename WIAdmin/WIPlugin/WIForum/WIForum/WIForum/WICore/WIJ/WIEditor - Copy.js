$(document).ready(function () {

     /*
 * -------------------------------------------------------
 *  BASIC FUNCTIONS 
 * -------------------------------------------------------
 */
var myTextArea = $('editor-area');
var myEditor = WIEditor.Editor(myTextArea);
WIEditor.buttons();

    $("#toolbox a").click(function(elem){
        console.log('clicked');
        e.preventDefault();
        WIEditor.click(elem)
    })
});


var WIEditor = {};

WIEditor.Editor = function(source){
        var base = this,
        history = [],
        undo = 0,
        redo = null;

    base.area = typeof source != "undefined" ? source : $('textarea')[0];
  
    history[undo] = {
        value: base.area.value,
        selectionStart: 0,
        selectionEnd: 0
    };

    undo++;

        /**
     * Collect data from selected text inside a textarea
     *
     * <code>
     *   var editor = new Editor(elem);
     *   elem.onmouseup = function() {
     *       alert(editor.selection().start);
     *       alert(editor.selection().end);
     *       alert(editor.selection().value);
     *   };
     * </code>
     *
     */

    base.selection = function() {

        var start = base.area.selectionStart,
            end = base.area.selectionEnd,
            value = base.area.value.substring(start, end),
            before = base.area.value.substring(0, start),
            after = base.area.value.substring(end),
            data = {
                start: start,
                end: end,
                value: value,
                before: before,
                after: after
            };
        // console.log(data);
        return data;
    };


    /**
     * Select portion of text inside a textarea
     *
     * <code>
     *   var editor = new Editor(elem);
     *   editor.select(7, 11);
     * </code>
     *
     */
    base.select = function(start, end, callback) {
        base.area.focus();
        base.area.setSelectionRange(start, end);
        if (typeof callback == "function") callback();
    };


    /**
     * Replace portion of selected text inside a textarea with something
     *
     * <code>
     *   var editor = new Editor(elem);
     *   editor.replace(/foo/, "bar");
     * </code>
     *
     */

    base.replace = function(from, to, callback) {
        var sel = base.selection(),
            start = sel.start,
            end = sel.end,
            selections = sel.value.replace(from, to);
        base.area.value = sel.before + selections + sel.after;
        base.select(start, start + selections.length);
        if (typeof callback == "function") {
            callback();
        } else {
            base.updateHistory({
                value: base.area.value,
                selectionStart: start,
                selectionEnd: start + selections.length
            });
        }
    };


    /**
     * Replace selected text inside a textarea with something
     *
     * <code>
     *   var editor = new Editor(elem);
     *   editor.insert('foo');
     * </code>
     *
     */

    base.insert = function(insertion, callback) {

        var sel = base.selection(),
            start = sel.start,
            end = sel.end;

        base.area.value = sel.before + insertion + sel.after;

        base.select(start + insertion.length, start + insertion.length);

        if (typeof callback == "function") {
            callback();
        } else {
            base.updateHistory({
                value: base.area.value,
                selectionStart: start + insertion.length,
                selectionEnd: start + insertion.length
            });
        }

    };


    /**
     * Wrap selected text inside a textarea with something
     *
     * <code>
     *   var editor = new Editor(elem);
     *   editor.wrap('<strong>', '</strong>');
     * </code>
     *
     */

    base.wrap = function(open, close, callback) {

        var sel = base.selection(),
            selections = sel.value,
            before = sel.before,
            after = sel.after;

        base.area.value = before + open + selections + close + after;

        base.select(before.length + open.length, before.length + open.length + selections.length);

        if (typeof callback == "function") {
            callback();
        } else {
            base.updateHistory({
                value: base.area.value,
                selectionStart: before.length + open.length,
                selectionEnd: before.length + open.length + selections.length
            });
        }

    };


    /**
     * Indent selected text inside a textarea with something
     *
     * <code>
     *   var editor = new Editor(elem);
     *   editor.indent('\t');
     * </code>
     *
     */

    base.indent = function(chars, callback) {

        var sel = base.selection();

        if (sel.value.length > 0) { // Multi line

            base.replace(/(^|\n)([^\n])/gm, '$1' + chars + '$2', callback);

        } else { // Single line

            base.area.value = sel.before + chars + sel.value + sel.after;

            base.select(sel.start + chars.length, sel.start + chars.length);

            if (typeof callback == "function") {
                callback();
            } else {
                base.updateHistory({
                    value: base.area.value,
                    selectionStart: sel.start + chars.length,
                    selectionEnd: sel.start + chars.length
                });
            }

        }

    };


    /**
     * Outdent selected text inside a textarea from something
     *
     * <code>
     *   var editor = new Editor(elem);
     *   editor.outdent('\t');
     * </code>
     *
     */

    base.outdent = function(chars, callback) {

        var sel = base.selection();

        if (sel.value.length > 0) { // Multi line

            base.replace(new RegExp('(^|\n)' + chars, 'gm'), '$1', callback);

        } else { // Single line

            var before = sel.before.replace(new RegExp(chars + '$'), "");

            base.area.value = before + sel.value + sel.after;

            base.select(before.length, before.length);

            if (typeof callback == "function") {
                callback();
            } else {
                base.updateHistory({
                    value: base.area.value,
                    selectionStart: before.length,
                    selectionEnd: before.length
                });
            }

        }

    };


    /**
     * Call available history data
     *
     * <code>
     *   var editor = new Editor(elem);
     *   alert(editor.callHistory(2).value);
     *   alert(editor.callHistory(2).selectionStart);
     *   alert(editor.callHistory(2).selectionEnd);
     * </code>
     *
     */

    base.callHistory = function(index) {

        return (typeof index == "number") ? history[index] : history;

    };


    /**
     * Update history data
     *
     * <code>
     *   var editor = new Editor(elem);
     *   editor.area.onkeydown = function() {
     *       editor.updateHistory();
     *   };
     * </code>
     *
     */

    base.updateHistory = function(data, index) {

        var value = (typeof data != "undefined") ? data : {
            value: base.area.value,
            selectionStart: base.selection().start,
            selectionEnd: base.selection().end
        };

        history[typeof index == "number" ? index : undo] = value;

        undo++;

    };


    /**
     * Undo from previous action or previous Redo
     *
     * <code>
     *   var editor = new Editor(elem);
     *   editor.undo();
     * </code>
     *
     */

    base.undo = function(callback) {
        var data;
        if (history.length > 1) {

            if (undo > 1) {
                undo--;
            } else {
                undo = 1;
            }
            data = base.callHistory(undo - 1);
            redo = undo <= 0 ? undo - 1 : undo;
        } else {
            return;
        }
        base.area.value = data.value;
        base.select(data.selectionStart, data.selectionEnd);
        if (typeof callback == "function") callback();
    };


    /**
     * Redo from previous Undo
     *
     * <code>
     *   var editor = new Editor(elem);
     *   editor.redo();
     * </code>
     *
     */

    base.redo = function(callback) {
        var data;
        if (redo !== null) {
            data = base.callHistory(redo);
            if (redo < history.length - 1) {
                redo++;
            } else {
                redo = history.length - 1;
            }
            undo = redo >= history.length - 1 ? redo + 1 : redo;
        } else {
            return;
        }
        base.area.value = data.value;
        base.select(data.selectionStart, data.selectionEnd);
        // console.log(redo);
        if (typeof callback == "function") callback();
    };
};

WIEditor.capitalize = function(lower) {
        return (lower ? this.toLowerCase() : this).replace(/(?:^|\s)\S/g, function(a) {
            return a.toUpperCase();
        });
};

WIEditor.Controls = function(){
        var controls = {
        'bold': function() {
            myEditor.wrap('<strong>', '</strong>');
        },
        'italic': function() {
            myEditor.wrap('<em>', '</em>');
        },
        'code': function() {
            myEditor.wrap('<pre>', '</pre>');
        },
        'quote': function() {
            myEditor.wrap('<blockquote>', '</blockquote>');
        },
        'li': function() {
            myEditor.wrap('  <li>', '</li>');
        },
        'ul-list': function() {
            var sel = myEditor.selection();
            if (sel.value.length > 0) {
                myEditor.insert('<ul>\n  <li>' + sel.value.replace(/\n/g, '</li>\n  <li>') + '</li>\n</ul>');
            } else {
                var placeholder = '<ul>\n  <li>List Item</li>\n</ul>';
                myEditor.indent(placeholder, function() {
                    myEditor.select(sel.start + 11, sel.start + placeholder.length - 11);
                });
            }
        },
        'ol-list': function() {
            var sel = myEditor.selection();
            if (sel.value.length > 0) {
                myEditor.insert('<ol>\n  <li>' + sel.value.replace(/\n/g, '</li>\n  <li>') + '</li>\n</ol>');
            } else {
                var placeholder = '<ol>\n  <li>List Item</li>\n</ol>';
                myEditor.indent(placeholder, function() {
                    myEditor.select(sel.start + 11, sel.start + placeholder.length - 11);
                });
            }
        },
        'h1': function() {
            heading('h1');
        },
        'h2': function() {
            heading('h2');
        },
        'h3': function() {
            heading('h3');
        },
        'hr': function() {
            myEditor.insert('\n<hr>\n');
        },
        'p': function() {
            myEditor.wrap('<p>', '</p>\n');
        },
        'lorem': function() {
            myEditor.insert('<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim.</p>\n');
        },
        'undo': function() {
            myEditor.undo();
        },
        'redo': function() {
            myEditor.redo();
        },
        'select': function() {
            myEditor.select(0, myTextArea.value.length);
        },
        'return': function() {
            myEditor.insert('\n');
        },
        'link': function() {
            var sel = myEditor.selection(),
                title = null,
                url = null,
                placeholder = 'Your link text goes here...';
            fakePrompt('Link title:', 'Link title goes here...', false, function(r) {
                title = r;
                fakePrompt('URL:', 'http://', true, function(r) {
                    url = r;
                    myEditor.insert('\n<a href="' + r + '" title=" ' + title + '">' + title + '</a>\n');
                });
            });
        },
        'image': function() {
            fakePrompt('Image URL:', 'http://', true, function(r) {
                var altText = r.substring(r.lastIndexOf('/') + 1, r.lastIndexOf('.')).replace(/[\-\_\+]+/g, " ").capitalize();
                altText = altText.indexOf('/') < 0 ? decodeURIComponent(altText) : 'Image';
                myEditor.insert('\n<img src="' + r + '" alt=" ' + altText + '"/>\n');
            });
        }
    };

};

WIEditor.fakePrompt = function(label, value, isRequired, callback){

            var overlay = document.createElement('div');
        overlay.className = 'custom-modal-overlay';
        var modal = document.createElement('div');
        modal.className = 'custom-modal custom-modal-prompt';
        modal.innerHTML = '<div class="custom-modal-header">' + label + '</div><div class="custom-modal-content"></div><div class="custom-modal-action"></div>';
        var onSuccess = function(value) {
            overlay.parentNode.removeChild(overlay);
            modal.parentNode.removeChild(modal);
            if (typeof callback == "function") callback(value);
        };
        var input = document.createElement('input');
        input.type = 'text';
        input.value = value;
        input.onkeyup = function(e) {
            if (isRequired) {
                if (e.keyCode == 13 && this.value !== "" && this.value !== value) {
                    onSuccess(this.value);
                }
            } else {
                if (e.keyCode == 13) {
                    onSuccess(this.value == value ? "" : this.value);
                }
            }
        };
        var buttonOK = document.createElement('button');
        buttonOK.innerHTML = 'OK';
        buttonOK.onclick = function() {
            if (isRequired) {
                if (input.value !== "" && input.value !== value) {
                    onSuccess(input.value);
                }
            } else {
                onSuccess(input.value == value ? "" : input.value);
            }
        };
        var buttonCANCEL = document.createElement('button');
        buttonCANCEL.innerHTML = 'Cancel';
        buttonCANCEL.onclick = function() {
            overlay.parentNode.removeChild(overlay);
            modal.parentNode.removeChild(modal);
        };
        document.body.appendChild(overlay);
        document.body.appendChild(modal);
        modal.children[1].appendChild(input);
        modal.children[2].appendChild(buttonOK);
        modal.children[2].appendChild(buttonCANCEL);
        input.select();
}

WIEditor.click = function(elem){

            var hash = elem.hash.replace('#', "");
        if (WIEditor.Controls[hash]) {
            elem.onclick = function() {
                controls[hash]();
                return false;
            };
        }
}

WIEditor.heading = function(key){
            if (myEditor.selection().value.length > 0) {
            myEditor.wrap('<' + key + '>', '</' + key + '>');
        } else {
            var placeholder = '<' + key + '>Heading ' + key.substr(1) + '</' + key + '>';
            myEditor.insert(placeholder, function() {
                var s = myEditor.selection().start;
                myEditor.select(s - placeholder.length + 4, s - 5);
            });
        }
}

WIEditor.eye = function(){
    // the eye
var e = document.querySelector('#eye'),
    i =  document.querySelector('#editor-area'),
    o = document.querySelector('.result');

   o.innerHTML = i.value;
   o.classList.toggle('show');
   this.classList.toggle('active');

}

WIEditor.buttons = function(){
    var    myButton = $("#toolbar a");

            for (var i = 0, len = myButton.length; i < len; ++i) {if (window.CP.shouldStopExecution(1)){break;}
            WIEditor.click(myButton[i]);
            myButton[i].href = '#';
                }
}
