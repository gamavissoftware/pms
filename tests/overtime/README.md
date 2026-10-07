# Overtime request approval module

## Current team-overtime workflow

New overtime requests are raised from `/index.php/Overtime/create` by an active
team leader, department head or administrator account that has OVERTIME REQUESTS
Add access. The requester selects a DF, selects one or more PMS users, optionally
enters manual labour/person names, enters the overtime hours per person and a
valid reason, then submits the request.

All new requests go directly to Shubham Sharma (`user_id = 139`) for approval.
Approval assigns the overtime to the selected PMS users and records manual
workers in the overtime assignment/report rows. Rejection remarks are optional.
Manual workers do not receive PMS notifications because they do not have PMS
accounts. Legacy two-stage requests are still readable and pending legacy items
are also routed to user 139 for final handling.

Reports are person-row based: a request for four people at two hours is shown as
eight requested person-hours. Filters include DF, date range, status, PMS user,
department and person/manual-worker name. CSV export uses the same scoped rows.

Install or upgrade in this order:

```sh
mysql PMS_DB < Database/overtime_001.sql
mysql PMS_DB < Database/overtime_002_permissions.sql
mysql PMS_DB < Database/overtime_003_team_requests.sql
```

`overtime_003_team_requests.sql` is the upgrade that adds DF linkage and the
per-person assignment table. Run it once after the base module migration.

## Install

Deploy these new files together:

- `application/controllers/Overtime.php`
- `application/models/Overtime_model.php`
- `application/helpers/overtime_helper.php`
- `application/views/overtime/` (all views)

Run `Database/overtime_001.sql` and then `Database/overtime_002_permissions.sql` in the intended PMS database using the normal deployment process. It creates seven InnoDB tables; it does not alter existing application tables. It is safe to rerun. Until installation, module pages show a setup message. No existing controllers or routes are changed. The shared navigation and Task Master dashboard views now include permission-aware overtime links.

Open `/index.php/Overtime` (production URL after deployment: `https://pms.shubhampack.in/index.php/Overtime`). Direct endpoints:

| Page | Path |
|---|---|
| Requests | `/index.php/Overtime` |
| New request | `/index.php/Overtime/create` |
| Approval inbox | `/index.php/Overtime/approvals` |
| Reports and CSV | `/index.php/Overtime/reports` |
| Administrator settings | `/index.php/Overtime/settings` |

An existing authenticated PMS session is required. The module uses active users in `system_users`, existing departments, `presto_team_members` and active `prestogroup_teams`. Administrator eligibility is resolved from the current database role: `user_role.isadmin = 1` and `user_role.status = 1`. It does not rely on the unused `logged_in.adminuser` flag or hard-coded user IDs. Users and administrators are limited to their business location.

Before rollout, an administrator should review request limits in Settings and confirm user 139 is active. Team membership and department-head setup now controls who can raise requests; administrator accounts can raise requests as well. Reporting-leader overrides remain in the module only for older records.

## Workflow

`PENDING_ADMIN → APPROVED`

- A team leader, department head or administrator submits a DF overtime request for one or more PMS users and/or manual workers.
- Shubham Sharma (`user_id = 139`) approves or rejects the requested hours. Approval assigns the work to the selected PMS users.
- Rejection remarks are optional. Manual workers are recorded for audit/reporting but do not receive PMS notifications.
- The requester can cancel a pending request, or an approved request before its start time, with a reason. Rejected/cancelled requests cannot be reopened; submit a new request for changes.
- No delete or silent edit endpoint is provided. Every submission, decision and cancellation has an audit event with actor, timestamp, status transition and remarks.
- Transactional in-module notifications appear on the requests screen for requesters, assigned PMS users and user 139. No email, SMS, WhatsApp, payroll or attendance service is invoked.

## Default policy

Operational defaults, configurable by an administrator for their location:

- Maximum 720 net minutes per request and per overtime start date.
- No backdating; up to 90 days ahead.
- Positive hours per person, maximum 24 hours per request; overnight work supported.
- Break entry is not used for new team requests.
- All minutes of an overnight request count against its start date, not split at midnight. This convention is shown on the form and reports.
- Pending and approved requests count toward the daily limit and block overlapping time intervals, including breaks. Adjacent intervals are allowed. Cancelled/rejected requests release the interval.
- Existing requests are not retroactively recalculated when policy changes.

These are overtime authorization hours, not attendance verification, statutory overtime calculations or payroll amounts.

## Security and consistency

All mutation endpoints require POST and a session-bound CSRF token even though global application CSRF is disabled. Forms also emit the global CSRF token when enabled. User-supplied identity, approval stage, hours, department and location are not trusted. Database queries bind inputs; output is HTML-escaped; CSV formula cells are neutralized.

Each submission has a stable random idempotency key with an employee/key unique index. A per-employee InnoDB mutex (exclusive no-op upsert plus row locking) serializes duplicate and overlapping submissions, cancellations and decisions. Request, audit and notifications commit together. Competing final approvals cannot overwrite each other. The UI shows a loader, blocks repeat submission and resets on back navigation. Invalid forms remain editable.

Reports and filter options apply the same location/requester/assigned-worker/admin scope as request details. Employees see their own requests and approved assignments; administrators see their location; user 139 sees approval scope. Reports filter by DF, start date, person, employee, department and status, with person/department/DF summaries, paginated detail, print styles and full-detail CSV (maximum 10,000 rows; larger selections must be narrowed). Approved hours include only final `APPROVED` request rows. The printable detailed list is the current page; CSV includes the whole matching selection within the export limit.

