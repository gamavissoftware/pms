# Navigation snippet

**File:** `application/views/common/nav-menu.php`
**Paste at:** **line 1352** — between the `<?php }?>` on line 1350 that
closes the M/cs Dispatch Report block, and the `</ul>` on line 1354.

Apply this yourself. It is not in the module's file list and has not
been applied here.

---

## Why there is no id in it

The gate reads the submodule id from `application/config/abom.php`
rather than hardcoding one, so the snippet is correct whatever
`abom_003_permissions.sql` assigned, on every environment, with no
per-environment edit.

It follows the surrounding items' pattern in every other respect: the
same `module_capablity` lookup, `->where('role_id', $user_id)` (that
column holds a **user** id — see MODULE_CHANGELOG.md §0.9), the same
`<li><div class="dash">` structure, an inline SVG icon, and an `active`
class driven by `$this->uri->segment(1)`.

If the config is not yet filled in, `$abom_generator_id` is `0`, the
gate is skipped and **no menu item renders** — the same fail-quiet
behaviour as an ungranted role, and consistent with the approval panel
showing its configuration diagnostic rather than an empty toolbar.

---

## The snippet

```php
                <?php

// Automation BOM Generator. The submodule id comes from
// application/config/abom.php, so this needs no edit per environment.
// module_capablity.role_id holds a USER id despite the name — the same
// convention every other item on this menu uses.
$this->config->load('abom', true, true);
$abom_ids = $this->config->item('abom_submodule_ids', 'abom');
$abom_generator_id = is_array($abom_ids) && !empty($abom_ids['generator'])
    ? (int) $abom_ids['generator']
    : 0;

if ($abom_generator_id > 0) {
    $abom_qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')
        ->from('module_capablity')
        ->where('role_id', $user_id)
        ->where('moduleid', (int) $this->config->item('abom_module_id', 'abom'))
        ->where('submoduleid', $abom_generator_id)
        ->where('submodule_access', '1')
        ->get();

    if ($abom_qry->num_rows() > 0) {

?>
                    <li>
                        <div class="dash">
                            <a <?php if ($this->uri->segment(1) == 'abom') { ?>class="active" <?php } ?>href="<?php echo page_url; ?>abom/generate">
                                <div>
                                    <svg id="Layer_1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 0a12 12 0 1 0 12 12A12.013 12.013 0 0 0 12 0Zm0 22a10 10 0 1 1 10-10 10.011 10.011 0 0 1-10 10Zm5-10a1 1 0 0 1-1 1h-3v3a1 1 0 0 1-2 0v-3H8a1 1 0 0 1 0-2h3V8a1 1 0 0 1 2 0v3h3a1 1 0 0 1 1 1Z"/>
                                    </svg>
                                </div>
                                <div>Automation BOM Generator</div>
                            </a>
                        </div>
                    </li>
<?php
    }
}
?>
```

---

## After pasting

1. `php -l application/views/common/nav-menu.php` — must report no
   syntax errors.
2. Load any page. The item appears for users holding the grant, and the
   rest of the menu is unchanged.
3. Click it — `/index.php/abom/generate` loads.

If the item does not appear, in order: is
`$config['abom_submodule_ids']['generator']` set? Does that id exist in
`submodule` under module 4? Does the user have a `module_capablity` row
with `role_id = <their user_id>`, that `submoduleid`, and
`submodule_access = 1`?
