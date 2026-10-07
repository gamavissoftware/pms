<?php
$val = function ($key, $default = '') use ($values) { return isset($values[$key]) && is_scalar($values[$key]) ? $values[$key] : $default; };
$selected = isset($values['user_ids']) && is_array($values['user_ids']) ? array_filter($values['user_ids'], 'is_scalar') : array();
$person_labels = array();
$duplicate_names = array();
foreach ($users as $person) {
    $name = ot_person_name($person['first_name'], $person['last_name']);
    $person_labels[(int) $person['user_id']] = $name;
    $duplicate_names[$name] = isset($duplicate_names[$name]) ? $duplicate_names[$name] + 1 : 1;
}
// Only people who share a name keep an id beside it, so the list reads as names.
$person_label = function ($person) use ($person_labels, $duplicate_names) {
    $name = $person_labels[(int) $person['user_id']];
    return $duplicate_names[$name] > 1 ? $name . ' (#' . (int) $person['user_id'] . ')' : $name;
};
?>
<div class="heading"><div><div class="eyebrow">Plan additional work</div><h1>Request team overtime</h1><p class="muted">Team leader raises request → Shubham Sharma approves → overtime assigned to selected people.</p></div></div>
<?php if ($error !== '') { ?><div class="notice error" role="alert"><?php echo ot_e($error); ?></div><?php } ?>
<div class="card"><form method="post" action="<?php echo ot_e(ot_link('save')); ?>">
<?php $this->load->view('overtime/_token', compact('csrf')); ?><input type="hidden" name="submission_key" value="<?php echo ot_e($submission_key); ?>">
<div class="ot-create-grid">
<div><label>Requested by</label><input readonly value="<?php echo ot_e($viewer['first_name'].' '.$viewer['last_name']); ?>"></div>
<div><label for="ot-df">DF No. *</label><select id="ot-df" name="df_id" required data-placeholder="Search a DF number"><option value="">Select DF</option><?php foreach ($dfs as $df) { ?><option value="<?php echo (int)$df['id']; ?>" <?php echo (int)$val('df_id') === (int)$df['id'] ? 'selected' : ''; ?>><?php echo ot_e($df['df_no'].' — '.$df['df_description']); ?></option><?php } ?></select></div>
<div><label for="ot-start">Overtime date and start time *</label><input id="ot-start" type="datetime-local" name="start_at" value="<?php echo ot_e($val('start_at')); ?>" required></div>
<div><label for="ot-hours">Overtime hours per person *</label><input id="ot-hours" type="number" name="hours" min="0.02" max="<?php echo (int)$policy['max_request_minutes']/60; ?>" step="0.01" value="<?php echo ot_e($val('hours')); ?>" required placeholder="e.g. 2 or 2.5"><small>The same hours apply to each person. Use a separate request for different hours.</small></div>
<div class="wide grid">
<div><label for="ot-users">Select PMS users</label><select id="ot-users" name="user_ids[]" multiple data-placeholder="Type a name to search, then pick as many people as you need"><?php foreach ($users as $person) { ?><option value="<?php echo (int)$person['user_id']; ?>" <?php echo in_array($person['user_id'],$selected) ? 'selected' : ''; ?>><?php echo ot_e($person_label($person)); ?></option><?php } ?></select><small>Start typing to search. Each person you pick becomes a tag; click its &times; to remove them.</small></div>
<div><label for="ot-manual">Person not in the list?</label><textarea id="ot-manual" name="manual_people" rows="8" maxlength="20000" placeholder="Enter one worker's name per line"><?php echo ot_e($val('manual_people')); ?></textarea><small>For labour or other workers without PMS accounts. Include a contractor or worker reference when names are the same. Manual workers are recorded in reports; they do not receive PMS notifications.</small></div>
</div>
<div class="wide"><div id="ot-duration" class="notice" aria-live="polite">Select people and enter hours to see total overtime.</div></div>
<div class="wide"><label for="ot-reason">Valid reason and planned work *</label><textarea id="ot-reason" name="reason" minlength="10" maxlength="4000" required placeholder="Explain why overtime is needed and the work to complete for this DF."><?php echo ot_e($val('reason')); ?></textarea></div>
<div class="wide"><label for="ot-reference">Additional work reference (optional)</label><input id="ot-reference" name="work_reference" maxlength="255" value="<?php echo ot_e($val('work_reference')); ?>"></div>
</div>
<div class="notice" style="margin-top:20px">Approval by <strong>Shubham Sharma (139)</strong> is required before assignment. Current limits: <?php echo ot_hours($policy['max_request_minutes']); ?> hours per person per request; <?php echo ot_hours($policy['max_daily_minutes']); ?> hours per start date. Requests may be made <?php echo (int)$policy['past_days']; ?> days in the past through <?php echo (int)$policy['future_days']; ?> days ahead. Overnight hours count against the start date. Overlap and daily-limit checks apply to PMS users.</div>
<div class="actions"><button type="submit">Send for approval</button><a class="button secondary" href="<?php echo ot_e(ot_link()); ?>">Back to requests</a></div>
</form></div>

