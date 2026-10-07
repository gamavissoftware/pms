<footer>Overtime authorization only. Approved hours are not attendance, hours actually worked, or payroll calculations. Times use the PMS timezone (<?php echo ot_e(date_default_timezone_get()); ?>).</footer>
<div id="ot-loader" class="loader" hidden role="status" aria-live="polite"><div><div class="spinner" aria-hidden="true"></div><strong>Saving…</strong><p>Please wait. Your request is being processed.</p></div></div>
</main>
<?php $this->load->view('common/footer'); ?>
</div></div>
<?php foreach (array('detect','fastclick','jquery.slimscroll','jquery.blockUI','waves','wow.min','jquery.nicescroll','jquery.scrollTo.min','jquery.core','jquery.app') as $script) { ?>
<script src="<?php echo assets_url; ?>js/<?php echo $script; ?>.js"></script>
<?php } ?>
<script>
(function(){
    var forms = document.querySelectorAll('#overtime-module form[method="post"]');
    forms.forEach(function(form){
        form.addEventListener('submit',function(event){
            if(form.dataset.saving==='1'){event.preventDefault();return;}
            if(!form.checkValidity()){event.preventDefault();form.reportValidity();return;}
            var confirmation=form.dataset.confirm;
            if(confirmation && !window.confirm(confirmation)){event.preventDefault();return;}
            // Preserve submit-button values before disabling controls.
            var submitter=event.submitter;
            if(submitter && submitter.name){var hidden=document.createElement('input');hidden.type='hidden';hidden.name=submitter.name;hidden.value=submitter.value;hidden.dataset.submitValue='1';form.appendChild(hidden);}
            form.dataset.saving='1';form.setAttribute('aria-busy','true');
            form.querySelectorAll('button[type="submit"]').forEach(function(button){button.disabled=true;});
            document.getElementById('ot-loader').hidden=false;
        });
    });
    window.addEventListener('pageshow',function(){
        document.getElementById('ot-loader').hidden=true;
        forms.forEach(function(form){form.dataset.saving='0';form.setAttribute('aria-busy','false');form.querySelectorAll('button[type="submit"]:not([data-locked])').forEach(function(button){button.disabled=false;});form.querySelectorAll('[data-submit-value]').forEach(function(input){input.remove();});});
    });
    document.querySelectorAll('#overtime-module [data-print]').forEach(function(button){button.addEventListener('click',function(){window.print();});});
    document.querySelectorAll('#overtime-module .ot-module-nav a').forEach(function(a){if(a.href===window.location.href.split('?')[0])a.setAttribute('aria-current','page');});
    var hours=document.getElementById('ot-hours'),users=document.getElementById('ot-users'),manual=document.getElementById('ot-manual'),preview=document.getElementById('ot-duration');
    function duration(){
        if(!hours||!users||!manual||!preview)return;
        var count=users.selectedOptions.length+manual.value.split(/\r?\n/).filter(function(name){return name.trim()!=='';}).length;
        users.setCustomValidity(count ? '' : 'Select at least one PMS user or enter a worker name.');
        var perPerson=Math.round(Number(hours.value)*60)/60;
        preview.textContent=count+' people × '+perPerson.toFixed(2)+' hours = '+(count*perPerson).toFixed(2)+' total person-hours';
    }
    [hours,users,manual].forEach(function(field){if(field){field.addEventListener('input',duration);field.addEventListener('change',duration);}});duration();
    // Select2 raises jQuery change events, which these native listeners never see,
    // so the enhanced people picker calls the recount directly.
    window.otRefreshDuration=duration;
})();
</script></body></html>
