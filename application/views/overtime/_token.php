<input type="hidden" name="overtime_csrf" value="<?php echo ot_e($csrf); ?>">
<?php if ($this->config->item('csrf_protection')) { ?><input type="hidden" name="<?php echo ot_e($this->security->get_csrf_token_name()); ?>" value="<?php echo ot_e($this->security->get_csrf_hash()); ?>"><?php } ?>
