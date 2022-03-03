/* 
By QuanNV
Ticket 358
*/
CREATE TABLE `company_view_defaults` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_view_id` int(10) NOT NULL,
  `company_id` int(10) NOT NULL,
  `progress_view` int(10) DEFAULT NULL,
  `oppor_view` int(10) DEFAULT NULL,
  `archived_view` int(10) DEFAULT NULL,
  `model_view` int(10) DEFAULT NULL,
  `default_mobile` int(10) DEFAULT NULL,
  `default_view` int(10) DEFAULT NULL,

  PRIMARY KEY (`id`),
  CONSTRAINT fk_user_view_id
  FOREIGN KEY (`user_view_id`)
  REFERENCES user_views (`id`)
)DEFAULT CHARSET=utf8;


DELETE FROM `company_configs`
WHERE cf_name = "default_project_view"
or cf_name = "default_inprogress_view"
or cf_name = "default_opportunity_view"
or cf_name = "default_archived_view"
or cf_name = "default_model_view"
or cf_name = "default_mobile_view"
;
