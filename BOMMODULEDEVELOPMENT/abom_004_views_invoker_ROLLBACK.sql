-- =====================================================================
--  ROLLBACK for abom_004_views_invoker.sql
--
--  Restores the six views to EXACTLY the definitions captured from the
--  live schema before that change was written. Verbatim SHOW CREATE VIEW
--  output — DEFINER and SQL SECURITY DEFINER intact.
--
--  This file was written BEFORE the forward change, deliberately: the
--  undo has to exist before the thing it undoes.
--
--  SEPARATE CHANGE. Nothing to do with the Automation BOM module.
--
--    mysql -u USER -p DBNAME < BOMMODULEDEVELOPMENT/abom_004_views_invoker_ROLLBACK.sql
--
--  Captured from: the production dump
--  (Database/u537620103_shubhampckpms.sql) loaded into a MariaDB 11.4
--  container. Verify against your live schema before relying on it:
--
--    SELECT TABLE_NAME, DEFINER, SECURITY_TYPE
--      FROM information_schema.VIEWS WHERE TABLE_SCHEMA = DATABASE();
-- =====================================================================
SET NAMES utf8mb4;

CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`u537620103_shuser`@`127.0.0.1` SQL SECURITY DEFINER VIEW `dfmom_points_view` AS select `dfmom_points`.`id` AS `id`,`dfmom_points`.`df_id` AS `df_id`,`dfmom_points`.`record_id` AS `record_id`,`dfmom_points`.`mom_point` AS `mom_point`,`dfmom_points`.`added_on` AS `added_on`,`dfmom_points`.`added_by` AS `added_by` from `dfmom_points`
;
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`u537620103_shuser`@`127.0.0.1` SQL SECURITY DEFINER VIEW `module_capablity_view` AS select `module_capablity`.`id` AS `id`,`module_capablity`.`acessid` AS `acessid`,`module_capablity`.`role_id` AS `role_id`,`module_capablity`.`moduleid` AS `moduleid`,`module_capablity`.`submoduleid` AS `submoduleid`,`module_capablity`.`submodule_access` AS `submodule_access`,`module_capablity`.`madd` AS `madd`,`module_capablity`.`medit` AS `medit`,`module_capablity`.`mremove` AS `mremove`,`module_capablity`.`addedOn` AS `addedOn`,`module_capablity`.`upadtedOn` AS `upadtedOn`,`module_capablity`.`sms` AS `sms`,`module_capablity`.`email` AS `email`,`module_capablity`.`whatsapp` AS `whatsapp`,`module_capablity`.`frequency` AS `frequency` from `module_capablity`
;
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`u537620103_shuser`@`127.0.0.1` SQL SECURITY DEFINER VIEW `poinstructions_view` AS select `poinstructions`.`id` AS `id`,`poinstructions`.`script` AS `script`,`poinstructions`.`addedOn` AS `addedOn`,`poinstructions`.`addedBy` AS `addedBy` from `poinstructions`
;
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`u537620103_shuser`@`127.0.0.1` SQL SECURITY DEFINER VIEW `service_order_report` AS select `so`.`opportunity_id` AS `opportunity_id`,`so`.`op_no` AS `op_no`,`so`.`op_date` AS `op_date`,`so`.`op_type` AS `op_type`,ifnull(`cm_spares`.`company_name`,`cm_marketing`.`company_name`) AS `customer_name`,`u`.`first_name` AS `marketing_person`,`spo`.`po_number` AS `po_number`,`spo`.`po_date` AS `po_date`,`spo`.`po_amount` AS `po_amount`,`spo`.`po_attachment` AS `po_attachment`,`sq`.`currency` AS `currency`,`sq`.`total_basic_amount` AS `total_basic_amount`,`sq`.`grand_total` AS `quote_amount` from (((((`service_opportunities` `so` join `service_purchase_orders` `spo` on(`so`.`opportunity_id` = `spo`.`opportunity_id`)) left join `service_quotations` `sq` on(`so`.`opportunity_id` = `sq`.`opportunity_id`)) left join `spares_customers` `cm_spares` on(`so`.`customer_id` = `cm_spares`.`customer_id`)) left join `customer_detail` `cm_marketing` on(`so`.`customer_id` = `cm_marketing`.`id`)) left join `system_users` `u` on(`so`.`marketing_person_id` = `u`.`user_id`)) where `so`.`current_stage_id` = 7
;
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`u537620103_shuser`@`127.0.0.1` SQL SECURITY DEFINER VIEW `system_users_view` AS select `system_users`.`user_id` AS `user_id`,`system_users`.`title` AS `title`,`system_users`.`first_name` AS `first_name`,`system_users`.`last_name` AS `last_name`,`system_users`.`email` AS `email`,`system_users`.`password` AS `password`,`system_users`.`contact_number` AS `contact_number`,`system_users`.`alternate_number` AS `alternate_number`,`system_users`.`father_name` AS `father_name`,`system_users`.`mother_name` AS `mother_name`,`system_users`.`date_of_birth` AS `date_of_birth`,`system_users`.`date_of_joining` AS `date_of_joining`,`system_users`.`qualification` AS `qualification`,`system_users`.`aadharcard` AS `aadharcard`,`system_users`.`pancard` AS `pancard`,`system_users`.`profile_image` AS `profile_image`,`system_users`.`business_location` AS `business_location`,`system_users`.`department_id` AS `department_id`,`system_users`.`user_role_id` AS `user_role_id`,`system_users`.`address` AS `address`,`system_users`.`fms_process` AS `fms_process`,`system_users`.`user_status` AS `user_status`,`system_users`.`added_on` AS `added_on`,`system_users`.`added_by` AS `added_by`,`system_users`.`last_updated_on` AS `last_updated_on`,`system_users`.`hide_profile` AS `hide_profile`,`system_users`.`aadhar_number` AS `aadhar_number`,`system_users`.`blood_group` AS `blood_group`,`system_users`.`spouce_name` AS `spouce_name`,`system_users`.`spouce_company_name` AS `spouce_company_name`,`system_users`.`children_name` AS `children_name`,`system_users`.`employee_code` AS `employee_code`,`system_users`.`nominee_name` AS `nominee_name`,`system_users`.`date_of_leaving` AS `date_of_leaving`,`system_users`.`basic_salary` AS `basic_salary`,`system_users`.`gross_salary` AS `gross_salary`,`system_users`.`ctc` AS `ctc`,`system_users`.`uia_no` AS `uia_no`,`system_users`.`pf_no` AS `pf_no`,`system_users`.`bank_acc_detail` AS `bank_acc_detail`,`system_users`.`present_address` AS `present_address`,`system_users`.`joining_letter` AS `joining_letter`,`system_users`.`relieving_letter` AS `relieving_letter`,`system_users`.`marketing_person` AS `marketing_person`,`system_users`.`assign_delegation` AS `assign_delegation`,`system_users`.`salestarget` AS `salestarget`,`system_users`.`paymenttarget` AS `paymenttarget`,`system_users`.`assign_call_delegation` AS `assign_call_delegation`,`system_users`.`show_in_attendance` AS `show_in_attendance`,`system_users`.`employeecode` AS `employeecode`,`system_users`.`start_training` AS `start_training`,`system_users`.`salesforce_code` AS `salesforce_code`,`system_users`.`payment_grace_period` AS `payment_grace_period`,`system_users`.`convence` AS `convence`,`system_users`.`convence_type` AS `convence_type`,`system_users`.`convence_rate` AS `convence_rate`,`system_users`.`approvals` AS `approvals` from `system_users`
;
CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`u537620103_shuser`@`127.0.0.1` SQL SECURITY DEFINER VIEW `units_view` AS select `units`.`id` AS `id`,`units`.`name` AS `name`,`units`.`shortname` AS `shortname`,`units`.`addedBy` AS `addedBy`,`units`.`addedOn` AS `addedOn` from `units`
;

-- Verify: all six back to DEFINER, and readable.
SELECT TABLE_NAME, DEFINER, SECURITY_TYPE
  FROM information_schema.VIEWS
 WHERE TABLE_SCHEMA = DATABASE()
 ORDER BY TABLE_NAME;
