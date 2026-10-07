# Change request approval

Run `php tests/change_control/approval.php` with PDO SQLite enabled. The test uses an in-memory database and does not send notifications or touch production data.

New requests use `PENDING_APPROVAL` in both the request and department records. User 139 reviews them from the approval panel on the PMS dashboard or Change Control dashboard. Only that user can POST an approval decision, using a session-bound token. Approval changes the request to `OPEN` and departments to `PENDING_HEAD_ACTION`, then queues HOD and requester notifications in the same transaction. Rejection requires a reason, marks both levels `REJECTED`, and queues only the requester notification. The existing history table records the decision, actor, remarks and timestamp. Decisions are single-use; there is no reopen/resubmit action. A corrected request must be raised separately.

Existing OPEN, IN_PROGRESS and COMPLETED records retain their existing workflow. No schema migration or backfill is required; the existing status columns are VARCHAR(30).

## Deployment

Deploy these files together using the normal application deployment process:

- application/controllers/Df_change_control.php
- application/models/Df_change_control_model.php
- application/views/df_change_control/_approval_queue.php
- application/views/df_change_control/create.php
- application/views/df_change_control/dashboard.php
- application/views/df_change_control/view.php
- application/views/dashboard/newdesigndashboard.php

The working tree contains other pre-existing edits. Review the complete diff before deploying whole files.

## Acceptance checks in staging

1. Raise a change request as an authorized requester. Verify PENDING_APPROVAL, no HOD notifications, and no HOD queue entry or direct detail access (unless the HOD is also the requester).
2. Sign in as user 139. Verify the PMS dashboard approval panel lists the request and opens its full details, impact, attachment and department list.
3. Approve. Verify HOD queues and notifications appear, and assignment, progress, completion and requester communication follow the existing workflow.
4. Raise another request and reject with a reason. Verify requester notification and decision history, no HOD release, and no assignment/execution through direct URLs.
5. Check another admin cannot approve. Repeat a decision POST and verify no second history entry or notifications. Verify missing/invalid tokens and GET requests return 403.
6. Verify a pre-existing request still follows its prior workflow.

Production deployment and signed-in acceptance checks have not been performed in this task.
