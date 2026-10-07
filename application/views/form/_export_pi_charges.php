<?php $charges_editable = $this->db->table_exists('performa_invoice_commercial'); ?>
<?php if (!$charges_editable): ?>
<p class="text-danger">Charge editing is not available yet. Please contact your administrator.</p>
<?php endif; ?>
<table class="table table-bordered">
<thead><tr><th>Additional charge</th><th>Rate / amount</th></tr></thead>
<tbody>
<?php foreach (array('packing_charges'=>'Packing (%)', 'forwarding_charges'=>'Forwarding (%)', 'insurance'=>'Insurance (%)', 'freight_charges'=>'Freight amount (invoice currency)', 'installation'=>'Installation amount (invoice currency)', 'other_charges'=>'Other charges (invoice currency)') as $key=>$label): ?>
<tr><td><label for="pi_<?php echo $key; ?>"><?php echo $label; ?></label></td>
<td><input class="form-control" type="number" min="0" step="0.01" id="pi_<?php echo $key; ?>" name="pi_<?php echo $key; ?>" value="<?php echo (float) $pi_commercial[$key]; ?>" <?php echo $charges_editable ? '' : 'disabled'; ?>></td></tr>
<?php endforeach; ?>
<tr><td><label for="pi_freight">Freight terms</label></td><td><select class="form-control" id="pi_freight" name="pi_freight" <?php echo $charges_editable ? '' : 'disabled'; ?>>
<?php foreach (array(1=>'Extra at Actuals', 2=>'In Customer Scope', 3=>'Additional') as $value=>$label): ?>
<option value="<?php echo $value; ?>" <?php echo (int) $pi_commercial['freight'] === $value ? 'selected' : ''; ?>><?php echo $label; ?></option>
<?php endforeach; ?></select></td></tr>
</tbody></table>