<link href="<?php echo assets_url; ?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
<script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js"></script>
<style>
/* Select2 scoped to this module, so the picker matches the module's own inputs and
   keeps the height of the manual-worker box beside it. */
#overtime-module .select2-container {width:100%!important;}
/* The box starts at input height and grows with the tags. A tall empty box pushed the
   dropdown past the fold, and Select2 then opened it upwards over the fields above. */
#overtime-module .select2-container--default .select2-selection--multiple {min-height:42px;border:1px solid #b8c8d3;border-radius:6px;padding:3px 6px;}
#overtime-module .select2-container--default .select2-selection--single {height:40px;border:1px solid #b8c8d3;border-radius:6px;}
#overtime-module .select2-container--default .select2-selection--single .select2-selection__rendered {line-height:38px;padding-left:10px;padding-right:26px;font-size:13px;color:var(--ink);}
#overtime-module .select2-container--default .select2-selection--single .select2-selection__placeholder {color:#7d8fa0;}
#overtime-module .select2-container--default .select2-selection--single .select2-selection__arrow {height:38px;}
#overtime-module .select2-container--default.select2-container--focus .select2-selection--multiple {border-color:#77bfb9;outline:2px solid #77bfb9;outline-offset:1px;}
#overtime-module .select2-container--default .select2-selection--multiple .select2-selection__choice {background:#e6f2f0;border:1px solid #cce5df;color:#23756d;border-radius:4px;padding:2px 8px;margin:4px 5px 0 0;font-size:12px;}
#overtime-module .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {color:#23756d;margin-right:6px;font-weight:700;}
/* The module gives every <input> in this grid a border, padding and a min-height, and
   Select2's own inline search IS an input - left alone it renders as a second empty box
   beside the tags. It has to be reset back to a bare caret. */
#overtime-module .select2-container--default .select2-search--inline .select2-search__field {border:0;background:transparent;border-radius:0;box-shadow:none;margin:5px 0 0 4px;padding:0;height:26px;min-height:0;line-height:26px;font-size:13px;}
#overtime-module .select2-container--default .select2-search--inline .select2-search__field:focus {outline:none;box-shadow:none;}
#overtime-module .select2-container--default .select2-selection--multiple .select2-search__field::placeholder {color:#7d8fa0;}
/* The dropdown is attached to <body>, outside the module, so these selectors are
   unscoped - harmless, because this stylesheet only loads on this one screen. */
.select2-container--default .select2-results__option {font-size:13px;padding:7px 10px;}
.select2-container--default .select2-results__option--highlighted[aria-selected] {background:#087d75;}
.select2-container--default .select2-search--dropdown .select2-search__field {font-size:13px;}
</style>
<script>
jQuery(function($){
    if (!$.fn.select2) return;
    var df = $('#ot-df'), people = $('#ot-users');
    if (df.length) df.select2({width: '100%', placeholder: df.data('placeholder')});
    if (!people.length) return;
    people.select2({
        width: '100%',
        placeholder: people.data('placeholder'),
        closeOnSelect: false
    });
    // The person count, the hours preview and the "pick someone" validation all live in
    // the module footer and listen for native events, which Select2 does not raise.
    people.on('change', function () { if (window.otRefreshDuration) window.otRefreshDuration(); });
});
</script>
