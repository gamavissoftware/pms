<?php
// Finished goods fields for the first Annexure-IV line item, shown right after the Item description (Model) box on the
// new, edit and clone quotation screens.
//   Finished Goods Code : SPFGMC + number, assigned by the system (read-only). A new or cloned quotation shows the number it
//                         will receive when saved; an edited quotation keeps its own.
//   Title 1             : blank by default. "Fill from quotation" builds it from the form: Machine Model No | Machine Type |
//                         Product to be packed | tracks | actual speed | no. of axis (the text itself is read-only).
//   Title 2             : blank by default; the sales person types it, or the same button copies Title 1 into it.
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

    <button type="button" class="btn btn-default btn-sm" id="fg_fill" style="margin-top:6px;">Fill from quotation</button>
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
    function selectedText(id) {
        var el = document.getElementById(id);
        return el && el.selectedIndex > 0 ? plain(el.options[el.selectedIndex].text) : '';
    }
    // same layout as libraries/Fg_code.php title1():
    // Machine Model No | Machine Type | Product to be packed | N Track | Speed N | N Axis
    function title1() {
        var parts = [];
        var model = plain(val('machineModel')), machine = plain(val('machineName') || val('machineNameDFSF')), product = selectedText('producttobepacked');
        if (model) { parts.push(model); }
        if (machine) { parts.push(machine); }
        if (product) { parts.push(product); }
        var track = firstNumber(val('nooftracks'), false);
        if (track) { parts.push(track + ' Track'); }
        var speed = firstNumber(editorValue('actualspeed'), true);
        if (speed) { parts.push('Speed ' + speed); }
        var axis = axisCount();
        if (axis) { parts.push(axis + ' Axis'); }
        return parts.join(' | ').substring(0, 255);
    }

    var t1 = document.getElementById('fg_title1'), t2 = document.getElementById('fg_title2'), codeBox = document.getElementById('fg_code'), note = document.getElementById('fg_code_note');

    // Titles stay as they are (blank by default) until the button is used: it rebuilds Title 1 from the form as it is now
    // and copies it into Title 2 when Title 2 is still empty.
    document.getElementById('fg_fill').addEventListener('click', function () {
        var built = title1();
        t1.value = built;
        if (t2.value.trim() === '') { t2.value = built; }
    });

    // the code: the saved one for an edit; otherwise the number this quotation will receive when saved
    if (savedCode) {
        codeBox.value = savedCode;
    } else {
        $.getJSON("<?php echo page_url; ?>Opportunity/fg_code_next", function (res) {
            codeBox.value = res.next || '';
            note.textContent = res.next ? 'This number is assigned when the quotation is saved.' : '';
        });
    }

})();
</script>
