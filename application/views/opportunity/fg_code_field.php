<?php
// Finished goods code for the first Annexure-IV line item. Shown right after the Item description (Model) box
// on the new, edit and clone quotation forms. Expects $fg_code (existing value, '' for a new quotation).
// Rules live in libraries/Fg_code.php; the script below mirrors them so the code fills in as the form is completed.
$fg_code = isset($fg_code) ? $fg_code : '';
?>
<div class="form-group" style="margin-top:8px;">
    <label for="fg_code" style="margin-bottom:2px;">Finished Goods Code</label>
    <div class="input-group">
        <input type="text" class="form-control" name="fg_code" id="fg_code" maxlength="40" placeholder="SP + Model + L/P + Track + Axis + Speed" value="<?php echo htmlspecialchars($fg_code, ENT_QUOTES, 'UTF-8'); ?>" style="font-weight:bold;text-transform:uppercase;">
        <span class="input-group-btn">
            <button type="button" class="btn btn-default" id="fg_code_regen" title="Rebuild from the model, product, tracks, axis and speed on this form">Regenerate</button>
        </span>
    </div>
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
    // first whole number in the text; a leading range such as 50-60 gives its upper end when rangeUpper is set
    function firstNumber(text, rangeUpper) {
        var m = plain(text).match(/(\d+)(?:\s*(?:-|\u2013|to)\s*(\d+))?/);
        if (!m) { return ''; }
        var n = parseInt(rangeUpper && m[2] ? m[2] : m[1], 10);
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
            speed: firstNumber(editorValue('designspeed'), true)
        };
        var code = 'SP', missing = [];
        Object.keys(parts).forEach(function (k) { if (parts[k]) { code += '_' + parts[k]; } else { missing.push(k); } });
        return { code: code, missing: missing };
    }
    function fill(force) {
        var input = document.getElementById('fg_code');
        if (!input || (!force && input.value !== '')) { return; }
        var r = build();
        input.value = r.code;
        document.getElementById('fg_code_note').textContent = r.missing.length ? 'Could not read from the form: ' + r.missing.join(', ') + ' - complete the code manually if needed.' : '';
    }
    var typedByHand = false;
    document.getElementById('fg_code').addEventListener('input', function () { typedByHand = true; });
    document.getElementById('fg_code_regen').addEventListener('click', function () { typedByHand = false; fill(true); });
    document.addEventListener('DOMContentLoaded', function () {
        // only a new quotation (or one with no code yet) is filled automatically, and only until the code is edited by hand;
        // a saved code is never overwritten except through the Regenerate button
        if (document.getElementById('fg_code').value === '') {
            var refresh = function () { if (!typedByHand) { fill(true); } };
            ['mach_model_no', 'nooftracks', 'producttobepacked'].forEach(function (id) {
                var el = document.getElementById(id);
                if (el) { el.addEventListener('change', refresh); el.addEventListener('keyup', refresh); }
            });
        }
    });
})();
</script>
