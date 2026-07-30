-- =====================================================================
--  SIX VIEWS -> SQL SECURITY INVOKER
--
--  *** A SEPARATE CHANGE. NOT PART OF THE AUTOMATION BOM MODULE. ***
--  *** DO NOT RUN IN THE SAME WINDOW AS THE MODULE DEPLOYMENT.   ***
--
--  Two unrelated changes in one production window means an unexplained
--  symptom has two candidate causes, and someone backs out the wrong one.
--  Deploy and verify the module first. Then, on a different day, this.
--
--  WHY
--  ---
--  All six views carry DEFINER = `u537620103_shuser`@`127.0.0.1` with
--  SQL SECURITY DEFINER. That account exists on the current server, so
--  everything works there today. It does NOT exist anywhere else.
--
--  Restore this database to a new provider, a staging box or a developer
--  machine and all six views become unreadable — ERROR 1449 on a bare
--  SELECT — taking roughly 25 application files with them, silently,
--  until a page happens to touch one. `system_users_view` alone has 25
--  callers.
--
--  Demonstrated, not theorised: this exact failure occurred when the
--  production dump was loaded into a clean container during the module
--  build, and creating the missing account fixed all six at once.
--
--  A backup that only restores onto the machine it came from is thin
--  insurance against the one scenario backups exist for.
--
--  WHAT THIS CHANGES
--  -----------------
--  SQL SECURITY INVOKER evaluates permissions as the CALLING user rather
--  than a stored definer. All six views are simple reads over tables the
--  application's DB user already holds SELECT on, so there is no
--  practical loss of access — but it IS a semantic change: any future
--  least-privilege account would need SELECT on the underlying tables
--  directly, not just on the view.
--
--  Nothing else is altered. Column lists, joins and filters are
--  reproduced exactly as captured. The four passthrough views are left
--  as they are — retiring redundancy is a different, larger change.
--
--  ROLLBACK
--  --------
--  BOMMODULEDEVELOPMENT/abom_004_views_invoker_ROLLBACK.sql restores the
--  captured definitions verbatim, DEFINER intact. It was written BEFORE
--  this file.
--
--  RUNBOOK
--  -------
--   1. Confirm the module is deployed and verified, and that this is a
--      different window.
--   2. Take a fresh backup:
--        mysqldump -u USER -p --single-transaction --quick DBNAME > backup_before_views.sql
--      Check its size (tens of MB) and that the last line reads
--      "-- Dump completed".
--   3. Record the current state:
--        SELECT TABLE_NAME, DEFINER, SECURITY_TYPE
--          FROM information_schema.VIEWS WHERE TABLE_SCHEMA = DATABASE();
--      Expect six rows, all DEFINER, all @127.0.0.1.
--   4. Run this file.
--   5. Verify — see the two queries at the end. All six must read
--      INVOKER, and all six must return a row count without error.
--   6. Load three pages that use them: any page listing users (many use
--      system_users_view), a purchase-order screen (units_view,
--      poinstructions_view) and the spares service order report.
--      All must render with no error.
--   7. If anything is wrong, run the ROLLBACK file. It is immediate and
--      complete — views hold no data.
--
--  Views hold no data, so neither this change nor its rollback can lose
--  any. The risk is access, not loss.
-- =====================================================================
SET NAMES utf8mb4;

