<?php
// Finished goods fields for the first Annexure-IV line item, shown right after the Item description (Model) box on the
// new, edit and clone quotation screens.
//   Finished Goods Code : SPFGMC + number, assigned by the system (read-only). A new or cloned quotation shows the number it
//                         will receive when saved; an edited quotation keeps its own.
//   Title 1             : blank on a new quotation until a field is chosen, then built automatically as the form is filled:
//                         SP-{model}{L|P}-{tracks}T-{axis}A-{speed} | {machine type}, e.g. SP-1200L-12T-5A-100 (read-only).
//   Title 2             : starts as a copy of Title 1 and follows it; the sales person can edit it, after which it is left alone.
// Expects $fg_code, $fg_title1, $fg_title2 (saved values, '' for a new quotation) and $fg_mode ('new' | 'edit' | 'clone').
// A new or cloned quotation starts with both titles blank.
$fg_code = isset($fg_code) ? $fg_code : '';
$fg_title1 = isset($fg_title1) ? $fg_title1 : '';
$fg_title2 = isset($fg_title2) ? $fg_title2 : '';
$fg_mode = isset($fg_mode) ? $fg_mode : 'new';
if ($fg_mode !== 'edit') {
    $fg_title1 = '';
    $fg_title2 = '';
}
$fg_e = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
?>
<div class="form-group" style="margin-top:8px;">
    <label for="fg_code" style="margin-bottom:2px;">Finished Goods Code</label>
    <input type="text" class="form-control" id="fg_code" readonly value="<?php echo $fg_e($fg_mode === 'clone' ? '' : $fg_code); ?>" style="font-weight:bold;">
    <small id="fg_code_note" style="color:#777;"></small>

    <label for="fg_title1" style="margin:8px 0 2px;">Title 1</label>
    <textarea class="form-control" name="fg_title1" id="fg_title1" rows="2" maxlength="255" readonly><?php echo $fg_e($fg_title1); ?></textarea>

    <label for="fg_title2" style="margin:8px 0 2px;">Title 2 <small>(you can edit this)</small></label>
    <textarea class="form-control" name="fg_title2" id="fg_title2" rows="2" maxlength="255"><?php echo $fg_e($fg_title2); ?></textarea>
</div>
<script>
(function () {
    var savedCode = <?php echo json_encode($fg_mode === 'clone' ? '' : $fg_code); ?>;

    function plain(html) {
        var d = document.createElement('div');
        d.innerHTML = html || '';
        return (d.textContent || '').replace(/ /g, ' ').replace(/\s+/g, ' ').trim();
    }
    // first whole number; a leading range such as 50-60 gives its highest end when highest is set
    function firstNumber(text, highest) {
        var m = plain(text).match(/(\d+)(?:\s*(?:-|–|to)\s*(\d+))?/);
        if (!m) { return ''; }
        var n = highest && m[2] ? Math.max(parseInt(m[1], 10), parseInt(m[2], 10)) : parseInt(m[1], 10);
        return n > 0 ? String(n) : '';
    }
    function val(id) { var el = document.getElementById(id); return el ? el.value : ''; }
    function editorValue(id) {
        if (window.CKEDITOR && CKEDITOR.instances && CKEDITOR.instances[id]) { return CKEDITOR.instances[id].getData(); }
        return val(id);
    }
    // number of axis rows: new rows (noofaxisinmachine[]) and saved rows (editnoofaxisinmachine<id>) that have an axis name
    function axisCount() {
        var count = 0;
        document.querySelectorAll('[name^="noofaxisinmachine["], [name^="editnoofaxisinmachine"]').forEach(function (nameEl) {
            if (plain(nameEl.value)) { count++; }
        });
        return count;
    }
    var MODEL = [1250, 1200, 1000, 800, 600, 300];
    function modelType(text) {
        text = plain(text);
        for (var i = 0; i < MODEL.length; i++) {
            if (new RegExp('(^|[^0-9.])' + MODEL[i] + '([^0-9]|$)').test(text)) { return String(MODEL[i]); }
        }
        return '';
    }
    // Product to be packed: Liquid / Paste = L, Powder / Granules = P
    function liquidOrPowder() {
        var v = plain(val('producttobepacked')).toLowerCase();
        if (v === '1' || /liquid|paste/.test(v)) { return 'L'; }
        if (v === '2' || /powder|granul/.test(v)) { return 'P'; }
        return '';
    }
    // same layout as libraries/Fg_code.php title1():  SP-{model}{L|P}-{tracks}T-{axis}A-{speed} | {machine type}
    function title1() {
        var model = modelType(val('mach_model_no')) || modelType(val('machinemodelno')), lp = liquidOrPowder();
        var pieces = ['SP' + (model || lp ? '-' + model + lp : '')];
        var track = firstNumber(val('nooftracks'), false);
        if (track) { pieces.push(track + 'T'); }
        var axis = axisCount();
        if (axis) { pieces.push(axis + 'A'); }
        var speed = firstNumber(editorValue('actualspeed'), true);
        if (speed) { pieces.push(speed); }
        var title = pieces.join('-'), machine = plain(val('machineName') || val('machineNameDFSF'));
        if (machine) { title += ' | ' + machine; }
        return title.substring(0, 255);
    }

    var t1 = document.getElementById('fg_title1'), t2 = document.getElementById('fg_title2'), codeBox = document.getElementById('fg_code'), note = document.getElementById('fg_code_note');

    // Title 1 is rebuilt whenever one of its fields changes; Title 2 copies it until the sales person edits Title 2.
    var following = (t2.value.trim() === '' || t2.value.trim() === t1.value.trim());
    t2.addEventListener('input', function () { following = (t2.value.trim() === t1.value.trim()); });
    function refresh() {
        var built = title1();
        t1.value = built;
        if (following) { t2.value = built; }
    }

    // the code: the saved one for an edit; otherwise the number this quotation will receive when saved
    if (savedCode) {
        codeBox.value = savedCode;
    } else {
        $.getJSON("<?php echo page_url; ?>Opportunity/fg_code_next", function (res) {
            codeBox.value = res.next || '';
            note.textContent = res.next ? 'This number is assigned when the quotation is saved.' : '';
        });
    }


    document.addEventListener('DOMContentLoaded', function () {
        ['mach_model_no', 'machinemodelno', 'machineName', 'producttobepacked', 'nooftracks', 'actualspeed'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) { el.addEventListener('change', refresh); el.addEventListener('keyup', refresh); }
        });
        // axis rows are added, edited and removed dynamically, so listen on the document
        ['change', 'keyup'].forEach(function (type) {
            document.addEventListener(type, function (e) {
                if (e.target && /axis/i.test(e.target.name || '')) { refresh(); }
            });
        });
        document.addEventListener('click', function (e) {
            if (e.target && /axis|remove|delete/i.test((e.target.id || '') + ' ' + (e.target.className || ''))) { setTimeout(refresh, 300); }
        });
        if (window.CKEDITOR && CKEDITOR.instances && CKEDITOR.instances.actualspeed) {
            CKEDITOR.instances.actualspeed.on('change', refresh);
        }
        // last look before saving, so the saved titles match the form
        var form = t1.form;
        if (form) { form.addEventListener('submit', refresh, true); }
    });
})();
</script>
