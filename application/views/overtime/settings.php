<?php
$show_policy = !empty($permissions['policy']) && ($section === '' || $section === 'policy');
$show_leaders = !empty($permissions['leaders']) && ($section === '' || $section === 'leaders');
$show_costs = !empty($permissions['costs']) && ($section === '' || $section === 'cost');
?>
<div class="ot-settings">
    <div class="heading">
        <div><div class="eyebrow">Administrator controls</div><h1>Overtime settings</h1><p class="muted">Manage request limits for team overtime. Older reporting-leader settings remain only for legacy records.</p></div>
        <span class="ot-settings-tag"><i class="fa fa-sliders" aria-hidden="true"></i> Approval configuration</span>
    </div>
    <div class="ot-approval-strip">
        <span><i class="fa fa-check-circle" aria-hidden="true"></i> Approval flow</span>
        <strong>Team leader / HOD / Administrator <span aria-hidden="true">→</span> Shubham Sharma (139) <span aria-hidden="true">→</span> Assignment</strong>
        <small>New requests are approved only by user 139.</small>
    </div>

    <?php if ($show_policy) { ?><section class="card" id="request-limits">
        <div class="ot-section-heading"><span class="ot-section-icon"><i class="fa fa-clock-o" aria-hidden="true"></i></span><div><h2>Request limits</h2><p class="muted">Set the permitted duration and date window for new requests.</p></div></div>
        <form method="post" action="<?php echo ot_e(ot_link('save_settings')); ?>">
            <?php $this->load->view('overtime/_token', compact('csrf')); ?>
            <input type="hidden" name="action" value="policy">
            <div class="ot-settings-grid">
                <?php foreach (array('max_request_minutes'=>'Per-request limit', 'max_daily_minutes'=>'Daily limit', 'past_days'=>'Past date allowance', 'future_days'=>'Advance request window') as $key=>$label) { $minutes = strpos($key,'minutes') !== false; ?>
                    <div class="col-md-3">
                        <label for="<?php echo ot_e($key); ?>"><?php echo ot_e($label); ?></label>
                        <div class="ot-unit-input"><input id="<?php echo ot_e($key); ?>" type="number" min="<?php echo $minutes ? 1 : 0; ?>" max="<?php echo $minutes ? 1440 : 365; ?>" step="1" name="<?php echo ot_e($key); ?>" value="<?php echo (int) $policy[$key]; ?>" aria-describedby="<?php echo ot_e($key); ?>-unit" required><span id="<?php echo ot_e($key); ?>-unit"><?php echo $minutes ? 'minutes' : 'days'; ?></span></div>
                    </div>
                <?php } ?>
            </div>
            <div class="ot-settings-actions"><small>Daily totals include pending and approved requests. Overnight hours count against the start date.</small><?php if (!empty($permissions['policy_edit'])) { ?><button type="submit"><i class="fa fa-check" aria-hidden="true"></i> Save limits</button><?php } ?></div>
        </form>
    </section>

    <?php } if ($show_leaders) { ?><section class="card" id="reporting-leaders">
        <div class="ot-section-heading"><span class="ot-section-icon"><i class="fa fa-users" aria-hidden="true"></i></span><div><h2>Legacy reporting-leader override</h2><p class="muted">Kept for old requests and historical mappings. New overtime requests go directly to Shubham Sharma.</p></div></div>
        <form method="post" action="<?php echo ot_e(ot_link('save_settings')); ?>">
            <?php $this->load->view('overtime/_token', compact('csrf')); ?><input type="hidden" name="action" value="leader">
            <div class="ot-settings-grid">
                <div class="col-md-3"><label for="override-employee">Employee *</label><select id="override-employee" name="employee_id" required><option value="">Select employee</option><?php foreach ($users as $user) { ?><option value="<?php echo (int) $user['user_id']; ?>"><?php echo ot_e(ot_person_name($user['first_name'], $user['last_name'])); ?> #<?php echo (int) $user['user_id']; ?></option><?php } ?></select></div>
                <div class="col-md-3"><label for="override-leader">Reporting leader</label><select id="override-leader" name="leader_id"><option value="0">Use existing team mapping</option><?php foreach ($users as $user) { ?><option value="<?php echo (int) $user['user_id']; ?>"><?php echo ot_e(ot_person_name($user['first_name'], $user['last_name'])); ?> #<?php echo (int) $user['user_id']; ?></option><?php } ?></select></div>
                <div class="col-md-3"><label for="override-reason">Reason for change *</label><input id="override-reason" name="reason" minlength="5" maxlength="1000" placeholder="Explain this exception" required></div>
                <div class="col-md-3 ot-settings-submit"><?php if (!empty($permissions['leaders_edit'])) { ?><button type="submit"><i class="fa fa-check" aria-hidden="true"></i> Save reporting leader</button><?php } ?></div>
            </div>
            <p class="ot-field-help">Choose "Use existing team mapping" to remove an override. These settings are retained for legacy records only; new requests use the fixed approver.</p>
        </form>
    </section>

    <?php } if ($show_costs) { ?><section class="card" id="cost-rates">
        <div class="ot-section-heading"><span class="ot-section-icon"><i class="fa fa-inr" aria-hidden="true"></i></span><div><h2>Overtime cost rates</h2><p class="muted">The hourly cost used to value overtime. Reports total it by DF, day, person and department.</p></div></div>
        <form method="post" action="<?php echo ot_e(ot_link('save_settings')); ?>">
            <?php $this->load->view('overtime/_token', compact('csrf')); ?><input type="hidden" name="action" value="cost">
            <div class="ot-settings-grid">
                <div class="col-md-3"><label for="rate-scope">Applies to *</label><select id="rate-scope" name="scope_target" required>
                    <option value="LOCATION">Everyone else in this location</option>
                    <option value="MANUAL">Manual / contract labour</option>
                    <optgroup label="One department"><?php foreach ($departments as $department) { ?><option value="DEPARTMENT:<?php echo (int) $department['department_id']; ?>"><?php echo ot_e($department['department']); ?></option><?php } ?></optgroup>
                    <optgroup label="One employee"><?php foreach ($users as $user) { ?><option value="USER:<?php echo (int) $user['user_id']; ?>"><?php echo ot_e(ot_person_name($user['first_name'], $user['last_name'])); ?> #<?php echo (int) $user['user_id']; ?></option><?php } ?></optgroup>
                </select></div>
                <div class="col-md-3"><label for="rate-amount">Cost per hour (&#8377;) *</label><input id="rate-amount" name="hourly_rate" type="number" min="0" max="99999999" step="0.01" placeholder="0.00" required></div>
                <div class="col-md-3"><label for="rate-note">Note</label><input id="rate-note" name="note" maxlength="255" placeholder="How this rate was arrived at"></div>
                <div class="col-md-3 ot-settings-submit"><?php if (!empty($permissions['costs_edit'])) { ?><button type="submit"><i class="fa fa-check" aria-hidden="true"></i> Save cost rate</button><?php } ?></div>
            </div>
            <p class="ot-field-help">The most specific rate wins: an employee rate beats their department rate, which beats the location default. Manual and contract names typed on a request use the manual rate, then the location default &mdash; department rates never apply to them. Saving the same scope again replaces its rate.</p>
        </form>
        <div class="table-wrap"><table><thead><tr><th>Applies to</th><th class="num">Cost per hour</th><th>Note</th><th>Last updated</th><?php if (!empty($permissions['costs_edit'])) { ?><th class="no-print"></th><?php } ?></tr></thead><tbody>
            <?php foreach ($rates as $rate) { ?><tr><td><?php echo ot_e($rate['scope_label']); ?><br><small><?php echo ot_e($rate['scope']); ?></small></td><td class="num">&#8377; <?php echo ot_money($rate['hourly_rate']); ?></td><td class="preserve"><?php echo ot_e($rate['note']); ?></td><td><?php echo ot_e($rate['updated_at']); ?><br><small><?php echo ot_e(trim($rate['actor_first_name'] . ' ' . $rate['actor_last_name'])); ?></small></td>
            <?php if (!empty($permissions['costs_edit'])) { ?><td class="no-print"><form method="post" action="<?php echo ot_e(ot_link('save_settings')); ?>" data-confirm="Remove this cost rate? Overtime already valued with it keeps its stored cost."><?php $this->load->view('overtime/_token', compact('csrf')); ?><input type="hidden" name="action" value="cost_delete"><input type="hidden" name="rate_id" value="<?php echo (int) $rate['id']; ?>"><button class="danger secondary" type="submit">Remove</button></form></td><?php } ?></tr><?php } ?>
            <?php if (!$rates) { ?><tr><td colspan="5" class="empty">No cost rates yet. Until one is set, overtime reports show zero cost.</td></tr><?php } ?>
        </tbody></table></div>
        <?php if (!empty($permissions['costs_edit'])) { ?><div class="ot-settings-actions"><small>Recalculate restates the stored cost on every overtime person row in this location at the rates above. Run it once after setting rates up, so overtime raised before today stops reading as zero. It rewrites recorded costs, including approved ones.</small>
            <form method="post" action="<?php echo ot_e(ot_link('save_settings')); ?>" data-confirm="Restate stored costs for all overtime in this business location at the current rates?"><?php $this->load->view('overtime/_token', compact('csrf')); ?><input type="hidden" name="action" value="cost_recalculate"><button class="secondary" type="submit"><i class="fa fa-refresh" aria-hidden="true"></i> Recalculate stored costs</button></form></div><?php } ?>
    </section>

    <?php } ?>
    <div class="ot-settings-tables">
        <?php if ($show_leaders) { ?><section class="card"><div class="ot-section-heading"><span class="ot-section-icon"><i class="fa fa-user" aria-hidden="true"></i></span><div><h2>Current legacy overrides <span class="ot-count"><?php echo count($overrides); ?></span></h2><p class="muted">Exceptions retained for historical team mapping.</p></div></div>
            <div class="table-wrap"><table><thead><tr><th>Employee</th><th>Reporting leader</th><th>Updated</th></tr></thead><tbody><?php foreach ($overrides as $row) { ?><tr><td><?php echo ot_e($row['first_name'] . ' ' . $row['last_name']); ?></td><td><?php echo ot_e($row['leader_first_name'] . ' ' . $row['leader_last_name']); ?></td><td><?php echo ot_e($row['updated_at']); ?></td></tr><?php } ?><?php if (!$overrides) { ?><tr><td colspan="3" class="empty">No overrides. Existing team mappings apply.</td></tr><?php } ?></tbody></table></div>
        </section>
        <?php } ?><section class="card"><div class="ot-section-heading"><span class="ot-section-icon"><i class="fa fa-history" aria-hidden="true"></i></span><div><h2>Recent settings history</h2><p class="muted">Latest 50 changes. The full audit is retained.</p></div></div>
            <div class="table-wrap"><table><thead><tr><th>When / Who</th><th>Action</th><th>Details</th></tr></thead><tbody><?php foreach ($audit as $row) { ?><tr><td><?php echo ot_e($row['created_at']); ?><br><?php echo ot_e($row['first_name'] . ' ' . $row['last_name']); ?></td><td><?php echo ot_e($row['action']); ?></td><td class="preserve"><?php echo ot_e($row['note']); ?></td></tr><?php } ?><?php if (!$audit) { ?><tr><td colspan="3" class="empty">No settings changes recorded.</td></tr><?php } ?></tbody></table></div>
        </section>
    </div>
</div>
