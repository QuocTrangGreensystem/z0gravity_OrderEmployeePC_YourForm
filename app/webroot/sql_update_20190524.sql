/* 
By QuanNV
Ticket 387.
*/
/*Tao 2 table luu comment, read status*/
CREATE TABLE `project_finance_plus_txts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `project_finance_plus_id` int(10) DEFAULT NULL,
  `project_id` int(10) NOT NULL,
  `employee_id` int(10) DEFAULT NULL,
  `comment` varchar(50) NOT NULL,
  `created` int(10) DEFAULT NULL,
  `updated` int(10) DEFAULT NULL,

  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8;

CREATE TABLE `project_finance_plus_txt_views` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `project_finance_plus_id` int(10) DEFAULT NULL,
  `employee_id` int(10) DEFAULT NULL,
  `read_status` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8;

/*Tao 2 table luu duong dan file attachment va read status*/
CREATE TABLE `project_finance_plus_attachments` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `project_finance_plus_id` int(10) DEFAULT NULL,
  `project_id` int(10) NOT NULL,
  `employee_id` int(10) DEFAULT NULL,
  `attachment` varchar(50) NOT NULL,
  `is_file` varchar(50) NOT NULL,
  `is_https` varchar(50) NOT NULL,
  `created` int(10) DEFAULT NULL,
  `updated` int(10) DEFAULT NULL,

  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8;

CREATE TABLE `project_finance_plus_attachment_views` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `project_finance_plus_id` int(10) DEFAULT NULL,
  `employee_id` int(10) DEFAULT NULL,
  `read_status` int(10) DEFAULT NULL,

  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8;