## Automated checks

PHP 7.4+ / CodeIgniter 3 compatible implementation (verified locally on PHP 8.3). No package install is needed for PHP tests beyond PDO SQLite.

```sh
php tests/overtime/regression.php
php tests/overtime/hod.php
php tests/overtime/permissions.php
php tests/overtime/render.php
```

The tests exercise the actual model/controller methods with an isolated SQL adapter. Default tests use in-memory SQLite and translate only engine-specific SQL. The renderer checks all screens for PHP warnings and escaping, and writes fixture HTML to the system temp directory (`pms-overtime-preview`). No production data or notifications are used.

For real MySQL testing, start a disposable MySQL instance with networking disabled, a private socket under `/tmp/pms-overtime-mysql.<suffix>/mysql.sock`, and an isolated data directory. The harness refuses other socket paths and creates/drops a uniquely named test database. Use only this temporary empty test instance (root with no password):

```sh
OVERTIME_TEST_MYSQL_SOCKET=/tmp/pms-overtime-mysql.EXAMPLE/mysql.sock php tests/overtime/regression.php
OVERTIME_TEST_MYSQL_SOCKET=/tmp/pms-overtime-mysql.EXAMPLE/mysql.sock php tests/overtime/concurrency.php
```

Both migration repeatability and production SQL run on MySQL. The concurrency test uses separate PHP processes and database connections to verify same-key idempotency, different-key overlap rejection and repeated Shubham approval attempts. Stop the disposable server afterwards.

For desktop/mobile browser checks, make Playwright available to Node and install its Chromium browser, or set `OVERTIME_TEST_CHROME` to a local Chrome executable:

```sh
php tests/overtime/render.php
node tests/overtime/browser.cjs
```

Optional `OVERTIME_PREVIEW_DIR` overrides the fixture directory. Browser checks cover all five main screens at desktop/mobile widths, JavaScript errors, hours preview, validation, loader, repeated submit handling and back-navigation reset. Screenshots are saved next to the fixture HTML.

## Staging acceptance

1. Run migrations in order; open the module under the existing PMS session and confirm administrator accounts with Add access show the request button.
2. Submit with a team leader, a HOD and an administrator. Approve each as Shubham Sharma (139). Verify audit, notifications and assignments.
3. Verify denial for ordinary users, non-139 approvers, unrelated users, cross-location users, GET mutation requests and bad CSRF tokens.
4. Reject without remarks; cancel pending/future-approved requests; verify past approvals cannot be cancelled.
5. Compare filtered reports/CSV totals with request details for DF, date, department, PMS user and manual-worker name.
6. Test a slow request, repeated clicks and two simultaneous browser sessions. Verify a single committed request/decision.

Local tests have passed on SQLite and MySQL 9.6, including concurrent connections, plus headless Chrome desktop/mobile checks. Production deployment, production database migration and authenticated staging acceptance are not performed by these tests.

## Current upload notes

The September team-overtime update adds DF selection, PMS/manual workers,
administrator request eligibility, fixed approval by user 139, assignment rows
and person-row reporting. Upload the application files and run the third
migration once.

Upload these changed application files together (paths relative to the PMS root):

```text
application/controllers/Overtime.php
application/models/Overtime_model.php
application/helpers/overtime_helper.php
application/views/overtime/_header.php
application/views/overtime/_footer.php
application/views/overtime/create.php
application/views/overtime/index.php
application/views/overtime/settings.php
application/views/overtime/view.php
application/views/overtime/reports.php
application/views/overtime/_filters.php
application/views/overtime/_stats.php
application/views/overtime/_table.php
application/views/common/nav-menu.php
application/views/dashboard/task_master_dashboard.php
Database/overtime_003_team_requests.sql
```

Import `Database/overtime_002_permissions.sql` if the base permission rows are
not already present. Existing grants are preserved. In the existing user-wise
module permission screen, enable the parent module, allow the appropriate
submodule, and set Add/Edit as below:

| Parent module | Submodule | Required rights |
|---|---|---|
| OVERTIME | OVERTIME REQUESTS | Allow to view; Add to raise/cancel eligible requests |
| OVERTIME | OVERTIME APPROVALS | Allow to view inbox; Edit is honored only for user 139 |
| OVERTIME | OVERTIME REPORTS | Allow to view and export scoped reports |
| MASTER | OVERTIME REQUEST LIMITS | Allow to view; Edit to save limits; administrator role also required |
| MASTER | OVERTIME REPORTING LEADERS | Legacy only; administrator role also required |

Permissions are enforced on URLs and writes, not just navigation. Administrators
also need OVERTIME REQUESTS Add access to raise requests; their admin role now
satisfies the request-eligibility check. Approval decisions are restricted to
Shubham Sharma (`user_id = 139`).

The two master tiles appear under `Dashboard/task_master_dashboard`; the
overtime tabs link there rather than presenting Settings as a separate master
location. Module links, master tiles, and edit buttons follow the grants.

The request form uses four quarter-width desktop columns (two on tablets, one on
phones). It requires a DF, start datetime, hours per person, at least one PMS or
manual worker, and a valid reason. The permission, workflow and rendering suites
pass locally; live deployment and permission assignment are still required.
