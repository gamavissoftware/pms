<?php
// Finished goods code for the first Annexure-IV line item. Shown right after the Item description (Model) box
// on the new, edit and clone quotation forms. Expects $fg_code (existing value, '' for a new quotation).
// The code is generated automatically and cannot be edited. Rules live in libraries/Fg_code.php; the script below
// mirrors them so the code follows the form, and the server rebuilds it on every save.
$fg_code = isset($fg_code) ? $fg_code : '';
?>
<div class="form-group" style="margin-top:8px;">
    <label for="fg_code" style="margin-bottom:2px;">Finished Goods Code</label>
    <input type="text" class="form-control" name="fg_code" id="fg_code" maxlength="40" readonly placeholder="Generated automatically" value="<?php echo htmlspecialchars($fg_code, ENT_QUOTES, 'UTF-8'); ?>" style="font-weight:bold;">
    <small id="fg_code_note" style="color:#b94a48;"></small>
</div>
<script>
(function () {
    var MODEL = [1250, 1200, 1000, 800, 600, 300];

    function plain(html) {
        var d = document.createElement('div');
        d.innerHTML = html || '';
        return (d.textContent || '').replace(/ /g, ' ').trim();
    }
    // the supported model type (300, 600 ...) found in the text
    function modelType(text) {
        text = plain(text);
        for (var i = 0; i < MODEL.length; i++) {
            if (new RegExp('(^|[^0-9.])' + MODEL[i] + '([^0-9]|$)').test(text)) { return String(MODEL[i]); }
        }
        return '';
    }
    // first whole number in the text; a leading range such as 50-60 gives its highest end when rangeUpper is set
    function firstNumber(text, rangeUpper) {
        var m = plain(text).match(/(\d+)(?:\s*(?:-|\u2013|to)\s*(\d+))?/);
        if (!m) { return ''; }
        var n = rangeUpper && m[2] ? Math.max(parseInt(m[1], 10), parseInt(m[2], 10)) : parseInt(m[1], 10);
        return n > 0 ? String(n) : '';
    }
    function editorValue(id) {
        if (window.CKEDITOR && CKEDITOR.instances && CKEDITOR.instances[id]) { return CKEDITOR.instances[id].getData(); }
        var el = document.getElementById(id);
        return el ? el.value : '';
    }
    function liquidOrPowder() {
        var el = document.getElementById('producttobepacked');
        var v = el ? String(el.value).toLowerCase() : '';
        if (v === '1' || /liquid|paste/.test(v)) { return 'L'; }
        if (v === '2' || /powder|granul/.test(v)) { return 'P'; }
        return '';
    }
    // number of axis rows on the quotation
    function axisRows() {
        var rows = document.querySelectorAll('input[name^="noofaxisincount"], input[name^="editnoofaxisincount"]').length;
        return rows ? String(rows) : '';
    }
    function build() {
        var val = function (id) { var el = document.getElementById(id); return el ? el.value : ''; };
        var parts = {
            model: modelType(val('mach_model_no')),
            type: liquidOrPowder(),
            track: firstNumber(val('nooftracks'), false),
            axis: axisRows(),
            speed: firstNumber(editorValue('actualspeed'), true)
        };
        var code = 'SP', missing = [];
        Object.keys(parts).forEach(function (k) { if (parts[k]) { code += '_' + parts[k]; } else { missing.push(k); } });
        return { code: code, missing: missing };
    }
    function fill() {
        var input = document.getElementById('fg_code');
        if (!input) { return; }
        var r = build();
        input.value = r.code;
        document.getElementById('fg_code_note').textContent = r.missing.length ? 'Not yet readable from the form: ' + r.missing.join(', ') : '';
    }

    document.addEventListener('DOMContentLoaded', function () {
        ['mach_model_no', 'nooftracks', 'producttobepacked', 'actualspeed'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) { el.addEventListener('change', fill); el.addEventListener('keyup', fill); }
        });
        // axis rows are added and removed dynamically, so listen on the document
        document.addEventListener('change', function (e) {
            if (e.target && /axis/i.test(e.target.name || '')) { fill(); }
        });
        document.addEventListener('click', function (e) {
            if (e.target && /axis|remove|delete/i.test((e.target.id || '') + ' ' + (e.target.className || ''))) { setTimeout(fill, 300); }
        });
        if (window.CKEDITOR && CKEDITOR.instances && CKEDITOR.instances.actualspeed) {
            CKEDITOR.instances.actualspeed.on('change', fill);
        }
        // the saved code is rebuilt on the server as well; rebuild here on submit so the form shows the same value
        var form = document.getElementById('fg_code').form;
        if (form) { form.addEventListener('submit', fill, true); }
    });
})();
</script>