CREATE OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY INVOKER VIEW `dfmom_points_view` AS select `dfmom_points`.`id` AS `id`,`dfmom_points`.`df_id` AS `df_id`,`dfmom_points`.`record_id` AS `record_id`,`dfmom_points`.`mom_point` AS `mom_point`,`dfmom_points`.`added_on` AS `added_on`,`dfmom_points`.`added_by` AS `added_by` from `dfmom_points`
;
CREATE OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY INVOKER VIEW `module_capablity_view` AS select `module_capablity`.`id` AS `id`,`module_capablity`.`acessid` AS `acessid`,`module_capablity`.`role_id` AS `role_id`,`module_capablity`.`moduleid` AS `moduleid`,`module_capablity`.`submoduleid` AS `submoduleid`,`module_capablity`.`submodule_access` AS `submodule_access`,`module_capablity`.`madd` AS `madd`,`module_capablity`.`medit` AS `medit`,`module_capablity`.`mremove` AS `mremove`,`module_capablity`.`addedOn` AS `addedOn`,`module_capablity`.`upadtedOn` AS `upadtedOn`,`module_capablity`.`sms` AS `sms`,`module_capablity`.`email` AS `email`,`module_capablity`.`whatsapp` AS `whatsapp`,`module_capablity`.`frequency` AS `frequency` from `module_capablity`
;
CREATE OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY INVOKER VIEW `poinstructions_view` AS select `poinstructions`.`id` AS `id`,`poinstructions`.`script` AS `script`,`poinstructions`.`addedOn` AS `addedOn`,`poinstructions`.`addedBy` AS `addedBy` from `poinstructions`
;
CREATE OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY INVOKER VIEW `service_order_report` AS select `so`.`opportunity_id` AS `opportunity_id`,`so`.`op_no` AS `op_no`,`so`.`op_date` AS `op_date`,`so`.`op_type` AS `op_type`,ifnull(`cm_spares`.`company_name`,`cm_marketing`.`company_name`) AS `customer_name`,`u`.`first_name` AS `marketing_person`,`spo`.`po_number` AS `po_number`,`spo`.`po_date` AS `po_date`,`spo`.`po_amount` AS `po_amount`,`spo`.`po_attachment` AS `po_attachment`,`sq`.`currency` AS `currency`,`sq`.`total_basic_amount` AS `total_basic_amount`,`sq`.`grand_total` AS `quote_amount` from (((((`service_opportunities` `so` join `service_purchase_orders` `spo` on(`so`.`opportunity_id` = `spo`.`opportunity_id`)) left join `service_quotations` `sq` on(`so`.`opportunity_id` = `sq`.`opportunity_id`)) left join `spares_customers` `cm_spares` on(`so`.`customer_id` = `cm_spares`.`customer_id`)) left join `customer_detail` `cm_marketing` on(`so`.`customer_id` = `cm_marketing`.`id`)) left join `system_users` `u` on(`so`.`marketing_person_id` = `u`.`user_id`)) where `so`.`current_stage_id` = 7
;
CREATE OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY INVOKER VIEW `system_users_view` AS select `system_users`.`user_id` AS `user_id`,`system_users`.`title` AS `title`,`system_users`.`first_name` AS `first_name`,`system_users`.`last_name` AS `last_name`,`system_users`.`email` AS `email`,`system_users`.`password` AS `password`,`system_users`.`contact_number` AS `contact_number`,`system_users`.`alternate_number` AS `alternate_number`,`system_users`.`father_name` AS `father_name`,`system_users`.`mother_name` AS `mother_name`,`system_users`.`date_of_birth` AS `date_of_birth`,`system_users`.`date_of_joining` AS `date_of_joining`,`system_users`.`qualification` AS `qualification`,`system_users`.`aadharcard` AS `aadharcard`,`system_users`.`pancard` AS `pancard`,`system_users`.`profile_image` AS `profile_image`,`system_users`.`business_location` AS `business_location`,`system_users`.`department_id` AS `department_id`,`system_users`.`user_role_id` AS `user_role_id`,`system_users`.`address` AS `address`,`system_users`.`fms_process` AS `fms_process`,`system_users`.`user_status` AS `user_status`,`system_users`.`added_on` AS `added_on`,`system_users`.`added_by` AS `added_by`,`system_users`.`last_updated_on` AS `last_updated_on`,`system_users`.`hide_profile` AS `hide_profile`,`system_users`.`aadhar_number` AS `aadhar_number`,`system_users`.`blood_group` AS `blood_group`,`system_users`.`spouce_name` AS `spouce_name`,`system_users`.`spouce_company_name` AS `spouce_company_name`,`system_users`.`children_name` AS `children_name`,`system_users`.`employee_code` AS `employee_code`,`system_users`.`nominee_name` AS `nominee_name`,`system_users`.`date_of_leaving` AS `date_of_leaving`,`system_users`.`basic_salary` AS `basic_salary`,`system_users`.`gross_salary` AS `gross_salary`,`system_users`.`ctc` AS `ctc`,`system_users`.`uia_no` AS `uia_no`,`system_users`.`pf_no` AS `pf_no`,`system_users`.`bank_acc_detail` AS `bank_acc_detail`,`system_users`.`present_address` AS `present_address`,`system_users`.`joining_letter` AS `joining_letter`,`system_users`.`relieving_letter` AS `relieving_letter`,`system_users`.`marketing_person` AS `marketing_person`,`system_users`.`assign_delegation` AS `assign_delegation`,`system_users`.`salestarget` AS `salestarget`,`system_users`.`paymenttarget` AS `paymenttarget`,`system_users`.`assign_call_delegation` AS `assign_call_delegation`,`system_users`.`show_in_attendance` AS `show_in_attendance`,`system_users`.`employeecode` AS `employeecode`,`system_users`.`start_training` AS `start_training`,`system_users`.`salesforce_code` AS `salesforce_code`,`system_users`.`payment_grace_period` AS `payment_grace_period`,`system_users`.`convence` AS `convence`,`system_users`.`convence_type` AS `convence_type`,`system_users`.`convence_rate` AS `convence_rate`,`system_users`.`approvals` AS `approvals` from `system_users`
;
CREATE OR REPLACE ALGORITHM=UNDEFINED SQL SECURITY INVOKER VIEW `units_view` AS select `units`.`id` AS `id`,`units`.`name` AS `name`,`units`.`shortname` AS `shortname`,`units`.`addedBy` AS `addedBy`,`units`.`addedOn` AS `addedOn` from `units`
;

-- ---------------------------------------------------------------------
-- VERIFY 1: all six must now read INVOKER, and DEFINER must be gone as
-- a dependency.
-- ---------------------------------------------------------------------
SELECT TABLE_NAME, DEFINER, SECURITY_TYPE
  FROM information_schema.VIEWS
 WHERE TABLE_SCHEMA = DATABASE()
 ORDER BY TABLE_NAME;

-- ---------------------------------------------------------------------
-- VERIFY 2: every view must return a count without error. Before this
-- change, on any host lacking the definer account, each of these raised
-- ERROR 1449.
-- ---------------------------------------------------------------------
SELECT 'dfmom_points_view'     AS view_name, COUNT(*) AS rows_ FROM `dfmom_points_view`
UNION ALL SELECT 'module_capablity_view', COUNT(*) FROM `module_capablity_view`
UNION ALL SELECT 'poinstructions_view',   COUNT(*) FROM `poinstructions_view`
UNION ALL SELECT 'service_order_report',  COUNT(*) FROM `service_order_report`
UNION ALL SELECT 'system_users_view',     COUNT(*) FROM `system_users_view`
UNION ALL SELECT 'units_view',            COUNT(*) FROM `units_view`;
