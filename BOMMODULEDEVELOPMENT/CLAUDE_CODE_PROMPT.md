# Prompt for Claude Code — Automation BOM Module

Copy everything inside the block below and paste it as your first message to Claude Code,
from the root of your CodeIgniter 3 project.

---

Read these files first, in full, before writing any code:

  BOMMODULEDEVELOPMENT/Automation_BOM_Module_CI3_Spec.md
  BOMMODULEDEVELOPMENT/abom_schema.sql
  BOMMODULEDEVELOPMENT/abom_seed.sql
  BOMMODULEDEVELOPMENT/SPM1200L_Automation_BOM_DF1826_DF1827_Review.html

The .md file is the complete build specification for an Automation BOM Generator
module. Implement it in this existing CodeIgniter 3 project. The .html file is the
approved UI prototype — the finished module must look and behave like it.

=== NON-NEGOTIABLE: DO NOT BREAK THE EXISTING APPLICATION ===

This is a live production codebase. The new module must be purely ADDITIVE.

1. Work on a new git branch: feature/automation-bom-module. Never commit to main.
2. Create NEW files only. Do not edit, rename, move, refactor or reformat any
   existing file except the small allowlist in point 3.
3. The ONLY existing files you may touch, and only by APPENDING:
     - application/config/routes.php      (append the new $route lines at the end)
     - application/config/autoload.php    (only if strictly required; tell me first)
   Show me the exact diff for each of these before applying it.
4. Database: create ONLY the new abom_* prefixed tables from abom_schema.sql.
   Do not ALTER, DROP, RENAME or add foreign keys to any existing table. Do not
   TRUNCATE anything outside abom_*. If a table name collides with something that
   already exists, stop and tell me instead of overwriting.
5. Do not upgrade, downgrade or remove any composer package, library in
   application/libraries, or file in application/third_party. If the module needs
   PhpSpreadsheet / TCPDF and something similar is already present, reuse the
   existing one. If nothing exists, ask me before adding a dependency.
6. Do not touch the existing session, auth, ACL or base controller code. Hook into
   whatever the project already uses. Read the existing controllers first and match
   their conventions for auth checks, CSRF and layout loading.
7. CSS and JS must be scoped. Put everything under assets/bom/ and wrap all module
   CSS in a container class (e.g. .abom-wrap) so it cannot leak into existing
   screens. Do not add global selectors, do not modify existing stylesheets, and
   do not load a different jQuery/Bootstrap version than the project already uses.
8. Do not change .htaccess, index.php, database.php credentials, or any
   environment/config value used by the rest of the app.
9. If anything in the spec appears to conflict with how this project already works,
   STOP and ask me. Do not resolve it by changing existing code.

=== BUILD ORDER ===

Follow section 9 of the spec exactly. In particular, build and test Bom_engine
(section 5) with the unit tests in section 5.1 BEFORE building any UI. Do not move
past that step until the two regression checks pass: a full FX5 generation returns
exactly 29 lines and a full iQ-R generation returns exactly 42 lines.

Do not re-key the master item data by hand. Import abom_seed.sql. It contains all
71 items extracted and verified against the source BOMs.

Do not "fix" the flagged data quality issues in the seed. They are deliberate
flags that engineering resolves through the UI.

=== VERIFICATION BEFORE YOU TELL ME IT'S DONE ===

1. Confirm the seed loaded: 71 rows in abom_item (29 family 1, 42 family 2),
   8 sections, 4 rules, 4 features, 6 formulas.
2. Run the section 5.1 tests and show me the output.
3. Generate both reference configurations and confirm they match the spec's
   Appendix B line counts.
4. Show me `git status` and `git diff --stat` so I can see that no existing file
   was modified beyond the allowlist.
5. Load two or three existing pages of the application and confirm they still
   render correctly with no console or PHP errors.

Work through it step by step and check in with me after each numbered stage in
section 9 rather than doing everything in one pass.

---

## Before you start — two minutes of insurance

Take a database dump and commit the current state first. The prompt tells Claude Code
not to touch existing tables, but a backup costs nothing:

    mysqldump -u USER -p YOUR_DB > backup_before_bom_module.sql
    git checkout -b feature/automation-bom-module

## If you want the safest possible path

Point Claude Code at a **staging copy** of the database rather than production for the
build, then run only the `abom_` table creation on production once you have reviewed
the result. The module reads no existing tables, so it can be developed entirely
against a scratch schema.

## Follow-up prompts you will probably need

After stage 3 (engine + tests):

> Show me the Bom_engine test output and the generated line list for 15 axes /
> 12 tracks / 180 PPM / Continuous. Compare it item by item against Appendix B
> of the spec before we build any UI.

After stage 4 (UI):

> Open BOMMODULEDEVELOPMENT/SPM1200L_Automation_BOM_DF1826_DF1827_Review.html and compare the rendered
> module screen against it. The palette, the 12 columns, the row-colour precedence
> and the section header rows must match. List any differences.

If anything looks wrong at any point:

> Stop. Revert the last change, show me git diff, and explain what you were trying
> to do before we continue.
